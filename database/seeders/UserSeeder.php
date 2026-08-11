<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'password'  => Hash::make('admin123'),
                'full_name' => 'Administrator',
                'email'     => 'admin@maintenance.local',
                'role'      => 'admin',
            ]
        );

        User::updateOrCreate(
            ['username' => 'teknisi'],
            [
                'password'  => Hash::make('teknisi123'),
                'full_name' => 'Teknisi Maintenance',
                'email'     => 'teknisi@maintenance.local',
                'role'      => 'teknisi',
            ]
        );
    }
}
