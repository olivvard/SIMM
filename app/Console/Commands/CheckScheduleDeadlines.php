<?php

namespace App\Console\Commands;

use App\Events\ScheduleAlert;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckScheduleDeadlines extends Command
{
    protected $signature   = 'schedules:check-deadlines';
    protected $description = 'Mark overdue schedules and broadcast approaching/overdue alerts.';

    public function handle(): int
    {
        $today = Carbon::today();

        // ── 1. Mark past-due pending schedules as overdue ─────────────────────
        $overdueSchedules = Schedule::where('status', 'pending')
            ->where('schedule_date', '<', $today)
            ->with('motor')
            ->get();

        foreach ($overdueSchedules as $schedule) {
            $schedule->update(['status' => 'overdue']);

            broadcast(new ScheduleAlert(
                scheduleId:   $schedule->id,
                motorCode:    $schedule->motor->motor_code,
                scheduleDate: $schedule->schedule_date->toDateString(),
                type:         'overdue'
            ));

            $this->line("[overdue]     #{$schedule->id} {$schedule->motor->motor_code} — {$schedule->schedule_date->toDateString()}");
        }

        // ── 2. Broadcast approaching alert for schedules within next 3 days ──
        $approachingSchedules = Schedule::where('status', 'pending')
            ->whereBetween('schedule_date', [$today, $today->copy()->addDays(3)])
            ->with('motor')
            ->get();

        foreach ($approachingSchedules as $schedule) {
            broadcast(new ScheduleAlert(
                scheduleId:   $schedule->id,
                motorCode:    $schedule->motor->motor_code,
                scheduleDate: $schedule->schedule_date->toDateString(),
                type:         'approaching'
            ));

            $this->line("[approaching] #{$schedule->id} {$schedule->motor->motor_code} — {$schedule->schedule_date->toDateString()}");
        }

        $this->info("Done. Overdue: {$overdueSchedules->count()}, Approaching: {$approachingSchedules->count()}");

        return Command::SUCCESS;
    }
}
