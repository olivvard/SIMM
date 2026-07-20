<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('admin')->latest();

        // Filter by status
        if ($request->filled('status') && in_array($request->status, ['normal', 'warning', 'danger'])) {
            $query->where('status', $request->status);
        }

        // Filter by module
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Search by description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs = $query->paginate(20)->withQueryString();

        // Stats untuk summary cards
        $totalToday   = ActivityLog::whereDate('created_at', today())->count();
        $dangerCount  = ActivityLog::where('status', 'danger')->whereDate('created_at', today())->count();
        $warningCount = ActivityLog::where('status', 'warning')->whereDate('created_at', today())->count();

        return view('activity-logs.index', compact('logs', 'totalToday', 'dangerCount', 'warningCount'));
    }
}
