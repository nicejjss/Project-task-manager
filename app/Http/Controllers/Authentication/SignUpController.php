<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\LoginRequest;
use App\Http\Requests\Authentication\SignUpRequest;
use App\Services\Authentication\LoginService;
use App\Services\Authentication\SignUpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class SignUpController extends Controller
{
    private SignUpService $service;

    public function __construct(SignUpService $service)
    {
        $this->service = $service;
    }

    public function signUp(SignUpRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $data = $this->service->signUp($credentials);

        if ($data) {
            return $this->success($data);
        }

        return $this->failed(data: $data, message: 'Wrong email or password', status: ResponseAlias::HTTP_UNAUTHORIZED);
    }

    public function signUpIndex()
    {
        return view('authentication.signup');
    }
}
