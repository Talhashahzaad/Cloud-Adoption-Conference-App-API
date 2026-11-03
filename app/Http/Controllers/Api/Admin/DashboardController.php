<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\Conference;
use App\Models\Registration;
use App\Models\Ticket;
use App\Models\User;
use App\Http\Resources\ConferenceResource;
use App\Http\Resources\RegistrationResource;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [

                'cards' => [

                    'total_conferences' => Conference::count(),

                    'active_conferences' => Conference::where('status', 'active')->count(),

                    'inactive_conferences' => Conference::where('status', 'inactive')->count(),

                    'total_registrations' => Registration::count(),

                    'approved_registrations' => Registration::where('approval_status', 'approved')->count(),

                    'pending_registrations' => Registration::where('approval_status', 'pending')->count(),

                    'rejected_registrations' => Registration::where('approval_status', 'rejected')->count(),

                    'total_tickets' => Ticket::count(),

                    'used_tickets' => Ticket::where('status', 'used')->count(),

                    'unused_tickets' => Ticket::where('status', 'unused')->count(),

                    'total_users' => User::count(),

                ],

                'recent_registrations' => RegistrationResource::collection(

                    Registration::with([
                        'user',
                        'conference',
                    ])
                        ->latest()
                        ->take(5)
                        ->get()

                ),

                'upcoming_conferences' => ConferenceResource::collection(

                    Conference::whereDate(
                        'conference_date',
                        '>=',
                        today()
                    )
                        ->orderBy('conference_date')
                        ->take(5)
                        ->get()

                ),

            ],
        ]);
    }
}