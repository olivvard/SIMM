<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $activities = [
            ['activity_code' => 'ACT-01', 'activity_name' => 'Regreasing bearing motor',                          'category' => 'Lubrication'],
            ['activity_code' => 'ACT-02', 'activity_name' => 'Replace lube oil & resetting sight glass bearing',  'category' => 'Lubrication'],
            ['activity_code' => 'ACT-03', 'activity_name' => 'Cleaning/seal/bolt/PBS check',                      'category' => 'Inspection'],
            ['activity_code' => 'ACT-04', 'activity_name' => 'Seal & terminal bolt check',                        'category' => 'Inspection'],
            ['activity_code' => 'ACT-05', 'activity_name' => 'Motor body cleaning',                               'category' => 'Cleaning'],
            ['activity_code' => 'ACT-06', 'activity_name' => 'Check space heater',                                'category' => 'Electrical'],
            ['activity_code' => 'ACT-07', 'activity_name' => 'Check & clean motor filter',                        'category' => 'Cleaning'],
            ['activity_code' => 'ACT-08', 'activity_name' => 'Physical check & grounding resistance',             'category' => 'Electrical'],
            ['activity_code' => 'ACT-09', 'activity_name' => 'Check indicator lamp & ampere meter',               'category' => 'Electrical'],
            ['activity_code' => 'ACT-10', 'activity_name' => 'Check insulation resistance contact to ground',     'category' => 'Electrical'],
            ['activity_code' => 'ACT-11', 'activity_name' => 'Check power cable insulation & motor winding',      'category' => 'Electrical'],
            ['activity_code' => 'ACT-12', 'activity_name' => 'Check & clean press/temperature gauge',             'category' => 'Inspection'],
            ['activity_code' => 'ACT-13', 'activity_name' => 'Painting motor body',                               'category' => 'Cleaning'],
            ['activity_code' => 'ACT-14', 'activity_name' => 'Check & clean grease pipe',                        'category' => 'Lubrication'],
            ['activity_code' => 'ACT-15', 'activity_name' => 'Check fan cover or cooler box',                     'category' => 'Inspection'],
        ];

        foreach ($activities as $act) {
            Activity::updateOrCreate(
                ['activity_code' => $act['activity_code']],
                $act
            );
        }
    }
}
