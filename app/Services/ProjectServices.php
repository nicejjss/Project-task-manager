<?php

namespace App\Services;

use App\Enums\NotificationType;
use App\Enums\ProjectStatus;
use App\Enums\TaskStatus;
use App\Jobs\NotificationJob;
use App\Repositories\ProjectMemberRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\TaskTypeRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
                $fileName = 'project_' . $project->project_id . '_' . Str::random(10);
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
                NotificationJob::dispatch($invitedPeople, $project->project_id, $project->project_name, NotificationType::Invite);
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

        $status = [
            'class' => strtolower(ProjectStatus::getKey($project->status)),
            'text' => ProjectStatus::MESSAGE($project->status),
        ];

        $openCount = $tasks->where(['status', '=', TaskStatus::Open])->count();
        $inProgressCount = $tasks->where(['status', '=', TaskStatus::Progressing])->count();
        $doneCount = $tasks->where(['status', '=', TaskStatus::Done])->count();
        $closedCount = $tasks->where(['status', '=', TaskStatus::Closed])->count();

        $projectDescription = Storage::disk('gcs')->get($project->description);

        $owner = $project->owner;
        $ownerAvatar = $owner->avatar;

        if ($ownerAvatar) {
            if (!Str::contains($ownerAvatar, 'http')) {
                $ownerAvatar = Storage::disk('gcs')->url($ownerAvatar);
            }
        }

        $members[] = [
            'avatar' => $ownerAvatar,
            'email' => $owner->email,
        ];

        $projectMems = $project->members()->join('users', 'users.id', '=', 'projectmembers.user_id')->get()->select(['email', 'avatar'])->toArray();
        foreach ($projectMems as $projectMember) {
            $memberAvatar = data_get($projectMember, 'avatar');

            if ($memberAvatar) {
                if (!Str::contains($memberAvatar, 'http')) {
                    $memberAvatar = Storage::disk('gcs')->url($memberAvatar);
                }
            }

            $projectMember['avatar'] = $memberAvatar;
            $members[] = $projectMember;
        }

        return [
            'projectId' => $projectId,
            'projectDescription' => $projectDescription,
            'isClose' => $project->status === ProjectStatus::Closed,
            'status' => $status,
            'tasks' => [
                'count' => $taskCount,
                'openCount' => $openCount,
                'inProgressCount' => $inProgressCount,
                'doneCount' => $doneCount,
                'closedCount' => $closedCount,
            ],
            'members' => $members,
            'ownerId' => $owner->id,
            'taskTypes' => $project->taskTypes()->where('is_delete', '=', 0)->get()->toArray(),
        ];
    }

    public function addMember(string $email, int $projectId): int
    {
        try {
            $project = $this->projectRepository->find($projectId);
            $user = $this->userRepository->getUser(['email' => $email]);

            if (!$user) {
                NotificationJob::dispatch([$email], $projectId, $project->project_name);
                return 1;
            } elseif (!$project->members()->where([['user_id', '=', $user->id]])->count() && $project->owner->id !== $user->id) {
                NotificationJob::dispatch([$email], $projectId, $project->project_name);
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
        $members = $project->members()->get();


        return [
            'projectId' => $project->project_id,
            'name' => $project->project_name,
            'description' => Storage::disk('gcs')->get($project->description),
            'members' => $members,
        ];
    }

    public function edit($data) {

        try {
            $name = data_get($data, 'name');
            $file = data_get($data, 'description');
            $projectId = data_get($data, 'projectID');
            $project = $this->projectRepository->find($projectId);
            $membersEmails = Arr::flatten($project->members()->join('users', 'users.id', '=', 'projectmembers.user_id')
                                ->get('email')->toArray());

            if($file){
                Storage::disk('gcs')->delete($project->description);
                $fileName = 'project_' . $project->project_id . '_' . Str::random(10);
                $ext = $file->getClientOriginalExtension();
                $path = 'project/' . $fileName . '.' . $ext;
                Storage::disk('gcs')->put($path, file_get_contents($file), 'public');
            }
            $invitedPeople = json_decode(data_get($data, 'people'));
            $ownerEmail = array(auth()->user()->email);
            $invitedPeople = array_diff($invitedPeople, $ownerEmail);

            $removeEmails= array_diff($membersEmails, $invitedPeople); // Items in arr1 but not in arr2
            $addEmails = array_diff($invitedPeople, $membersEmails); // Items in arr2 but not in arr1
            $removeIDs = Arr::flatten($this->userRepository->whereIn('email', $removeEmails)->get(['id'])->toArray());

            if (count($addEmails)) {
                NotificationJob::dispatch($addEmails, $project->project_id, $project->project_name);
            }

            if (count($removeIDs)) {
                $this->projectMemberRepository->whereIn('user_id', $removeIDs)->delete();
            }

            $project = $this->projectRepository->update($projectId ,[
                'project_name' => $name,
                'description' => $path,
            ]);

            return $project->project_id;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public function closeProject(int $projectId): bool
    {
        if ($this->projectRepository->update($projectId, [
           'status' => ProjectStatus::Closed,
           'closed_at' => now(),
        ])) {
            return true;
        };

        return false;
    }
}
