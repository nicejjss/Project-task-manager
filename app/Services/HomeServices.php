<?php

namespace App\Services;

use App\Enums\ProjectStatus;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\User;
use App\Repositories\ProjectMemberRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class HomeServices
{
    private ProjectRepository $projectRepository;
    private UserRepository $userRepository;

    private ProjectMemberRepository $projectMemberRepository;

    public function __construct(ProjectRepository $projectRepository, UserRepository $userRepository, ProjectMemberRepository $projectMemberRepository)
    {
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
        $this->projectMemberRepository = $projectMemberRepository;
    }

    public function getInfor(array $validated) {
        $projectName = data_get($validated, 'project_name');
        $projectStatus = (int)data_get($validated, 'project_status', -1);
        $userMail = data_get($validated, 'user_mail');
        $sort = (int)data_get($validated, 'sort', 0);
        $role = (int)data_get($validated, 'role', 0);

        /** @var User $user */
        $user = $this->userRepository->getAuthUser();
        $ownProjects = $user->projects()->pluck('project_id')->toArray();
        $membersProject  = $this->projectMemberRepository->where([['user_id', '=', $user->id]])->pluck('project_id')->unique()->toArray();
        $projectIDs = array_merge($ownProjects, $membersProject);

        $projects = $this->projectRepository->whereIn('project_id', $projectIDs)
            ->when($projectName, function ($query, $projectName) {
                return $query->where('project_name', 'like', "%$projectName%");
            })
            ->when($projectStatus > -1, function ($query) use ($projectStatus) {
                return $query->where('status', $projectStatus);
            })
            ->when($userMail, function ($query) use ($userMail, $projectIDs) {
                $ownerProjectIds = Project::join('users', 'users.id', '=', 'projects.owner_id')
                    ->where('email', '=', $userMail)->whereIn('project_id', $projectIDs)->pluck('projects.project_id')->toArray();
                $projectUserMailIds = Project::join('projectmembers', 'projectmembers.project_id', '=', 'projects.project_id')
                    ->join('users', 'users.id', '=', 'projectmembers.user_id')
                    ->where('email', '=', $userMail)->whereIn('projects.project_id', $projectIDs)->pluck('projects.project_id')->toArray();
                $projectIds = array_merge($ownerProjectIds, $projectUserMailIds);
                return $query->whereIn('project_id', $projectIds);
            })
            ->when($role, function ($query) use ($role, $ownProjects, $membersProject) {
                return match ($role) {
                    1 => $query->whereIn('project_id', $ownProjects),
                    2 => $query->whereIn('project_id', $membersProject),
                };
            })
            ->when($sort <= 4, function($query) use ($sort) {
                return match ($sort) {
                    2 => $query->orderBy('project_name', 'desc'),
                    3 => $query->orderBy('created_at', 'asc'),
                    4 => $query->orderBy('created_at', 'desc'),
                    default => $query->orderBy('project_name', 'asc'),
                };
            })
            ->get();

        foreach ($projects as $project) {
            $project['membersCount'] = $project->members()->count() +1 ;
            $project['tasksCount'] = $project->tasks()->count();
            $project['taskOpenCount'] = $project->tasks()->where('status', '=',TaskStatus::Open)->count();
            $project['taskProcessingCount'] = $project->tasks()->where('status', '=',TaskStatus::Progressing)->count();
            $project['taskDoneCount'] = $project->tasks()->where('status', '=',TaskStatus::Done)->count();
            $project['statusText'] = ProjectStatus::MESSAGE($project->status);
            $project['statusColor'] = ProjectStatus::getKey($project->status);
            $project['created_at'] = Carbon::parse($project->created_at)->toDateString();
            $project['role'] = in_array($project->project_id, $ownProjects)?'Quản Lý':'Thành Viên';
        }

        $projects->when($sort > 4, function (Collection $collection) use ($sort) {
            return match ($sort) {
                6 => $collection->sortByDesc('membersCount'),
                7 => $collection->sortBy('tasksCount'),
                8 => $collection->sortByDesc('tasksCount'),
                default => $collection->sortBy('membersCount'),
            };
        });

        return $projects;
    }
}
