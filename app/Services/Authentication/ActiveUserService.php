<?php

namespace App\Services\Authentication;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActiveUserService extends BaseService
{
    public function active($credentials)
    {
        try {
            $email = data_get($credentials, 'email');
            return $this->repository->updateOrCreate(['email' => $email], $credentials);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public function updatePassword($credentials) {
        try {
            $email = data_get($credentials, 'email');
            $user = Auth::attemptByCredentials(['email' => $email]);
            $user->update([
                'password' => md5(data_get($credentials, 'new_password')),
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error($e);
            return false;
        }
    }
}
