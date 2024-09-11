<?php

namespace App\Custom\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Database\Eloquent\Model;

class CustomProvider implements UserProvider
{
    private Model|string $model;

    public function __construct(string $model)
    {
        $this->model = $model;
        $this->createModel();
    }

    private function createModel()
    {
        $class = '\\'.ltrim($this->model, '\\');

        return $this->model = new $class;
    }

    public function retrieveById($identifier)
    {
         return $this->model->where('id', $identifier)->first();
    }

    public function existEmail($identifier)
    {
        return $this->model->where('email', $identifier)->exists();
    }

    public function retrieveByToken($identifier, $token)
    {
        // TODO: Implement retrieveByToken() method.
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        // TODO: Implement updateRememberToken() method.
    }

    public function retrieveByCredentials(array $credentials)
    {
        return $this->model->where($credentials)->first();
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        // TODO: Implement validateCredentials() method.
    }

    public function rehashPasswordIfRequired(Authenticatable $user, #[\SensitiveParameter] array $credentials, bool $force = false)
    {
        // TODO: Implement rehashPasswordIfRequired() method.
    }
}
