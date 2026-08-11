<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceLog;
use App\Models\Schedule;
use App\Models\Motor;
use App\Models\Activity;
use App\Events\ScheduleAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaintenanceLogController extends Controller
{
    public function index()
    {
        $logs = MaintenanceLog::with(['motor', 'schedule', 'admin', 'activityDetails'])
            ->orderByDesc('inspection_date')
            ->paginate(15);

        return view('maintenance.index', compact('logs'));
    }

    public function create(Request $request)
    {
        $schedules  = Schedule::with('motor')
            ->where('status', '!=', 'done')
            ->orderBy('schedule_date')
            ->get();
        $activities = Activity::orderBy('activity_code')->get();

        $selectedSchedule = null;
        if ($request->filled('schedule_id')) {
            $selectedSchedule = Schedule::with('motor')->find($request->schedule_id);
        }

        return view('maintenance.create', compact('schedules', 'activities', 'selectedSchedule'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'schedule_id'          => ['required', 'exists:schedules,id'],
            'inspection_date'      => ['required', 'date'],
            'general_notes'        => ['nullable', 'string'],
            'photo'                => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'activities'           => ['required', 'array'],
            'activities.*.is_done' => ['nullable', 'boolean'],
            'activities.*.notes'   => ['nullable', 'string'],
        ]);

        $schedule = Schedule::with('motor')->findOrFail($validated['schedule_id']);

        // Handle photo upload
        $photoUrl = null;
        if ($request->hasFile('photo')) {
            $path     = $request->file('photo')->store('maintenance', 'public');
            $photoUrl = $path;
        }

        $adminId = Auth::id();

        // Buat maintenance log
        $log = MaintenanceLog::create([
            'motor_id'        => $schedule->motor_id,
            'schedule_id'     => $schedule->id,
            'admin_id'        => $adminId,
            'inspection_date' => $validated['inspection_date'],
            'general_notes'   => $validated['general_notes'] ?? null,
            'photo_url'       => $photoUrl,
        ]);

        // ── [DATA PROTECTION] DIGITAL SIGNATURE ─────────────────────────────
        $log->update(['digital_signature' => $log->generateSignature()]);

        // Create activity detail rows
        foreach ($request->input('activities', []) as $activityId => $data) {
            $log->activityDetails()->create([
                'activity_id' => $activityId,
                'is_done'     => isset($data['is_done']) ? (bool) $data['is_done'] : false,
                'notes'       => $data['notes'] ?? null,
            ]);
        }

        // Mark schedule as done
        $schedule->update(['status' => 'done']);

        // Broadcast ScheduleAlert event
        broadcast(new ScheduleAlert(
            scheduleId:   $schedule->id,
            motorCode:    $schedule->motor->motor_code,
            scheduleDate: $schedule->schedule_date->toDateString(),
            type:         'done'
        ))->toOthers();

        return redirect()->route('maintenance.index')
            ->with('success', 'Maintenance log recorded successfully.');
    }

    public function show(MaintenanceLog $maintenanceLog)
    {
        $maintenanceLog->load(['motor', 'schedule', 'admin', 'activityDetails.activity']);

        // ── [DATA PROTECTION] Verifikasi Digital Signature ──────────────────
        $isVerified = $maintenanceLog->verifySignature();

        return view('maintenance.show', compact('maintenanceLog', 'isVerified'));
    }

    public function destroy(MaintenanceLog $maintenanceLog)
    {
        // Delete photo if exists
        if ($maintenanceLog->photo_url) {
            Storage::disk('public')->delete($maintenanceLog->photo_url);
        }

        $maintenanceLog->delete();

        return redirect()->route('maintenance.index')
            ->with('success', 'Maintenance log deleted.');
    }
}
