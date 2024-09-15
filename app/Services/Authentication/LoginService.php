<?php

namespace App\Services\Authentication;
use Illuminate\Support\Facades\Auth;

class LoginService extends BaseService
{

    public function login($credentials): array|bool
    {
        if (Auth::attemptByCredentials($credentials)) {
            return true;
        }

        return false;
    }
}
