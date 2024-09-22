<?php

namespace App\Services;

use App\Enums\ProjectStatus;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Jobs\ProjectInviteJob;
use App\Repositories\ProjectMemberRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\TaskRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TaskServices
{
    private TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository, ProjectRepository $projectRepository)
    {
        $this->taskRepository = $taskRepository;
        $this->projectRepository = $projectRepository;
    }

    public function createView(int $projectId): array
    {
        $members = [];
        $project = $this->projectRepository->where(['project_id' , '=', $projectId])->first();
        $owner = $project->owner;
        $members[] = [
            'avatar' => $owner->avatar,
            'id' => $owner->id,
            'name' => $owner->name,
        ];

        $projectMembers = $project->members()->join('users', 'users.id' , '=', 'projectmembers.user_id')
            ->select(['users.id', 'users.name', 'users.avatar'])->get()->toArray();
        $members = array_merge($members, $projectMembers);

        return ['projectId' => $projectId, 'taskPriority' => TaskPriority::MESSAGE, 'members' => $members];
    }
}
