<?php

namespace App\Http\Controllers\WEB;

use App\Enums\TaskPriority;
use App\Http\Controllers\Controller;
use App\Services\TaskServices;
use Illuminate\Http\Request;

class TaskController extends BaseController
{
    public TaskServices $taskServices;
    public function __construct(TaskServices $taskServices)
    {
        $this->taskServices = $taskServices;
    }

    public function taskCreateView(string $projectId) {
        $data = $this->taskServices->createView((int)$projectId);
        return view('task.create', $data);
    }





}
