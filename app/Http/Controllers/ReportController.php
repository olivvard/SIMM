<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceLog;
use App\Models\Motor;
use App\Exports\MaintenanceExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $motors = Motor::orderBy('motor_code')->get();

        $query = MaintenanceLog::with(['motor', 'schedule', 'admin', 'activityDetails.activity'])
            ->orderByDesc('inspection_date');

        if ($request->filled('month')) {
            [$year, $month] = explode('-', $request->month);
            $query->whereYear('inspection_date', $year)
                  ->whereMonth('inspection_date', $month);
        }

        if ($request->filled('motor_id')) {
            $query->where('motor_id', $request->motor_id);
        }

        $logs = $query->get();

        return view('reports.index', compact('motors', 'logs'));
    }

    public function exportPdf(Request $request)
    {
        // POST with specific log IDs selected by the user
        if ($request->isMethod('post') && $request->filled('log_ids')) {
            $logs = MaintenanceLog::with(['motor', 'schedule', 'admin', 'activityDetails.activity'])
                ->whereIn('id', $request->input('log_ids'))
                ->orderByDesc('inspection_date')
                ->get();

            $month      = $logs->first()?->inspection_date?->format('Y-m') ?? now()->format('Y-m');
            $motorLabel = $logs->pluck('motor.motor_code')->filter()->unique()->join(', ') ?: 'Selected';
        } else {
            // Fallback: GET with filter params
            $query = MaintenanceLog::with(['motor', 'schedule', 'admin', 'activityDetails.activity'])
                ->orderByDesc('inspection_date');

            if ($request->filled('month')) {
                [$year, $month2] = explode('-', $request->month);
                $query->whereYear('inspection_date', $year)
                      ->whereMonth('inspection_date', $month2);
            }

            if ($request->filled('motor_id')) {
                $query->where('motor_id', $request->motor_id);
            }

            $logs       = $query->get();
            $month      = $request->month ?? now()->format('Y-m');
            $motorLabel = $request->filled('motor_id')
                ? Motor::withTrashed()->find($request->motor_id)?->motor_code
                : 'All Motors';
        }

        $pdf = Pdf::loadView('reports.pdf', compact('logs', 'month', 'motorLabel'))
            ->setPaper('a4', 'potrait');

        return $pdf->stream("maintenance-report-{$month}.pdf");
    }

    public function exportExcel(Request $request)
    {
        $month    = $request->month ?? Carbon::now()->format('Y-m');
        $motorId  = $request->motor_id;

        return Excel::download(
            new MaintenanceExport($month, $motorId),
            "maintenance-report-{$month}.xlsx"
        );
    }
}
