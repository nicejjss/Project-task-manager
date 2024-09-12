<?php

use App\Http\Controllers\Authentication\ActiveController;
use App\Http\Controllers\Authentication\LoginController;
use App\Http\Controllers\Authentication\ResetPasswordController;
use App\Http\Controllers\Authentication\SignUpController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;


Route::get('/login', function () {
    return view('authentication.login');
})->name('login');

Route::get('/signup', function () {
    return view('authentication.signup');
})->name('signup');

Route::get('/forget_password', function () {
    return view('authentication.forget_password');
})->name('forget_password');

Route::post('/login', [LoginController::class, 'login']);
Route::post('/signup', [SignUpController::class, 'signUp']);
Route::post('/active_account', [ActiveController::class, 'active']);
Route::post('/send_mail_reset', [ResetPasswordController::class, 'sendMail']);
Route::post('reset_password', [ResetPasswordController::class, 'reset_password']);

Route::get('/', function () {
    return view('welcome');
});

Route::get('/oauth/google/callback', function (Request $request) {
    $user = Socialite::driver('google')->user();
    dd($user, $user->getId());
});

Route::get('/oauth/google/login', function () {
    return Socialite::driver('google')->with(['access_type' => 'offline'])
        ->scopes(['https://www.googleapis.com/auth/calendar.events'])
        ->redirect();
})->name('google.login');

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
