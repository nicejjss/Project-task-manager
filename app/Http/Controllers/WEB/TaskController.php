<?php

namespace App\Http\Controllers\WEB;

use App\Http\Requests\Task\CreateRequest;
use App\Http\Requests\Task\ViewRequest;
use App\Services\TaskServices;

class TaskController extends BaseController
{
    public TaskServices $taskServices;
    public function __construct(TaskServices $taskServices)
    {
        $this->taskServices = $taskServices;
    }

    public function taskCreateView(string $projectId, int $taskId = 0) {
        $data = $this->taskServices->createView((int)$projectId, $taskId);
        return view('task.create', $data);
    }

    public function taskCreate(CreateRequest $request, string $projectId) {
        $id = $this->taskServices->taskCreate($request->validated(), $projectId);
        return $id;
    }

    public function taskListView(ViewRequest $request, int $projectId) {
        $data = $this->taskServices->list($request->validated(), $projectId);
        return view('task.list')->with($data);
    }

    public function index(int $projectId, int $taskId) {
        $data = $this->taskServices->index($projectId, $taskId);
        return view('task.index')->with($data);
    }

    public function addComment(int $projectId, int $taskId) {
        return $this->taskServices->addComment($projectId, $taskId);
    }

    public function download(int $projectId, int $taskId, int $attachmentId)
    {
        return $this->taskServices->download($projectId , $taskId, $attachmentId);
    }

    public function deleteFile(int $projectId, int $taskId, int $attachmentId)
    {
        return $this->taskServices->deleteFile($projectId, $taskId, $attachmentId);
    }

    public function createComment(int $projectId, int $taskId)
    {
        return $this->taskServices->addComment(request()->all(), $projectId, $taskId);
    }
}
