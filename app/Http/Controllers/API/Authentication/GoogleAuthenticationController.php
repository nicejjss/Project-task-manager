<?php

namespace App\Http\Controllers\API\Authentication;

use App\Http\Controllers\API\BaseController as Controller;
use App\Services\Authentication\SignUpService;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthenticationController extends Controller
{
    private SignUpService $signUpService;

    public function __construct(SignUpService $signUpService)
    {
        $this->signUpService = $signUpService;
    }

    public function callBack()
    {
        $user = Socialite::driver('google')->user();
        dd($user, $user->getId());
    }

    public function redirect()
    {
        return Socialite::driver('google')->with(['access_type' => 'offline'])
            ->scopes(['https://www.googleapis.com/auth/calendar.events'])
            ->redirect();
    }
}
