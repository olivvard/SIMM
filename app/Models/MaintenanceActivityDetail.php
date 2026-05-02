<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceActivityDetail extends Model
{
    protected $fillable = [
        'maintenance_log_id',
        'activity_id',
        'is_done',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_done' => 'boolean',
        ];
    }

    // Relations
    public function maintenanceLog()
    {
        return $this->belongsTo(MaintenanceLog::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
