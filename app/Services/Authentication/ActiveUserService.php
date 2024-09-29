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

            $user  = $this->repository->getUser(['email' => $email]);

            if ($user) {
                $user->google_id = data_get($credentials, 'google_id');
                $user->access_token = data_get($credentials, 'access_token');
                $user->refresh_token = data_get($credentials, 'refresh_token');
                $user->save();
                return $user;
            }

            return $this->repository->create($credentials);
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
                'password' => md5(data_get($credentials, 'confirm_pass')),
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error($e);
            return false;
        }
    }
}
