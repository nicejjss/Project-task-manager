<?php

namespace App\Http\Controllers\WEB;

use App\Models\User;
use App\Services\HomeServices;

class HomeController extends BaseController
{
    private HomeServices $services;
    public function __construct(HomeServices $services)
    {
        $this->services = $services;
    }

    public function index()
    {
        $info = $this->services->getInfor();
        /** @var User $user */
       $projects = data_get($info, 'projects');
       $tasks = data_get($info, 'tasks');
        return view('home')->with(['projects' => $projects, 'tasks' => $tasks]);
    }


}
