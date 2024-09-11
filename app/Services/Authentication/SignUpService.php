<?php

namespace App\Services\Authentication;
use App\Mail\Authentication\ActiveMail;
use App\Models\User;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class SignUpService extends BaseService
{
    public function signUp(array $credentials): bool
    {
        $data = $this->format($credentials);
        $activeToken = Hash::make(implode('-', $data));
        Cache::put($activeToken, $data, ttl: 3600);
        Mail::to(data_get($data, 'email', ''))->send(new ActiveMail($activeToken));

        return true;
    }

    public function format(array $credentials): array
    {
        return [
            'name' =>explode('@', $credentials['email'])[0],
            'email' => $credentials['email'],
            'password' => md5($credentials['password']),
        ];
    }
}
