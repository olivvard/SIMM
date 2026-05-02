<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Motor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'motor_code',
        'location',
        'area',
        'category',
        'installation_date',
    ];

    protected function casts(): array
    {
        return [
            'installation_date' => 'date',
        ];
    }

    // Relations
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function maintenanceLogs()
    {
        return $this->hasMany(MaintenanceLog::class);
    }
}
