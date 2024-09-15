<?php

namespace App\Http\Controllers\API\Authentication;

use App\Http\Controllers\API\BaseController as Controller;
use App\Http\Requests\Authentication\ActiveRequest;
use App\Services\Authentication\ActiveUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
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
            return $this->success([
                'active' => true,
                'token' => Auth::userToken($data),
            ]);
        }

        return $this->failed(data: $data, message: 'Wrong email or password', status: ResponseAlias::HTTP_UNAUTHORIZED);
    }
}
