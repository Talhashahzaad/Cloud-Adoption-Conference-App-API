<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ConferenceController;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\EmailVerificationController;

Route::post('/register', [AuthController::class, 'register']);
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware('signed')
    ->name('verification.verify');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request): JsonResponse {

    $request->fulfill();

    return response()->json([
        'success' => true,
        'message' => 'Email verified successfully. Your account is now waiting for admin approval.'
    ]);

})->middleware(['signed'])->name('verification.verify');


Route::post('/email/verification-notification', function (Request $request) {

    $request->user()->sendEmailVerificationNotification();

    return response()->json([
        'success' => true,
        'message' => 'Verification email sent.'
    ]);

})->middleware(['auth:sanctum', 'throttle:6,1']);

/**Protected Routes */
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

});

Route::prefix('admin')
    ->middleware('auth:sanctum')
    ->group(function () {

        Route::apiResource('conferences', ConferenceController::class);

        Route::patch(
            'conferences/{conference}/status',
            [ConferenceController::class, 'updateStatus']
        );

    });

Route::get('/conferences', [ConferenceController::class, 'index']);
Route::get('/conferences/{conference}', [ConferenceController::class, 'show']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/conferences', [ConferenceController::class, 'store']);
    Route::put('/conferences/{conference}', [ConferenceController::class, 'update']);
    Route::delete('/conferences/{conference}', [ConferenceController::class, 'destroy']);
});