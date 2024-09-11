<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\ActiveRequest;
use App\Http\Requests\Authentication\LoginRequest;
use App\Services\Authentication\ActiveUserService;
use App\Services\Authentication\LoginService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ActiveController extends Controller
{
    private ActiveUserService $service;

    public function __construct(ActiveUserService $service)
    {
        $this->service = $service;
    }

    public function active(ActiveRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $data = $this->service->active($credentials);

        if ($data) {
            return $this->success($data);
        }

        return $this->failed(data: $data, message: 'Wrong email or password', status: ResponseAlias::HTTP_UNAUTHORIZED);
    }
}
