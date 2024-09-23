<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class InviteEvent implements ShouldBroadcastNow
{
    use SerializesModels;

    public string $projectName;
    private string $target;
    private int $projectId;

    /**
     * Create a new event instance.
     */
    public function __construct(
        string $projectName,
        string $target,
        int $projectID,
    ) {
        $this->projectName = $projectName;
        $this->target = $target;
        $this->projectId = $projectID;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        try {
            return [
                new Channel('channels.user_' . $this->target),
            ];
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return [];
        }
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'invitation';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'msg' => 'Bạn có lời mời tham da dự án ' . $this->projectName,
            'projectId' => $this->projectId
        ];
    }
}

