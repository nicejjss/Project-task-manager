<?php

namespace App\Http\Controllers\WEB\Authentication;

use App\Http\Controllers\WEB\BaseController as Controller;
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

    public function active(ActiveRequest $request)
    {
        $credentials = $request->validated();
        $data = $this->service->active($credentials);

        if ($data) {
            session(['user'=> $data->toArray()]);
            return redirect('/');
        }

        return redirect('/authentication/signup')->withErrors(['authentication' => 'Có lỗi khi đăng ký']);
    }
}
