<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceLog extends Model
{
    protected $fillable = [
        'motor_id',
        'schedule_id',
        'admin_id',
        'inspection_date',
        'general_notes',
        'photo_url',
    ];

    protected $casts = [
        'inspection_date' => 'date',
    ];

    // Relations
    public function motor()
    {
        return $this->belongsTo(Motor::class)->withTrashed();
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function activityDetails()
    {
        return $this->hasMany(MaintenanceActivityDetail::class);
    }
}
