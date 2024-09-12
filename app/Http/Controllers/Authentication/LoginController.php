<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\LoginRequest;
use App\Services\Authentication\LoginService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class LoginController extends Controller
{
    private LoginService $service;

    public function __construct(LoginService $service)
    {
        $this->service = $service;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $data = $this->service->login($credentials);

        if ($data) {
            return $this->success($data);
        }

        return $this->failed(data: $data, message: 'Wrong email or password', status: ResponseAlias::HTTP_UNAUTHORIZED);
    }

    public function loginIndex()
    {
        return view('authentication.login');
    }
}
