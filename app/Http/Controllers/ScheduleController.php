<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Motor;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = Schedule::with('motor')->latest('schedule_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('motor_id')) {
            $query->where('motor_id', $request->motor_id);
        }

        $schedules = $query->paginate(15)->withQueryString();
        $motors    = Motor::orderBy('motor_code')->get();

        return view('schedules.index', compact('schedules', 'motors'));
    }

    public function create()
    {
        $motors = Motor::orderBy('motor_code')->get();

        return view('schedules.create', compact('motors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'motor_id'      => ['required', 'exists:motors,id'],
            'schedule_date' => ['required', 'date'],
        ]);

        // Auto-set overdue if date is in the past
        $status = Carbon::parse($validated['schedule_date'])->lt(Carbon::today())
            ? 'overdue'
            : 'pending';

        Schedule::create(array_merge($validated, ['status' => $status]));

        return redirect()->route('schedules.index')
            ->with('success', 'Schedule created successfully.');
    }

    public function show(Schedule $schedule)
    {
        $schedule->load('motor', 'maintenanceLogs.admin');

        return view('schedules.show', compact('schedule'));
    }

    public function edit(Schedule $schedule)
    {
        $motors = Motor::orderBy('motor_code')->get();

        return view('schedules.edit', compact('schedule', 'motors'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'motor_id'      => ['required', 'exists:motors,id'],
            'schedule_date' => ['required', 'date'],
            'status'        => ['required', 'in:pending,done,overdue'],
        ]);

        $schedule->update($validated);

        return redirect()->route('schedules.index')
            ->with('success', 'Schedule updated successfully.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('schedules.index')
            ->with('success', 'Schedule deleted successfully.');
    }
}
