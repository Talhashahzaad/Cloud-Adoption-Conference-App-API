<?php

namespace App\Jobs;

use App\Models\DeviceToken;
use App\Models\Notification;
use App\Services\FirebaseNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendNotificationJob implements ShouldQueue
{
    use Queueable;

    public Notification $notification;

    /**
     * Create a new job instance.
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Execute the job.
     */
    public function handle(
        FirebaseNotificationService $firebaseNotificationService
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Get device tokens for users registered for this conference
        |--------------------------------------------------------------------------
        */

        $tokens = DeviceToken::query()
            ->whereHas('user.registrations', function ($query) {
                $query
                    ->where(
                        'conference_id',
                        $this->notification->conference_id
                    )
                    ->where(
                        'approval_status',
                        'approved'
                    );
            })
            ->whereHas('user', function ($query) {
                $query->where(
                    'push_notifications_enabled',
                    true
                );
            })
            ->pluck('token')
            ->unique()
            ->values()
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | No eligible devices
        |--------------------------------------------------------------------------
        */

        if (empty($tokens)) {

            Log::info(
                'No eligible device tokens found for notification.',
                [
                    'notification_id' => $this->notification->id,
                ]
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Send notification through Firebase service
        |--------------------------------------------------------------------------
        */

        $result = $firebaseNotificationService->send(
            $this->notification,
            $tokens
        );


        /*
        |--------------------------------------------------------------------------
        | Mark notification as sent
        |--------------------------------------------------------------------------
        */

        if ($result['success'] && $result['sent'] > 0) {

            $this->notification->update([
                'sent_at' => now(),
            ]);
        }
    }
}
