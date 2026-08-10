<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveRegistrationRequest;
use App\Http\Resources\RegistrationResource;
use App\Models\Registration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    /**
     * Registration List
     */
    public function index(Request $request): JsonResponse
    {
        $query = Registration::with([
            'user',
            'conference',
            'approver',
        ]);

        if ($request->filled('status')) {
            $query->where('approval_status', $request->status);
        }

        $registrations = $query
            ->latest()
            ->paginate($request->integer('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => RegistrationResource::collection($registrations),
            'meta' => [
                'current_page' => $registrations->currentPage(),
                'last_page' => $registrations->lastPage(),
                'per_page' => $registrations->perPage(),
                'total' => $registrations->total(),
            ],
        ]);
    }

    /**
     * Registration Details
     */
    public function show(Registration $registration): JsonResponse
    {
        $registration->load([
            'user',
            'conference',
            'approver',
        ]);

        return response()->json([
            'success' => true,
            'data' => new RegistrationResource($registration),
        ]);
    }

    /**
     * Approve / Reject Registration
     */
    public function updateApprovalStatus(
        ApproveRegistrationRequest $request,
        Registration $registration
    ): JsonResponse {

        DB::transaction(function () use ($request, $registration) {

            $registration->update([
                'approval_status' => $request->approval_status,
                'approval_remarks' => $request->approval_remarks,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $registration->user()->update([
                'status' => $request->approval_status,
            ]);

        });

        $registration->load([
            'user',
            'conference',
            'approver',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registration status updated successfully.',
            'data' => new RegistrationResource($registration),
        ]);
    }
}
