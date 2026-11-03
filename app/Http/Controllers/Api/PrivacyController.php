<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class PrivacyController extends Controller
{
    /**
     * Data Privacy Disclaimer
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'title' => 'Data Privacy Disclaimer',

                'content' => 'Your personal information will be used only for conference registration, communication, and related event purposes. Your information will be handled securely and will not be shared with unauthorized third parties.'
            ],
        ]);
    }
}
