<?php

namespace App\Services\Authentication;
use App\Repositories\UserRepository;

class BaseService
{
    protected UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }
}
