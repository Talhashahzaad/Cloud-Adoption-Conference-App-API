<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\Registration;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Http\Requests\ForgotPasswordRequest;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\ResetPasswordRequest;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        // 1. Find User Role
        $userRole = Role::where('name', 'User')
            ->where('status', 'active')
            ->first();

        if (!$userRole) {
            return response()->json([
                'success' => false,
                'message' => 'User role not found.'
            ], 500);
        }

        // 2. Find Ticket
        $ticket = Ticket::where('ticket_reference', $request->ticket_reference)
            ->first();
        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid ticket reference.'
            ], 422);
        }

        // 3. Get Conference From Ticket
        $conference = $ticket->conference;

        if (!$conference || $conference->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Conference not found or inactive.'
            ], 404);
        }

        // 4. Registration Window Check
        $today = now()->toDateString();

        if (
            $today < $conference->active_start_date ||
            $today > $conference->active_end_date
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Conference registration is closed.'
            ], 422);
        }

        // 5. Ticket Already Used?
        if ($ticket->status !== 'unused') {
            return response()->json([
                'success' => false,
                'message' => 'This ticket has already been used.'
            ], 422);
        }

        // 6. Email Validation
        if (
            strtolower(trim($ticket->ticket_holder_email))
            !==
            strtolower(trim($request->email))
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Email does not match the uploaded ticket.'
            ], 422);
        }

        // 7. Extra Safety Check
        $alreadyRegistered = Registration::where(
            'ticket_reference',
            $ticket->ticket_reference
        )->exists();

        if ($alreadyRegistered) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket reference has already been registered.'
            ], 422);
        }

        DB::beginTransaction();

        try {

            // Create User
            $user = User::create([
                'role_id' => $userRole->id,
                'name' => $request->name,
                'phone' => $request->phone,
                'company_name' => $request->company_name,
                'designation' => $request->designation,
                'city' => $request->city,
                'state' => $request->state,
                'area_of_interest' => $request->area_of_interest,
                'linkedin_url' => $request->linkedin_url,
                'remarks' => $request->remarks,
                'email' => $request->email,
                'password' => $request->password,
                'status' => 'pending',
            ]);

            // Create Registration
            Registration::create([
                'user_id' => $user->id,
                'conference_id' => $ticket->conference_id,
                'ticket_reference' => $ticket->ticket_reference,
                'ticket_source' => $ticket->ticket_source,
                'ticket_metadata' => null,
                'approval_status' => 'pending',
            ]);

            // Mark Ticket Used
            $ticket->update([
                'status' => 'used',
                'used_by_user_id' => $user->id,
                'used_at' => now(),
            ]);

            DB::commit();

            try {
                $user->sendEmailVerificationNotification();
            } catch (\Throwable $e) {
                \Log::error('Verification email failed', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Registration successful. Please verify your email. After verification your account will be reviewed by the administrator.'
            ], 201);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Registration failed.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.'
            ], 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.'
            ], 401);
        }

        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'code' => 'email_not_verified',
                'message' => 'Please verify your email first.'
            ], 403);
        }

        if ($user->status === 'pending') {
            return response()->json([
                'success' => false,
                'code' => 'pending_approval',
                'message' => 'Your account is waiting for admin approval.'
            ], 403);
        }

        if ($user->status === 'rejected') {
            $isStaff = in_array($user->role?->name, ['Admin', 'Super Admin'], true);

            return response()->json([
                'success' => false,
                'code' => $isStaff ? 'account_inactive' : 'registration_rejected',
                'message' => $isStaff
                    ? 'Your account has been deactivated. Please contact a super admin.'
                    : 'Your registration has been rejected.'
            ], 403);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data' => [
                'token' => $token,
                'user' => new UserResource(
                    $user->load('role', 'registrations.conference')
                ),
            ]
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load([
            'role',
            'registrations.conference',
        ]);

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ]);
    }
    public function forgotPassword(
        ForgotPasswordRequest $request
    ): JsonResponse {

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status !== Password::RESET_LINK_SENT) {

            return response()->json([
                'success' => false,
                'message' => __($status),
            ], 422);

        }

        return response()->json([
            'success' => true,
            'message' => 'Password reset link sent successfully.',
        ]);
    }

    public function resetPassword(
        ResetPasswordRequest $request
    ): JsonResponse {

        $status = Password::reset(

            $request->only(
                'email',
                'password',
                'password_confirmation',
                'token'
            ),

            function ($user, $password) {

                $user->forceFill([

                    'password' => Hash::make($password),

                ])->save();

            }

        );

        if ($status !== Password::PASSWORD_RESET) {

            return response()->json([
                'success' => false,
                'message' => __($status),
            ], 422);

        }

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully.',
        ]);
    }
    public function updateProfile(
        UpdateProfileRequest $request
    ): JsonResponse {

        $user = $request->user();

        $user->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => new UserResource(
                $user->fresh()->load(
                    'role',
                    'registrations.conference'
                )
            ),
        ]);
    }

    public function changePassword(
        ChangePasswordRequest $request
    ): JsonResponse {

        $user = $request->user();

        if (
            !Hash::check(
                $request->current_password,
                $user->password
            )
        ) {

            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.'
            ], 422);

        }

        $user->update([
            'password' => $request->password,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.',
        ]);
    }
}
