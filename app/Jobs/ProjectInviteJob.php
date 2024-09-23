<?php

namespace App\Jobs;

use App\Enums\NotificationType;
use App\Mail\InviteMail;
use App\Repositories\UserNotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ProjectInviteJob implements ShouldQueue
{
    use Queueable;

    private array $invitedPeople;
    private int $projectId;
    private string $projectName;

    /**
     * Create a new job instance.
     */
    public function __construct(array $invitedPeople, int $projectId, string $projectName)
    {
        $this->invitedPeople = $invitedPeople;
        $this->projectId = $projectId;
        $this->projectName = $projectName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $userNotificationRepository = app(UserNotificationRepository::class);

        foreach ($this->invitedPeople as $invitedPerson) {
            Mail::to($invitedPerson)->send(new InviteMail($this->projectId, $this->projectName));
            $userNotificationRepository->create([
                'email' => $invitedPerson,
                'project_id' => $this->projectId,
                'message' => 'Bạn Có lời mời vào dự án',
                'notification_type' => NotificationType::Invite,
            ]);

            // Send email and create notification for invited person
            event(new \App\Events\InviteEvent($this->projectName, $invitedPerson, $this->projectId));
        }
    }
}
