<?php

use Illuminate\Support\Facades\Broadcast;

// Public channel for schedule alerts (no auth required)
Broadcast::channel('schedule-alerts', function () {
    return true;
});
