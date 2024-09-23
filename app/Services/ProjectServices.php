<?php

namespace App\Services;

use App\Enums\ProjectStatus;
use App\Enums\TaskStatus;
use App\Jobs\ProjectInviteJob;
use App\Repositories\ProjectMemberRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProjectServices
{
    private ProjectRepository $projectRepository;
    private ProjectMemberRepository $projectMemberRepository;
    private UserRepository $userRepository;

    public function __construct(ProjectRepository $projectRepository, ProjectMemberRepository $projectMemberRepository, UserRepository $userRepository)
    {
        $this->projectRepository = $projectRepository;
        $this->projectMemberRepository = $projectMemberRepository;
        $this->userRepository = $userRepository;
    }


    public function store($data) {
        $name = data_get($data, 'name');
        $ownerid = auth()->user()->id;
        $status = ProjectStatus::Open;

        try {
            $project = $this->projectRepository->create([
                'project_name' => $name,
                'status' => $status,
                'owner_id' => $ownerid,
            ]);
            $file = data_get($data, 'description');

            if($file){
                $fileName = 'project_' . $project->project_id;
                $ext = $file->getClientOriginalExtension();
                $path = $fileName . '.' . $ext;
                Storage::disk('gcs')->put('project/' . $path, file_get_contents($file), 'public');
                $path = 'project/' . $path;
                $project->description = $path;
                $project->save();
            }
            $invitedPeople = json_decode(data_get($data, 'people'));
            $ownerEmail = array(auth()->user()->email);
            $invitedPeople = array_diff($invitedPeople, $ownerEmail);

            if (count($invitedPeople)) {
                ProjectInviteJob::dispatch($invitedPeople, $project->project_id, $project->project_name);
            }

            return $project->project_id;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public function invite(int $projetId): bool
    {
        $userId = auth()->user()->id;
        try {
            $project = $this->projectRepository->find($projetId);

            if ($project->owner_id === $userId) {
                return true;
            }

            if ($this->projectMemberRepository->where([
                ['project_id', '=', $projetId],
                ['user_id', '=', $userId],
            ])->exists()) {
                return true;
            }

            $this->projectMemberRepository->create([
                'project_id' => $projetId,
                'user_id' => $userId,
                'joined_at' => now(),
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public function index(array $data): array
    {
        $projectId = (int)data_get($data, '0');
        $project = $this->projectRepository->find($projectId);
        $tasks = $project->tasks()->get();
        $taskCount = $tasks->count();

        $openCount = $tasks->where(['status', '=', TaskStatus::Open])->count();
        $inProgressCount = $tasks->where(['status', '=', TaskStatus::Progressing])->count();
        $acceptedCount = $tasks->where(['status', '=', TaskStatus::AcceptedTime])->count();
        $doneCount = $tasks->where(['status', '=', TaskStatus::Done])->count();

        $projectDescription = Storage::disk('gcs')->get($project->description);

        $owner = $project->owner;
        $members[] = [
            'avatar' => $owner->avatar,
            'email' => $owner->email,
        ];

        $projectMems = $project->members()->join('users', 'users.id', '=', 'projectmembers.user_id')->get()->select(['email', 'avatar'])->toArray();
        foreach ($projectMems as $projectMember) {
            $members[] = $projectMember;
        }

        return [
            'projectId' => $projectId,
            'projectDescription' => $projectDescription,
            'tasks' => [
                'count' => $taskCount,
                'openCount' => $openCount,
                'inProgressCount' => $inProgressCount,
                'acceptedCount' => $acceptedCount,
                'doneCount' => $doneCount,
            ],
            'members' => $members,
            'ownerId' => $owner->id,
        ];
    }

    public function addMember(string $email, int $projectId): int
    {
        try {
            $project = $this->projectRepository->find($projectId);
            $user = $this->userRepository->getUser(['email' => $email]);

            if (!$user) {
                ProjectInviteJob::dispatch([$email], $projectId, $project->project_name);
                return 1;
            } elseif (!$project->members()->where([['user_id', '=', $user->id]])->count() && $project->owner->id !== $user->id) {
                ProjectInviteJob::dispatch([$email], $projectId, $project->project_name);
                return 2;
            }

            return 3;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return 0;
        }
    }

    public function get(int $projectId) {
        $project = $this->projectRepository->find($projectId);
        $emails = $project->members()->get();


        return [
            'name' => $project->project_name,
            'description' => Storage::disk('gcs')->get($project->description),
//            'emails' =>
        ];
    }
}
