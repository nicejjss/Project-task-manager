<?php

namespace App\Services;

use App\Enums\ProjectStatus;
use App\Enums\TaskHistoryType;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\CommentNotification;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskHistory;
use App\Repositories\CommentNotificationRepository;
use App\Repositories\FileAttachmentRepository;
use App\Repositories\ProjectMemberRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\TaskRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TaskServices
{
    private TaskRepository $taskRepository;
    private ProjectRepository $projectRepository;
    private FileAttachmentRepository $fileAttachmentRepository;
    private ProjectMemberRepository $projectMemberRepository;

    private CommentNotificationRepository $commentNotificationRepository;

    public function __construct(
        TaskRepository $taskRepository,
        ProjectRepository $projectRepository,
        FileAttachmentRepository $fileAttachmentRepository,
        ProjectMemberRepository $projectMemberRepository,
        CommentNotificationRepository $commentNotificationRepository
    ) {
        $this->taskRepository = $taskRepository;
        $this->projectRepository = $projectRepository;
        $this->fileAttachmentRepository = $fileAttachmentRepository;
        $this->projectMemberRepository = $projectMemberRepository;
        $this->commentNotificationRepository = $commentNotificationRepository;
    }

    public function createView(int $projectId, $taskId = 0): array
    {
        $members = [];
        $project = $this->projectRepository->where([['project_id', '=', $projectId]])->first();
        $owner = $project->owner;
        $members[] = [
            'avatar' => $owner->avatar,
            'id' => $owner->id,
            'name' => $owner->name,
        ];

        $projectMembers = $project->members()->join('users', 'users.id', '=', 'projectmembers.user_id')
            ->select(['users.id', 'users.name', 'users.avatar'])->get()->toArray();
        $members = array_merge($members, $projectMembers);

        $taskTypes = $project->taskTypes()->get();

        $parentTask = $this->taskRepository->where([
            ['project_id', '=', $projectId],
            ['task_id', '=', $taskId]
        ])->first();

        return [
            'projectId' => $projectId,
            'taskPriority' => TaskPriority::MESSAGE,
            'members' => $members,
            'taskTypes' => $taskTypes,
            'parentTask' => $parentTask,
        ];
    }

    /**
     * @throws \Throwable
     */
    public function taskCreate(array $data, int $projectId): mixed
    {
        try {
            $maxTaskId = $this->projectRepository->where([['project_id', '=', $projectId]])
                ->first()->tasks()->max('task_id') ?? 0;

            if ($maxTaskId === 0) {
                $this->projectRepository->find($projectId)->update(['status' => ProjectStatus::Progressing]);
            }

            $taskType = (int)$data['tasktype'] == 0 ? null : (int)$data['tasktype'];

            $taskID = ++$maxTaskId;
            $task = $this->taskRepository->create([
                'task_id' => $taskID,
                'title' => $data['title'],
                'project_id' => $projectId,
                'assigned_to' => (int)$data['assignee'],
                'priority' => (int)$data['priority'],
                'tasktype_id' => $taskType,
                'deadline' => $data['deadline'],
                'created_by' => (int)auth()->user()->id,
                'status' => TaskStatus::Open,
                'parent_id' => data_get($data, 'parent', 0),
            ]);

            // Check if there are any files to upload
            $description = $data['description'] ?? null;
            $attachments = $data['attachments'] ?? [];

            $fileNameDescription = 'description_' . Str::random(10);
            $ext = $description->getClientOriginalExtension();
            $path = $fileNameDescription . '.' . $ext;
            Storage::disk('gcs')->put('tasks/' . $taskID . '/' . $path, file_get_contents($description), 'public');
            $this->taskRepository->update($taskID, [
                'description' => $path,
            ]);

            if ($attachments) {
                foreach ($attachments as $attachment) {
                    $fileTaskAttach = pathinfo($attachment->getClientOriginalName(), PATHINFO_FILENAME) . '_' . Str::random(10);
                    $ext = $attachment->getClientOriginalExtension();
                    $path = $fileTaskAttach . '.' . $ext;
                    Storage::disk('gcs')->put('tasks/' . $taskID . '/' . $path, file_get_contents($attachment), 'public');
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

            TaskHistory::create([
                'task_id' => $taskID,
                'user_id' => auth()->user()->id,
                'description' => TaskHistoryType::MESSAGE(TaskHistoryType::Create),
                'type' => TaskHistoryType::Create,
                'project_id' => $projectId,
            ]);

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

        $tasks = $this->taskRepository->where([['project_id', '=', $projectId]])
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
                if ($status == TaskStatus::WaitedTime) {
                    $query->where([

                    ]);
                } else {
                    $query->where('status', '=', $status);
                }
            })
            ->when($sort, function ($query) use ($sort) {
                switch ($sort) {
                    case 1:
                        $query->orderBy('deadline', 'asc');
                        break;
                    case 2:
                        $query->orderBy('deadline', 'desc');
                        break;
                    case 3:
                        $query->orderBy('priority', 'asc');
                        break;
                    case 4:
                        $query->orderBy('priority', 'desc');
                        break;
                }
            })
            ->get();

        $project = $this->projectRepository->find($projectId);
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
            'members' => $this->getMembers($projectId),
            'tasks' => $tasks,
        ];
    }

    public function index($projectID, $taskID)
    {
        $task = Task::where([
            ['task_id', '=', $taskID],
            ['project_id', '=', $projectID],
        ])->first();
        $members = $this->getMembers($projectID);
        $description = Storage::disk('gcs')->get('tasks/' . $taskID . '/' . $task->description);
        $assignee = $task->assignee()->first();
        $creator = $task->creator()->first();
        $creator['avatar'] = $members[$creator->id]['avatar'];
        $type = $task->taskType()->first();

        $attachments = $task->attachments()->get();
        $comments = $task->comments()
            ->with('user')
            ->with('commentNotifications.user')
            ->orderBy('created_at')
            ->get();

        foreach ($comments as $comment) {
            $commentNotifies = [];
            foreach ($comment->commentNotifications as $commentNotification) {
                $userAvatar = $members[$commentNotification->user->id]['avatar'];
                $userName = $commentNotification->user->name;
                $commentNotifies[] = [
                    'avatar' => $userAvatar,
                    'name' => $userName,
                ];
            }

            $comment['commentAvatar'] = $members[$comment->user->id]['avatar'];
            $comment['commentName'] = $comment->user->name;
            $comment['commentNotify'] = $comment->commentNotifications();
            $comment['notification'] = $commentNotifies;
        }

        $childTasks = Task::where([
            ['project_id', '=', $projectID],
            ['parent_id', '=', $taskID],
        ])->get();

        $taskParent = Task::where([
            ['project_id', '=', $projectID],
            ['task_id', '=', $task->parent_id],
        ])->first();

        foreach ($attachments as $attachment) {
            $attachment['fileName'] = $attachment->file_path;
            $attachment['file_path'] = Storage::disk('gcs')->url('tasks/' . $taskID . '/' . $attachment->file_path);
        }

        $histories = TaskHistory::where([
            ['task_id', '=', $taskID],
            ['project_id', '=', $projectID],
        ])->join('users', 'users.id', '=', 'taskhistory.user_id')
            ->orderBy('created_at')
            ->get(['user_id', 'name', 'taskhistory.created_at', 'description']);

        $historyLog = [];

        foreach ($histories as $history) {
            $historyLog[] = [
                'avatar' => $members[$history->user_id]['avatar'],
                'name' => $history->name,
                'description' => $history->description,
                'createTime' => $history->created_at,
            ];
        }

        return [
            'title' => $task->title,
            'description' => $description,
            'priority' => $task->priority,
            'priorityMessage' => TaskPriority::MESSAGE($task->priority),
            'status' => $task->status,
            'statusMessage' => TaskStatus::MESSAGE($task->status),
            'type' => $type ? $type->tasktype_name : 'Không Xác Định',
            'deadline' => $task->deadline ? Carbon::parse($task->deadline)->format('d/m/Y') : '--/--/--',
            'isDeadline' =>  !is_null($task->deadline) && Carbon::parse($task->deadline) <= today(),
            'assigneeAvatar' => $members[$assignee->id]['avatar'],
            'assigneeName' => $assignee->name,
            'attachments' => $attachments,
            'taskId' => $taskID,
            'projectId' => $projectID,
            'childTasks' => $childTasks,
            'taskParent' => $taskParent,
            'comments' => $comments,
            'members' => $members,
            'creator' => $creator,
            'createTime' => $task->created_at,
            'histories' => $historyLog,
            ];
    }

    public function download($projectId, $taskId, $attachmentId)
    {
        $attachment = $this->fileAttachmentRepository
            ->where([
                ['file_id', '=', $attachmentId],
                ['project_id', '=', $projectId],
                ['task_id', '=', $taskId],
            ])->first();
        return Storage::disk('gcs')->download('tasks/' . $taskId . '/' . $attachment->file_path);
    }

    public function deleteFile($projectId, $taskId, $attachmentId)
    {
        try {
            $attachment = $this->fileAttachmentRepository->where([
                ['file_id', '=', $attachmentId],
                ['project_id', '=', $projectId],
                ['task_id', '=', $taskId],
            ])->first();

            Storage::disk('gcs')->delete('tasks/' . $taskId . '/' . $attachment->file_path);
            $attachment->delete();

            return true;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            return false;
        }
    }

    public function addComment($data, $projectId, $taskId)
    {
        try {
            $taskComment = TaskComment::create([
                'task_id' => $taskId,
                'project_id' => $projectId,
                'user_id' => auth()->user()->id,
                'comment_text' => data_get($data, 'comment'),
            ]);

            $commentNotifications = [];
            foreach ($data['userNotified'] as $commentNotification) {
                $commentNotifications[] = [
                    'user_id' => $commentNotification,
                    'comment_id' => $taskComment->comment_id,
                ];
            }

            CommentNotification::insert($commentNotifications);

            TaskHistory::create([
                'task_id' => $taskId,
                'user_id' => auth()->user()->id,
                'description' => TaskHistoryType::MESSAGE(TaskHistoryType::Comment),
                'type' => TaskHistoryType::Comment,
                'project_id' => $projectId,
                'created_at' => $taskComment->created_at,
            ]);

            return $taskComment;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            return false;
        }
    }

    public function getMembers($projectId)
    {
        $project = Project::find($projectId);
        $owner = $project->owner()->first()->only(['id', 'name', 'avatar']);
        $owner['avatar'] = is_null($owner['avatar']) ? null : Storage::disk('gcs')->url($owner['avatar']);
        $members = ProjectMember::where([['project_id', '=', $projectId]])
            ->join('users', 'users.id', '=', 'projectmembers.user_id')->get(['users.id', 'users.name', 'users.avatar'])->toArray();

        $members = array_map(function($member) {
            $member['avatar'] = is_null($member['avatar']) ? null : Storage::disk('gcs')->url($member['avatar']);
            return $member;
        }, $members);

        $members[] = $owner;

        $members = collect($members);
        return $members->keyBy('id');
    }

    public function addAttachment($data, $taskId)
    {

    }
}
