<?php

namespace App\Jobs;

use App\Enums\NotificationType;
use App\Events\InviteEvent;
use App\Mail\InviteMail;
use App\Models\User;
use App\Repositories\ProjectRepository;
use App\Repositories\UserNotificationRepository;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class NotificationJob implements ShouldQueue
{
    use Queueable;

    private array $invitedPeople;
    private int $projectId;
    private string $projectName;
    private int $taskId;
    private int $type;

    /**
     * Create a new job instance.
     */
    public function __construct(array $invitedPeople, int $projectId, string $projectName, int $taskId = 0 ,int $type = 9999)
    {
        $this->invitedPeople = $invitedPeople;
        $this->projectId = $projectId;
        $this->projectName = $projectName;
        $this->taskId = $taskId;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $userNotificationRepository = app(UserNotificationRepository::class);
        $projectRepository = app(ProjectRepository::class);

        switch ($this->type) {
            case NotificationType::Invite:
                foreach ($this->invitedPeople as $invitedPerson) {
                    Mail::to($invitedPerson)->send(new InviteMail($this->projectId, $this->projectName));

                    $user = User::where('email', '=', $invitedPerson)->first();

                    $project = $projectRepository->find($this->projectId);


                    if ($user) {
                        $userNotificationRepository->create([
                            'user_id' => $user->id,
                            'project_id' => $this->projectId,
                            'message' => 'Bạn Có lời mời vào dự án ' . $project->name,
                            'notification_type' => NotificationType::Invite,
                        ]);

                        // Send email and create notification for invited person
                        event(new InviteEvent($this->projectName, (string)$user->id, $this->projectId));
                    }
                } break;

            case NotificationType::Comment:
            case NotificationType::ChangeContent:
            case NotificationType::Assign: break;
            default:
        }
    }
}
