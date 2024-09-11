<?php

namespace App\Services\Authentication;
use App\Mail\Authentication\ResetPasswordMail;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ResetPasswordService extends BaseService
{
    public function sendMail($data): array|bool
    {
        $email = data_get($data, 'email');
        $activeToken = Hash::make($email);
        Cache::put($activeToken, $email, ttl: 3600);
        Mail::to(data_get($data, 'email', ''))->send(new ResetPasswordMail($activeToken));

        return true;
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
