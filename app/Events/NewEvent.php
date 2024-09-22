<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class NewEvent implements ShouldBroadcastNow
{
    use SerializesModels;

    public string $msg;

    /**
     * Create a new event instance.
     */
    public function __construct(
        string $msg
    ) {
        $this->msg = $msg;
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
                new Channel('channels.user_' . 5),
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
        return 'my-event';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => 'No no no no no no no no no no no no no no no no no no',
            'msg' => $this->msg,
        ];
    }
}

