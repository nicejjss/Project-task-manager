<?php

namespace App\Services\Authentication;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

            $avatarLink = data_get($credentials, 'avatar');
            $credentials['avatar'] = null;
            $user = $this->repository->create($credentials);
            $extPath = pathinfo($avatarLink, PATHINFO_EXTENSION);
            $ext = !empty($extPath) ? $extPath : 'png';
            $fileName = 'avatar_' . $user->id . '_' . Str::random(10);
            $path = $fileName . '.' . $ext;

            Storage::disk('gcs')->put('user/' . $path, file_get_contents($avatarLink), 'public');
            $user->avatar = 'user/' . $path;
            $user->save();

            return $user;
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
