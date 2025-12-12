<?php

namespace App\Events;

use App\Models\AIJob;
use App\Models\Media;
use DragonCode\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AITaskSucceed implements ShouldBroadcastNow, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels, Queueable;

    /**
     * Create a new event instance.
     *
     * @param Media[] $media
     */
    public function __construct(
        public string $aiJobId,
        public string $taskId,
    ) {
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('ai.' . $this->aiJobId),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'ai.succeed';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $aiJob = AIJob::find($this->aiJobId);
        $media = $aiJob->getMedia('images')
            ->map(fn($m) => [
                'id'  => $m->id,
                'url' => $m->getUrl(),
            ])
            ->toArray();

        return [
            'task_id' => $this->taskId,
            'media' => $media
        ];
    }
}
