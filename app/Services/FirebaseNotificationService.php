<?php

namespace App\Services;

use App\Models\Notification;

class FirebaseNotificationService
{
    /**
     * Send a notification to the given device tokens.
     *
     * Firebase integration will be added here once
     * the client's Firebase credentials are available.
     */
    public function send(
        Notification $notification,
        array $tokens
    ): array {
        /*
        |--------------------------------------------------------------------------
        | Firebase FCM sending logic
        |--------------------------------------------------------------------------
        |
        | This is where Firebase Cloud Messaging will be called.
        |
        */

        return [
            'success' => false,
            'message' => 'Firebase notification service is not configured yet.',
            'sent' => 0,
            'failed' => count($tokens),
        ];
    }
}
