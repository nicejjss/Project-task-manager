<?php

namespace App\Services\Authentication;
use App\Models\User;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class LoginService extends BaseService
{

    public function login($credentials): array|bool
    {
        if (Auth::attemptByCredentials($credentials)) {
            session()->put('user', Auth::user()->toArray());
            return [
                'token' => Auth::userToken(),
            ];
        }

        return false;
    }
}
