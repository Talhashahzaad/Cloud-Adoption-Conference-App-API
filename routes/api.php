<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConferenceController;
use App\Http\Controllers\Api\Admin\ConferenceController as AdminConferenceController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\Admin\AgendaController as AdminAgendaController;
use App\Http\Controllers\Api\Admin\RegistrationController as AdminRegistrationController;
use App\Http\Controllers\Api\Admin\TicketController as AdminTicketController;

/*
|--------------------------------------------------------------------------
| Public Authentication Routes
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get(
    '/email/verify/{id}/{hash}',
    [EmailVerificationController::class, 'verify']
)->middleware('signed')->name('verification.verify');


/*
|--------------------------------------------------------------------------
| Public Conference Routes
|--------------------------------------------------------------------------
*/

Route::get('/conferences', [ConferenceController::class, 'index']);
Route::get('/conferences/{conference}', [ConferenceController::class, 'show']);


/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/email/verification-notification', function (Request $request) {

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => 'Verification email sent.'
        ]);

    })->middleware('throttle:6,1');

});


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth:sanctum')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Conference Management
        |--------------------------------------------------------------------------
        */

        Route::apiResource('conferences', AdminConferenceController::class);

        Route::patch(
            'conferences/{conference}/status',
            [AdminConferenceController::class, 'updateStatus']
        )->name('conferences.status');

        /*
        |--------------------------------------------------------------------------
        | Agendas Management
        |--------------------------------------------------------------------------
        */

        Route::apiResource(
            'conferences.agendas',
            AdminAgendaController::class
        );

        Route::apiResource(
            'registrations',
            AdminRegistrationController::class
        )->only([
                    'index',
                    'show'
                ]);

        Route::patch(
            'registrations/{registration}/approval',
            [AdminRegistrationController::class, 'updateApprovalStatus']
        )->name('registrations.approval');

        Route::prefix('tickets')
            ->name('tickets.')
            ->group(function () {

                Route::get(
                    '/',
                    [AdminTicketController::class, 'index']
                )->name('index');

                Route::post(
                    '/import',
                    [AdminTicketController::class, 'import']
                )->name('import');

                Route::get(
                    '/template',
                    [AdminTicketController::class, 'downloadTemplate']
                )->name('template');

            });
    });