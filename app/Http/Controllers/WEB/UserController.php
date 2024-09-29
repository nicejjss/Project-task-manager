<?php

namespace App\Http\Controllers\WEB;

use App\Http\Requests\User\ChangeRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Services\Authentication\ActiveUserService;
use App\Services\UserServices;
use Illuminate\Support\Facades\Auth;

class UserController extends BaseController
{
    private UserServices $userService;
    private ActiveUserService $activeUserService;

    public function __construct(UserServices $userService, ActiveUserService $activeUserService)
    {
        $this->userService = $userService;
        $this->activeUserService = $activeUserService;
    }



    public function index()
    {
        $avatar = $this->userService->getAvatar();
        return view('infor.index', ['user' => auth()->user(), 'avatar' => $avatar]);
    }

    public function update(UpdateRequest $request)
    {
        if ($this->userService->update($request->validated())) {
            return response()->json('Cập nhật Thành Công');
        }

        return response()->json('Cập nhật thất bại', 500);
    }

    public function changePasswordIndex(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        return view('infor.change_password', ['email' => auth()->user()->email]);
    }

    public function changePassword(ChangeRequest $request) {
        $data = $request->validated();
        $data['email'] = auth()->user()->email;

        if($this->activeUserService->updatePassword($data)) {
            return redirect('/');
        }

        return response()->json('Cập nhật thất bại', 500);
    }
}
