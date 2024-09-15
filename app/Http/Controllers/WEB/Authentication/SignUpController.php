<?php

namespace App\Http\Controllers\WEB\Authentication;

use App\Http\Controllers\WEB\BaseController as Controller;
use App\Http\Requests\Authentication\SignUpRequest;
use App\Services\Authentication\SignUpService;

class SignUpController extends Controller
{
    private SignUpService $service;

    public function __construct(SignUpService $service)
    {
        $this->service = $service;
    }

    public function signUp(SignUpRequest $request)
    {
        $credentials = $request->validated();
        $data = $this->service->signUp($credentials);

        if ($data) {
            return redirect()->back()->with(['success' => 'Đã gửi mail đăng ký cho bạn']);
        }

        return redirect()->back()->with(['error' => 'Đăng ký thất bại']);
    }

    public function signUpIndex()
    {
        return view('authentication.signup');
    }
}
