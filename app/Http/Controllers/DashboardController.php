<?php

namespace App\Http\Controllers;

use App\Models\Motor;
use App\Models\Schedule;
use App\Models\MaintenanceLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMotors   = Motor::count();
        $pendingCount  = Schedule::where('status', 'pending')->count();
        $overdueCount  = Schedule::where('status', 'overdue')->count();
        $thisMonth     = MaintenanceLog::whereMonth('inspection_date', Carbon::now()->month)
                            ->whereYear('inspection_date', Carbon::now()->year)
                            ->count();

        // Upcoming schedules within next 3 days (H-3)
        $upcomingSchedules = Schedule::with('motor')
            ->where('status', 'pending')
            ->whereBetween('schedule_date', [
                Carbon::today(),
                Carbon::today()->addDays(3),
            ])
            ->orderBy('schedule_date')
            ->get();

        return view('dashboard.index', compact(
            'totalMotors',
            'pendingCount',
            'overdueCount',
            'thisMonth',
            'upcomingSchedules'
        ));
    }
}
