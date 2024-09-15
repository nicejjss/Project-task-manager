<?php

namespace App\Http\Controllers\WEB\Authentication;

use App\Http\Controllers\WEB\BaseController as Controller;
use App\Http\Requests\Authentication\ResetPasswordRequest;
use App\Http\Requests\Authentication\SetPasswordRequest;
use App\Services\Authentication\ActiveUserService;
use App\Services\Authentication\ResetPasswordService;
use Illuminate\Support\Facades\Auth;

class ResetPasswordController extends Controller
{
    private ResetPasswordService $service;
    private ActiveUserService $activeUserService;

    public function __construct(ResetPasswordService $service, ActiveUserService $activeUserService)
    {
        $this->service = $service;
        $this->activeUserService = $activeUserService;
    }

    public function sendMail(ResetPasswordRequest $request)
    {
        if ($this->service->sendMail($request->validated())) {
            return redirect()->back()->with(['success' => 'Đã gửi mail lấy lại mật khẩu']);
        }

        return redirect()->back()->with(['error' => 'Gửi thất bại']);
    }

    public function resetPassword(SetPasswordRequest $request) {
        $data = $request->validated();
        if($this->activeUserService->updatePassword($data)) {
            session(['user'=> Auth::user()->toArray()]);
            return redirect('/');
        }

        return redirect()->back()->with(['error' => 'Cập nhật mật khẩu thất bại']);
    }

    public function setPasswordIndex(SetPasswordRequest $request)
    {
        $data = $request->validated();
        return view('authentication.set_password', ['email' => data_get($data, 'email')]);
    }

    public function resetPasswordIndex() {
        return view('authentication.reset_password');
    }
}
