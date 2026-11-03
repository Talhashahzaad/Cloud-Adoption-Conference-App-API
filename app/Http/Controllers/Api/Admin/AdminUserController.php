<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Http\Requests\UpdateAdminStatusRequest;
use App\Http\Resources\AdminUserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $adminRole = Role::where('name', 'Admin')->first();

        $query = User::with('role')
            ->where('role_id', $adminRole->id);

        if ($request->filled('search')) {

            $query->where(function ($q) use ($request) {

                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');

            });

        }

        $admins = $query
            ->latest()
            ->paginate($request->integer('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => AdminUserResource::collection($admins),
            'meta' => [
                'current_page' => $admins->currentPage(),
                'last_page' => $admins->lastPage(),
                'per_page' => $admins->perPage(),
                'total' => $admins->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreAdminRequest $request
    ): JsonResponse {

        DB::beginTransaction();

        try {

            $adminRole = Role::where(
                'name',
                'Admin'
            )->firstOrFail();

            $admin = User::create([

                'role_id' => $adminRole->id,

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

                'password' => Hash::make($request->password),

                'status' => 'approved',

                'email_verified_at' => now(),

            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Admin created successfully.',
                'data' => new AdminUserResource(
                    $admin->load('role')
                ),
            ], 201);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Admin creation failed.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null,
            ], 500);

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $admin): JsonResponse
    {
        $this->ensureAdmin($admin);

        $admin->load('role');

        return response()->json([
            'success' => true,
            'data' => new AdminUserResource($admin),
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateAdminRequest $request,
        User $admin
    ): JsonResponse {
        DB::beginTransaction();

        try {

            $this->ensureAdmin($admin);

            $admin->update([

                'name' => $request->name,

                'email' => $request->email,

                'phone' => $request->phone,

                'company_name' => $request->company_name,

                'designation' => $request->designation,

                'city' => $request->city,

                'state' => $request->state,

                'area_of_interest' => $request->area_of_interest,

                'linkedin_url' => $request->linkedin_url,

                'remarks' => $request->remarks,

            ]);

            if ($request->filled('password')) {

                $admin->update([
                    'password' => Hash::make($request->password),
                ]);

            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Admin updated successfully.',
                'data' => new AdminUserResource(
                    $admin->fresh()->load('role')
                ),
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Admin update failed.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null,
            ], 500);

        }


    }

    public function updateStatus(
        UpdateAdminStatusRequest $request,
        User $admin
    ): JsonResponse {

        $this->ensureAdmin($admin);

        $admin->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Admin status updated successfully.',
            'data' => new AdminUserResource(
                $admin->fresh()->load('role')
            ),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        User $admin
    ): JsonResponse {

        $this->ensureAdmin($admin);

        DB::beginTransaction();

        try {

            if ($admin->id === auth()->id()) {

                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete your own account.',
                ], 422);

            }

            $admin->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Admin deleted successfully.',
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Admin deletion failed.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null,
            ], 500);

        }
    }

    private function ensureAdmin(User $admin): void
    {
        $adminRole = Role::where('name', 'Admin')->first();

        abort_if(
            $admin->role_id !== $adminRole->id,
            404,
            'Admin not found.'
        );
    }
}