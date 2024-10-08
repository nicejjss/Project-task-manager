<?php

namespace App\Services\Authentication;
use App\Mail\Authentication\ActiveMail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SignUpService extends BaseService
{
    public function signUp(array $credentials): bool
    {
        try {
            $data = $this->format($credentials);
            $activeToken = Hash::make(implode('-', $data));
            Cache::put($activeToken, $data, ttl: 3600);
            Mail::to(data_get($data, 'email', ''))->send(new ActiveMail($activeToken));

            return true;
        } catch (\Exception $exception) {
            Log::error($exception);
            return false;
        }
    }

    public function format(array $credentials): array
    {
        return [
            'name' =>explode('@', $credentials['email'])[0],
            'email' => $credentials['email'],
            'password' => md5(data_get($credentials, 'password')),
            'avatar' => data_get($credentials, 'avatar'),
            'google_id' => data_get($credentials, 'id'),
            'access_token' => data_get($credentials, 'token'),
            'refresh_token' => data_get($credentials, 'refreshToken'),
        ];
    }
}
