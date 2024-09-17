<?php

namespace App\Http\Controllers\WEB\Authentication;

use App\Http\Controllers\WEB\BaseController as Controller;
use App\Services\Authentication\ActiveUserService;
use App\Services\Authentication\SignUpService;
use Illuminate\Support\Facades\Cache;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthenticationController extends Controller
{
    private SignUpService $signUpService;
    private ActiveUserService $activeUserService;

    public function __construct(SignUpService $signUpService, ActiveUserService $activeUserService)
    {
        $this->signUpService = $signUpService;
        $this->activeUserService = $activeUserService;
    }

    public function callBack()
    {
        $user = Socialite::driver('google')->user();
        $user = $this->signUpService->format((array)$user);
        if ($user = $this->activeUserService->active($user)) {
            session(['user'=> $user->toArray()]);
            return redirect('/');
        }

        return redirect('/authentication/login')->withErrors(['authentication' => 'Email đã tồn tại']);
    }

    public function redirect(string $path = null)
    {
        if ($path) {
            session(['path' => $path]);
        }
        return Socialite::driver('google')->with(['access_type' => 'offline'])
            ->scopes(['https://www.googleapis.com/auth/calendar.events'])
            ->redirect();
    }
}
