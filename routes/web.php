<?php

use App\Http\Controllers\Authentication\ActiveController;
use App\Http\Controllers\Authentication\LoginController;
use App\Http\Controllers\Authentication\ResetPasswordController;
use App\Http\Controllers\Authentication\SignUpController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication\GoogleAuthenticationController;

Route::get('/', function () {
    return redirect('/authentication/login');
});

Route::prefix('authentication')->group(function () {
    Route::get('/login', [LoginController::class, 'loginIndex'])->name('login');
    Route::get('/signup', [SignUpController::class, 'signUpIndex'])->name('signup');
    Route::get('/reset_password', [ResetPasswordController::class, 'resetPasswordIndex'])->name('reset_password');
    Route::get('/google/callback', [GoogleAuthenticationController::class, 'callBack']);
    Route::get('/google/login', [GoogleAuthenticationController::class, 'redirect'])->name('google.login');

    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/signup', [SignUpController::class, 'signUp']);
    Route::post('/active_account', [ActiveController::class, 'active']);
    Route::post('/send_mail_reset', [ResetPasswordController::class, 'sendMail']);
    Route::post('reset_password', [ResetPasswordController::class, 'resetPassword']);
});

Route::get('/user', function () {
   dd(session()->get('user'));
});

Route::get('/calendar', function () {
    $client = new Google_Client();
    $client->setAccessToken('');

    // Initialize the Google Calendar service
    $service = new Google_Service_Calendar($client);

    // Create the event
    $event = new Google_Service_Calendar_Event([
        'summary' => 'Meeting with client',
        'start' => [
            'dateTime' => Carbon::now()->addMinutes(2)->toRfc3339String(),
            'timeZone' => 'Asia/Ho_Chi_Minh',
        ],
        'end' => [
            'dateTime' => Carbon::now()->addMinutes(5)->toRfc3339String(),
            'timeZone' => 'Asia/Ho_Chi_Minh',
        ],
        'conferenceData' => new Google_Service_Calendar_ConferenceData([
            'createRequest' => new Google_Service_Calendar_CreateConferenceRequest([
                'requestId' => \Illuminate\Support\Str::random(20), // Unique request ID
                'conferenceSolutionKey' => [
                    'type' => 'hangoutsMeet', // Specifies Google Meet
                ],
            ]),
        ]),
        'attendees' => [
            ['email' => 'trenmy123@gmail.com'],
        ],
    ]);

    $event = $service->events->insert('primary', $event);

    dd($event);
});



Route::get('/event-view', function () {
    return view('newevent', ['key' => config('broadcasting.connections.pusher.key')]);
});

Route::get('/event-view-push', function () {
    $a = event(new \App\Events\NewEvent('Hello World'));
    dd($a);
});
