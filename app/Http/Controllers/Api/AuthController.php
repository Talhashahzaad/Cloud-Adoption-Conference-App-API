<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\Conference;
use App\Models\Registration;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Ticket;

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

        // 2. Conference Check
        $conference = Conference::where('id', $request->conference_id)
            ->where('status', 'active')
            ->first();

        if (!$conference) {
            return response()->json([
                'success' => false,
                'message' => 'Conference not found.'
            ], 404);
        }

        // 3.  Ticket Reference Check

        $ticket = Ticket::where('conference_id', $conference->id)
            ->where('ticket_reference', $request->ticket_reference)
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid ticket reference.'
            ], 422);
        }

        if ($ticket->status !== 'unused') {
            return response()->json([
                'success' => false,
                'message' => 'This ticket has already been used.'
            ], 422);
        }

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

        // 4. Registration Window Check
        $today = now()->toDateString();

        if (
            $today < $conference->registration_start_date ||
            $today > $conference->registration_end_date
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Conference registration is closed.'
            ], 422);
        }

        try {

            DB::beginTransaction();

            // 5. Create User
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

            $alreadyRegistered = Registration::where('conference_id', $conference->id)
                ->where('ticket_reference', $request->ticket_reference)
                ->exists();

            if ($alreadyRegistered) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket reference has already been used.'
                ], 422);
            }

            // 6. Create Registration
            Registration::create([
                'user_id' => $user->id,
                'conference_id' => $conference->id,
                'ticket_reference' => $request->ticket_reference,
                'ticket_source' => $request->ticket_source,
                'ticket_metadata' => null,
                'approval_status' => 'pending',
            ]);

            $ticket->update([
                'status' => 'used',
                'used_by_user_id' => $user->id,
                'used_at' => now(),
            ]);

            DB::commit();

            // 7. Verify Email
            $user->sendEmailVerificationNotification();

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
                'message' => 'Please verify your email first.'
            ], 403);
        }

        if ($user->status === 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is waiting for admin approval.'
            ], 403);
        }

        if ($user->status === 'rejected') {
            return response()->json([
                'success' => false,
                'message' => 'Your registration has been rejected.'
            ], 403);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data' => [
                'token' => $token,
                'user' => $user,
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
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ]);
    }
}
