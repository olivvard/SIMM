<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ScheduleAlert implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int    $scheduleId;
    public string $motorCode;
    public string $scheduleDate;
    public string $type; // 'approaching' | 'overdue' | 'done'

    public function __construct(
        int    $scheduleId,
        string $motorCode,
        string $scheduleDate,
        string $type
    ) {
        $this->scheduleId   = $scheduleId;
        $this->motorCode    = $motorCode;
        $this->scheduleDate = $scheduleDate;
        $this->type         = $type;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('schedule-alerts'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'schedule.alert';
    }

    public function broadcastWith(): array
    {
        return [
            'schedule_id'   => $this->scheduleId,
            'motor_code'    => $this->motorCode,
            'schedule_date' => $this->scheduleDate,
            'type'          => $this->type,
        ];
    }
}
