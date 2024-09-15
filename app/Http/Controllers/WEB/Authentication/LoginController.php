<?php

namespace App\Http\Controllers\WEB\Authentication;

use App\Http\Controllers\WEB\BaseController as Controller;
use App\Http\Requests\Authentication\LoginRequest;
use App\Services\Authentication\LoginService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class LoginController extends Controller
{
    private LoginService $service;

    public function __construct(LoginService $service)
    {
        $this->service = $service;
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $data = $this->service->login($credentials);

        if ($data) {
            session(['user'=> Auth::user()->toArray()]);
            return redirect('/');
        }

        return redirect('/authentication/login')->withErrors(['authentication' => 'Sai Email hoặc Mật Khẩu']);
    }

    public function loginIndex()
    {
        return view('authentication.login');
    }
}
