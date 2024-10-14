<?php

namespace App\Services;

use App\Enums\ProjectStatus;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Jobs\ProjectInviteJob;
use App\Repositories\FileAttachmentRepository;
use App\Repositories\ProjectMemberRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\TaskRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TaskServices
{
    private TaskRepository $taskRepository;
    private ProjectRepository $projectRepository;
    private FileAttachmentRepository $fileAttachmentRepository;
    private ProjectMemberRepository $projectMemberRepository;

    public function __construct(TaskRepository $taskRepository, ProjectRepository $projectRepository, FileAttachmentRepository $fileAttachmentRepository, ProjectMemberRepository $projectMemberRepository)
    {
        $this->taskRepository = $taskRepository;
        $this->projectRepository = $projectRepository;
        $this->fileAttachmentRepository = $fileAttachmentRepository;
        $this->projectMemberRepository = $projectMemberRepository;
    }

    public function createView(int $projectId): array
    {
        $members = [];
        $project = $this->projectRepository->where([['project_id' , '=', $projectId]])->first();
        $owner = $project->owner;
        $members[] = [
            'avatar' => $owner->avatar,
            'id' => $owner->id,
            'name' => $owner->name,
        ];

        $projectMembers = $project->members()->join('users', 'users.id' , '=', 'projectmembers.user_id')
            ->select(['users.id', 'users.name', 'users.avatar'])->get()->toArray();
        $members = array_merge($members, $projectMembers);

        $taskTypes = $project->taskTypes()->get();

        //TODO: Parent Task

        return ['projectId' => $projectId, 'taskPriority' => TaskPriority::MESSAGE, 'members' => $members, 'taskTypes' =>$taskTypes];
    }

    /**
     * @throws \Throwable
     */
    public function taskCreate(array $data, int $projectId): mixed
    {
        try {
            $maxTaskId = $this->projectRepository->where([['project_id' , '=', $projectId]])
                ->first()->tasks()->max('task_id') ?? 0;
            $taskID = ++$maxTaskId;
            $task = $this->taskRepository->create([
                'task_id' => $taskID,
                'title' => $data['title'],
                'project_id' => $projectId,
                'assigned_to' => (int)$data['assignee'],
                'priority' => (int)$data['priority'],
                'tasktype_id' => (int)$data['tasktype'],
                'deadline' => $data['deadline'],
                'created_by' => (int)auth()->user()->id,
                'status' =>TaskStatus::Open,
            ]);

            // Check if there are any files to upload
            $description = $data['description'] ?? null;
            $attachments = $data['attachments'] ?? [];

            $fileNameDescription = 'description_' . Str::random(10);
            $ext = $description->getClientOriginalExtension();
            $path = $fileNameDescription . '.' . $ext;
            Storage::disk('gcs')->put('tasks/'. $taskID . '/' . $path, file_get_contents($description), 'public');
            $this->taskRepository->update($taskID,[
                'description' => $path,
            ]);

            if ($attachments) {
                foreach ($attachments as $attachment) {
                    $fileTaskAttach =pathinfo($attachment->getClientOriginalName(), PATHINFO_FILENAME). '_' . Str::random(10);
                    $ext = $attachment->getClientOriginalExtension();
                    $path = $fileTaskAttach . '.' . $ext;
                    Storage::disk('gcs')->put('tasks/' . $taskID . '/'  . $path, file_get_contents($attachment), 'public');
                    $this->fileAttachmentRepository->create([
                        'task_id' => $taskID,
                        'project_id' => $projectId,
                        'file_path' => $path,
                        'uploaded_by' => auth()->user()->id,
                    ]);
                }
            }

            if ($task->assigned_to != auth()->user()->id) {
                //TODO: sent event
            }

            return $taskID; // Return the created task ID
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public function list($data, $projectId)
    {
        $title = $data['title'] ?? null;
        $sort = $data['sort'] ?? null;
        $assignee = $data['assignee'] ?? null;
        $creator = $data['creator'] ?? null;
        $type = $data['type'] ?? null;
        $status = $data['status'] ?? null;

        $tasks = $this->taskRepository->where([['project_id' , '=', $projectId]])
            ->join('users', 'tasks.assigned_to', '=', 'users.id')
            ->when(!empty($title), function ($query) use ($title) {
                $query->where('title', 'like', '%' . $title . '%');
            })
            ->when($assignee, function ($query) use ($assignee) {
                $query->where('assigned_to', '=', $assignee);
            })
            ->when($creator, function ($query) use ($creator) {
                $query->where('created_by', '=', $creator);
            })
            ->when($type, function ($query) use ($type) {
                $query->where('tasktype_id', '=', $type);
            })
            ->when($status > -99, function ($query) use ($status) {
                $query->where('status', '=', $status);
            })
            ->when($sort, function ($query) use ($sort) {
               switch ($sort) {
                   case 1: $query->orderBy('deadline', 'asc'); break;
                   case 2: $query->orderBy('deadline', 'desc'); break;
                   case 3: $query->orderBy('priority', 'asc'); break;
                   case 4: $query->orderBy('priority', 'desc'); break;
               }
            })
            ->get();

        $project = $this->projectRepository->find($projectId);
        $owner = $project->owner()->first()->only(['id', 'name']);
        $members = $this->projectMemberRepository->where([['project_id' , '=', $projectId]])
        ->join('users', 'users.id' , '=', 'projectmembers.user_id')->get(['users.id', 'users.name'])->toArray();

        $members[] = $owner;

        $types = $project->taskTypes()->get();

        foreach ($tasks as $task) {
            $deadline = $task->deadline ?? Carbon::parse($task->deadline);

            $task['statusMessage'] = TaskStatus::MESSAGE($task->status);
            $task['isDeadline'] = !is_null($deadline) && Carbon::parse($task->deadline) <= today();
            $task['deadline'] = $task->deadline ? Carbon::parse($task->deadline)->format('d/m/Y') : '--/--/--';
            $task['priorityMessage'] = TaskPriority::MESSAGE($task->priority);
            $task['avatar'] = is_null($task->avatar) ? null : Storage::disk('gcs')->url($task->avatar);
        }

        return [
            'projectId' => $projectId,
            'types' => $types,
            'members' => $members,
            'tasks' => $tasks,
        ];
    }

    public function index($projectID, $taskID)
    {

    }
}
