<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/oauth/google/callback', function () {
    $user = Socialite::driver('google')->user();
    dd($user, $user->getId());
});

Route::get('/oauth/google/login', function () {
    return Socialite::driver('google')->with(['access_type' => 'offline'])
        ->scopes(['https://www.googleapis.com/auth/calendar.events'])
        ->redirect();
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
