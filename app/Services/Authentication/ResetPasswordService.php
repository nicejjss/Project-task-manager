<?php

namespace App\Services\Authentication;
use App\Jobs\ResetPasswordJob;
use App\Mail\Authentication\ResetPasswordMail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ResetPasswordService extends BaseService
{
    public function sendMail($data): array|bool
    {
        try {
            $email = data_get($data, 'email');
            $activeToken = Hash::make($email);
            Cache::put($activeToken, $email, ttl: 3600);
            ResetPasswordJob::dispatch(data_get($data, 'email', ''), $activeToken);

            return true;
        } catch (\Exception $exception) {
            Log::error($exception);
            return false;
        }

    }

    public function resetPassword(mixed $validated): bool
    {
        $email = data_get($validated, 'email');
        $new_password = data_get($validated, 'new_password');

        if ($email) {
           $user  = $this->repository->getUser(['email' => $email]);
           return $user->update([
               'password' => md5($new_password),
           ]);
        }

        return false;
    }
}
