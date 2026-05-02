<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'activity_code',
        'activity_name',
        'category',
    ];

    // Relations
    public function maintenanceActivityDetails()
    {
        return $this->hasMany(MaintenanceActivityDetail::class);
    }
}
