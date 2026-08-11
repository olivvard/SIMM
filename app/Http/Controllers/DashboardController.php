<?php

namespace App\Http\Controllers;

use App\Models\Motor;
use App\Models\Schedule;
use App\Models\MaintenanceLog;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Stat Cards ───────────────────────────────────────────────────────
        $totalMotors    = Motor::count();
        $activeMotors   = Motor::whereNull('deleted_at')->count();
        $pendingCount   = Schedule::where('status', 'pending')->count();
        $overdueCount   = Schedule::where('status', 'overdue')->count();
        $doneCount      = Schedule::where('status', 'done')->count();
        $totalUsers     = User::count();
        $teknisiCount   = User::where('role', 'teknisi')->count();

        $thisMonth = MaintenanceLog::whereMonth('inspection_date', Carbon::now()->month)
                        ->whereYear('inspection_date', Carbon::now()->year)
                        ->count();

        $totalLogs = MaintenanceLog::count();

        // ── Upcoming schedules within next 3 days (H-3) ──────────────────────
        $upcomingSchedules = Schedule::with('motor')
            ->where('status', 'pending')
            ->whereBetween('schedule_date', [
                Carbon::today(),
                Carbon::today()->addDays(3),
            ])
            ->orderBy('schedule_date')
            ->get();

        // ── Overdue schedules (for alert) ────────────────────────────────────
        $overdueSchedules = Schedule::with('motor')
            ->where('status', 'overdue')
            ->orderBy('schedule_date', 'asc')
            ->take(5)
            ->get();

        // ── Monthly maintenance data for chart (last 6 months) ────────────────
        $monthlyData = [];
        $monthLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = MaintenanceLog::whereMonth('inspection_date', $month->month)
                ->whereYear('inspection_date', $month->year)
                ->count();
            $monthlyData[] = $count;
            $monthLabels[] = $month->format('M Y');
        }

        // ── Motor category distribution for chart ────────────────────────────
        $categoryData = Motor::whereNull('deleted_at')
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->get();

        // ── Schedule status distribution ─────────────────────────────────────
        $scheduleStatusData = [
            'pending' => $pendingCount,
            'done'    => $doneCount,
            'overdue' => $overdueCount,
        ];

        // ── Recent Maintenance Logs ──────────────────────────────────────────
        $recentLogs = MaintenanceLog::with(['motor', 'admin'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // ── Maintenance rate this month vs last month ─────────────────────────
        $lastMonth = MaintenanceLog::whereMonth('inspection_date', Carbon::now()->subMonth()->month)
            ->whereYear('inspection_date', Carbon::now()->subMonth()->year)
            ->count();
        $maintenanceTrend = $lastMonth > 0
            ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1)
            : ($thisMonth > 0 ? 100 : 0);

        return view('dashboard.index', compact(
            'totalMotors',
            'activeMotors',
            'pendingCount',
            'overdueCount',
            'doneCount',
            'thisMonth',
            'totalLogs',
            'totalUsers',
            'teknisiCount',
            'upcomingSchedules',
            'overdueSchedules',
            'monthlyData',
            'monthLabels',
            'categoryData',
            'scheduleStatusData',
            'recentLogs',
            'maintenanceTrend',
            'lastMonth'
        ));
    }
}
