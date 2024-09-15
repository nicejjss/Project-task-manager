<?php

namespace App\Http\Controllers\API\Authentication;

use App\Http\Controllers\API\BaseController as Controller;
use App\Http\Requests\Authentication\LoginRequest;
use App\Services\Authentication\LoginService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
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
            return $this->success([
                'token' => Auth::userToken(),
            ]);
        }

        return $this->failed(data: $data, message: 'Wrong email or password', status: ResponseAlias::HTTP_UNAUTHORIZED);
    }

    public function loginIndex()
    {
        return view('authentication.login');
    }
}
