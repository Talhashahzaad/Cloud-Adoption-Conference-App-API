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
use App\Http\Controllers\Api\Admin\AttendeeController as AdminAttendeeController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\AdminUserController;
use App\Http\Controllers\Api\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Api\Admin\ExportController;
use App\Http\Controllers\Api\InfographicsController;
use App\Http\Controllers\Api\BookmarkController;
use App\Http\Controllers\Api\PrivacyController;
use App\Http\Controllers\Api\DeviceTokenController;
use App\Http\Controllers\Api\UserAgendaController;
use App\Http\Controllers\Api\UserAttendeeController;

/*
|--------------------------------------------------------------------------
| Public Authentication Routes
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::get('/reset-password/{token}', function (Request $request, string $token) {
    return response()->json([
        'token' => $token,
        'email' => $request->email,
    ]);
})->name('password.reset');
Route::post(
    'reset-password',
    [AuthController::class, 'resetPassword']
);
Route::get(
    '/email/verify/{id}/{hash}',
    [EmailVerificationController::class, 'verify']
)->middleware('signed')->name('verification.verify');
Route::post(
    '/resend-verification',
    [EmailVerificationController::class, 'resend']
)->middleware('throttle:6,1')->name('verification.resend');


/*
|--------------------------------------------------------------------------
| Public Conference Routes
|--------------------------------------------------------------------------
*/

Route::get('/conferences', [ConferenceController::class, 'index']);
Route::get('/conferences/{conference}', [ConferenceController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Data Privacy
|--------------------------------------------------------------------------
*/

Route::get(
    '/privacy',
    [PrivacyController::class, 'index']
)->name('privacy');


/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/email/verification-notification', function (Request $request) {

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => 'Verification email sent.'
        ]);

    })->middleware('throttle:6,1');

    /*
    |--------------------------------------------------------------------------
    | Infographics
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/infographics',
        [InfographicsController::class, 'index']
    );

    /*
   |--------------------------------------------------------------------------
   | Attendee Bookmarks
   |--------------------------------------------------------------------------
   */

    Route::get(
        '/bookmarks',
        [BookmarkController::class, 'index']
    )->name('bookmarks.index');

    Route::post(
        '/bookmarks/{registration}',
        [BookmarkController::class, 'store']
    )->name('bookmarks.store');

    Route::delete(
        '/bookmarks/{registration}',
        [BookmarkController::class, 'destroy']
    )->name('bookmarks.destroy');

    /*
    |--------------------------------------------------------------------------
    | Device Token
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/device-token',
        [DeviceTokenController::class, 'store']
    )->name('device-token.store');

    Route::delete(
        '/device-token',
        [DeviceTokenController::class, 'destroy']
    )->name('device-token.destroy');

    /*
   |--------------------------------------------------------------------------
   | User Agenda
   |--------------------------------------------------------------------------
   */

    Route::get(
        '/my-conference/agenda',
        [UserAgendaController::class, 'index']
    );


    /*
      |--------------------------------------------------------------------------
      | User Conference Attendees
      |--------------------------------------------------------------------------
      */


    Route::get(
        '/my-conference/attendees',
        [UserAttendeeController::class, 'index']
    )->name('my-conference.attendees');

    Route::get(
        '/my-conference/attendees/{registration}',
        [UserAttendeeController::class, 'show']
    )->name('my-conference.attendees.show');

});


Route::prefix('admin')
    ->name('admin.')
    ->middleware([
        'auth:sanctum',
        'role:Super Admin,Admin'
    ])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get(
            'dashboard',
            [AdminDashboardController::class, 'index']
        )->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Conference Management
        |--------------------------------------------------------------------------
        */

        Route::apiResource(
            'conferences',
            AdminConferenceController::class
        );

        Route::patch(
            'conferences/{conference}/status',
            [AdminConferenceController::class, 'updateStatus']
        )->name('conferences.status');

        /*
        |--------------------------------------------------------------------------
        | Conference Agendas
        |--------------------------------------------------------------------------
        */

        Route::apiResource(
            'conferences.agendas',
            AdminAgendaController::class
        );

        /*
        |--------------------------------------------------------------------------
        | Registration Management
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Ticket Management
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Attendee Management
        |--------------------------------------------------------------------------
        */

        Route::get(
            'conferences/{conference}/attendees',
            [AdminAttendeeController::class, 'index']
        )->name('conferences.attendees');

        Route::get(
            'attendees/{registration}',
            [AdminAttendeeController::class, 'show']
        )->name('attendees.show');

        /*
        |--------------------------------------------------------------------------
        | Push Notifications
        |--------------------------------------------------------------------------
        */
        Route::apiResource(
            'notifications',
            AdminNotificationController::class
        );
        Route::post(
            'notifications/{notification}/send',
            [AdminNotificationController::class, 'send']
        )->name('notifications.send');

        /*
        |--------------------------------------------------------------------------
        | Reports & Export
        |--------------------------------------------------------------------------
        */

        Route::get(
            'reports/attendees/export',
            [ExportController::class, 'attendeeExport']
        )->name('reports.attendees.export');

        /*
        |--------------------------------------------------------------------------
        | Settings
        |--------------------------------------------------------------------------
        */
        // Routes will be added here



    });

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware([
        'auth:sanctum',
        'role:Super Admin'
    ])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Admin Management
        |--------------------------------------------------------------------------
        */

        Route::apiResource(
            'admins',
            AdminUserController::class
        );

        Route::patch(
            'admins/{admin}/status',
            [AdminUserController::class, 'updateStatus']
        )->name('admins.status');

        /*
       |--------------------------------------------------------------------------
       | Advanced / System Settings
       |--------------------------------------------------------------------------
       */

        // Future Super Admin only routes

    });
