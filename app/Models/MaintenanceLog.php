<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MaintenanceLog
 *
 * @property int $id
 * @property int $motor_id
 * @property int $schedule_id
 * @property int $admin_id
 * @property \Illuminate\Support\Carbon $inspection_date
 * @property string|null $general_notes
 * @property string|null $photo_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
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

    protected function casts(): array
    {
        return [
            'inspection_date' => 'date',
        ];
    }

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
