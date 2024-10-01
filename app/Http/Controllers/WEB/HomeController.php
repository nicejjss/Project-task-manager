<?php

namespace App\Http\Controllers\WEB;

use App\Http\Requests\HomeRequest;
use App\Models\User;
use App\Services\HomeServices;

class HomeController extends BaseController
{
    private HomeServices $services;
    public function __construct(HomeServices $services)
    {
        $this->services = $services;
    }

    public function index(HomeRequest $request)
    {
        return view('home')->with(['projects' => $this->services->getInfor($request->validated())]);
    }


}
