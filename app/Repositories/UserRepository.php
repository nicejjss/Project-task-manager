<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{

    public function getModel(): string
    {
        return User::class;
    }

    public function getUser(array $data): mixed
    {
        return $this->model->where($data)->first();
    }

    public function getAuthUser() {
        return auth()->user();
    }
}
