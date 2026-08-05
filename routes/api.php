<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {

    $request->fulfill();

    return response()->json([
        'success' => true,
        'message' => 'Email verified successfully.'
    ]);

})->middleware(['auth:sanctum', 'signed'])->name('verification.verify');


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
