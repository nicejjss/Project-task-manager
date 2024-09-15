<?php

use App\Http\Controllers\WEB\Authentication\ActiveController;
use App\Http\Controllers\WEB\Authentication\GoogleAuthenticationController;
use App\Http\Controllers\WEB\Authentication\LoginController;
use App\Http\Controllers\WEB\Authentication\ResetPasswordController;
use App\Http\Controllers\WEB\Authentication\SignUpController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

//Route::get('/', function () {
//    return redirect('/authentication/login');
//});

Route::prefix('authentication')->middleware('authenticationAlready')->group(function () {
    Route::get('/login', [LoginController::class, 'loginIndex'])->name('login');
    Route::get('/signup', [SignUpController::class, 'signUpIndex'])->name('signup');
    Route::get('/reset_password', [ResetPasswordController::class, 'resetPasswordIndex'])->name('reset_password');
    Route::get('/set_password', [ResetPasswordController::class, 'setPasswordIndex'])->name('set_password');
    Route::get('/google/callback', [GoogleAuthenticationController::class, 'callBack']);
    Route::get('/google/login', [GoogleAuthenticationController::class, 'redirect'])->name('google.login');

    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/signup', [SignUpController::class, 'signUp']);
    Route::get('/active_account', [ActiveController::class, 'active']);
    Route::post('/send_mail_reset', [ResetPasswordController::class, 'sendMail']);
    Route::post('/reset_password', [ResetPasswordController::class, 'resetPassword']);
});

Route::middleware(['authentication:web'])->group(function () {
   Route::get('/', function () {
       dd(session('user'));
   });
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

Route::get('/file', function () {
    dd(Storage::disk('google')->put('project/test.txt', 'Hello World'));
});


Route::get('/storage', function () {
    dd(Storage::disk('gcs')->get('file1.txt'));
});
