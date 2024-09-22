<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\ProjectRepository;
use App\Repositories\UserRepository;

class HomeServices
{
    private ProjectRepository $projectRepository;
    private UserRepository $userRepository;

    public function __construct(ProjectRepository $projectRepository, UserRepository $userRepository)
    {
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
    }

    public function getInfor() {
        $projects = [];
        /** @var User $user */
        $user = $this->userRepository->getAuthUser();
        $ownProjects = $user->projects()->get();
        foreach ($ownProjects as $project) {
            $members = $project->members()->get()->count() + 1;
            $tasks = $project->tasks()->get()->count();
            $projects[] = [
                'id' => $project->project_id,
                'name' => $project->project_name,
                'is_owner' => 1,
                'members' => $members,
                'tasks' => $tasks,
            ];
        }

        $memberProjects = $user->projectMembers()->get();
        foreach ($memberProjects as $memberProject) {
            $project = $memberProject->project;
            $members = $project->members()->get()->count() + 1;
            $tasks = $project->tasks()->get()->count();
            $projects[] = [
                'name' => $project->project_name,
                'is_owner' => 0,
                'members' => $members,
                'tasks' => $tasks,
                'id' => $project->project_id,
            ];
        }

        $tasks = [];
        $tasksAssigned = $user->tasksAssigned()->orderBy('priority')->orderBy('deadline', 'desc')->get();

        foreach ($tasksAssigned as $task) {
            $project = $task->project;
            $tasks[] = [
                'id' => $task->task_id,
                'title' => $task->title,
                'projectName' => $project->project_name,
                'dueDate' => $task->deadline,
                'status' => $task->status,
            ];

        }

        return [
            'projects' => $projects,
            'tasks' => $tasks,
        ];
    }
}
