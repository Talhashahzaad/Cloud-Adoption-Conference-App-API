<?php

namespace App\Console\Commands;

use App\Jobs\SendNotificationJob;
use App\Models\Notification;
use Illuminate\Console\Command;

class SendScheduledNotifications extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:send-scheduled-notifications';

    /**
     * The console command description.
     */
    protected $description = 'Dispatch scheduled notifications for delivery';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $notifications = Notification::query()
            ->whereNotNull('scheduled_at')
            ->whereNull('sent_at')
            ->where('scheduled_at', '<=', now())
            ->get();

        foreach ($notifications as $notification) {

            SendNotificationJob::dispatch($notification);
        }

        $this->info(
            $notifications->count()
            . ' scheduled notification(s) dispatched.'
        );

        return self::SUCCESS;
    }
}
