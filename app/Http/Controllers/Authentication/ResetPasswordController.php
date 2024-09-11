<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\ResetPasswordRequest;
use App\Services\Authentication\ResetPasswordService;

class ResetPasswordController extends Controller
{
    private ResetPasswordService $service;

    public function __construct(ResetPasswordService $service)
    {
        $this->service = $service;
    }

    public function sendMail(ResetPasswordRequest $request)
    {
        if ($data = $this->service->sendMail($request->validated())) {
            return $this->success($data);
        }

        return $this->failed($data);
    }

    public function reset_password(ResetPasswordRequest $request) {
        if ($data = $this->service->resetPassword($request->validated())) {
            return $this->success($data);
        }

        return $this->failed($data);
    }
}
