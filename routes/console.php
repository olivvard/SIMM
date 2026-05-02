<?php

use Illuminate\Support\Facades\Schedule;

// Daily: check and mark overdue schedules, broadcast approaching alerts
Schedule::command('schedules:check-deadlines')->daily();
