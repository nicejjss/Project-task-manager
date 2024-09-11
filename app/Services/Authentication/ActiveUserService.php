<?php

namespace App\Services\Authentication;
use App\Models\User;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class ActiveUserService extends BaseService
{
    public function active($credentials): array|bool
    {
        $user = $this->repository->create($credentials);
        return [
            'active' => true,
            'token' => Auth::userToken($user),
        ];
    }
}
