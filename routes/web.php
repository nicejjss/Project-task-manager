<?php

use App\Http\Controllers\WEB\Authentication\ActiveController;
use App\Http\Controllers\WEB\Authentication\GoogleAuthenticationController;
use App\Http\Controllers\WEB\Authentication\LoginController;
use App\Http\Controllers\WEB\Authentication\ResetPasswordController;
use App\Http\Controllers\WEB\Authentication\SignUpController;
use App\Http\Controllers\WEB\HomeController;
use App\Http\Controllers\WEB\ProjectController;
use App\Http\Controllers\WEB\TaskController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

//Route::get('/', function () {
//    return redirect('/authentication/login');
//});

Route::prefix('authentication')->group(function () {
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::middleware('authenticationAlready')->group(function () {
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
});

Route::middleware(['authentication:web'])->group(function () {
   Route::get('/', [HomeController::class, 'index']);

   Route::prefix('project') ->group(function () {
       Route::get('/create', [ProjectController::class, 'createIndex'])->name('project.create');
       Route::post('/store', [ProjectController::class, 'store']);
       Route::get('/invite/{projectID}', [ProjectController::class, 'invite']);

       Route::get('/{projectID}', [ProjectController::class, 'index']);
       Route::post('/{projectID}/add', [ProjectController::class, 'addMember']);

       Route::get('/{projectID}/edit', [ProjectController::class, 'editView']);
       Route::post('/{projectID}/edit', [ProjectController::class, 'edit']);

       Route::get('/{projectID}/task/create', [TaskController::class, 'taskCreateView'])
;//       Route::get('/edit/{id}', function ($id) {
//           return view('project_edit')->with('id', $id);
//       });
//       Route::get('/view/{id}', function ($id) {
//           return view('project_view')->with('id', $id);
//       });
//       Route::get('/delete/{id}', function ($id) {
//           Storage::delete('project/'. $id. '.json');
//           return redirect()->route('project.list');
//       });
   });
//    Route::get('/project', function () {
//        ->with(['user' => session()->get('user'), 'key' => config('broadcasting.connections.pusher.key')]);
//    });
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
    dd(event(new \App\Events\NewEvent('Hello World')));
});

Route::get('/file', function () {
    dd(Storage::disk('google')->put('test.txt', 'Hello World'));
});


Route::get('/storage', function () {
    dd(Storage::disk('gcs')->url('project/project_17.md'));
});
