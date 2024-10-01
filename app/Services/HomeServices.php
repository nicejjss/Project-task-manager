<?php

namespace App\Services;

use App\Enums\ProjectStatus;
use App\Enums\TaskStatus;
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
        $projectStatus = data_get($validated, 'project_status');
        $userName = data_get($validated, 'user_name');
        $sort = data_get($validated, 'sort', 0);
        /** @var User $user */
        $user = $this->userRepository->getAuthUser();

        $ownProjects = $user->projects()->pluck('project_id')->toArray();
        $membersProject  = $this->projectMemberRepository->where([['user_id', '=', $user->id]])->pluck('project_id')->unique()->toArray();
        $projectIDs = array_merge($ownProjects, $membersProject);

        $projects = $this->projectRepository->whereIn('project_id', $projectIDs)
            ->when($projectName, function ($query, $projectName) {
                return $query->where('project_name', 'like', "%$projectName%");
            })
            ->when(!is_null($projectStatus), function ($query, $projectStatus) {
                return $query->where('status', $projectStatus);
            })
            ->when($userName, function ($query, $userName) {
                $userIds = User::where('name', 'like', "%$userName%")->pluck('id')->toArray();
                return $query->whereIn('user_id', $userIds);
            })
            ->when($sort <= 4, function($query, $sort) {
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
            $project['taskWaitedTimeCount'] = $project->tasks()->where('status', '=',TaskStatus::WaitedTime)->count();
            $project['taskDoneCount'] = $project->tasks()->where('status', '=',TaskStatus::Done)->count();
            $project['statusText'] = ProjectStatus::MESSAGE($project->status);
            $project['created_at'] = Carbon::parse($project->created_at)->startOfDay();
        }

        $projects->when($sort > 4, function (Collection $collection, int $value) {
            return match ($value) {
                6 => $collection->sortByDesc('membersCount'),
                7 => $collection->sortBy('tasksCount'),
                8 => $collection->sortByDesc('tasksCount'),
                default => $collection->sortBy('membersCount'),
            };
        });

        return $projects;
    }
}
