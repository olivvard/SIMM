<?php

namespace App\Http\Controllers;

use App\Models\Motor;
use Illuminate\Http\Request;

class MotorController extends Controller
{
    public function index(Request $request)
    {
        $query = Motor::withTrashed()->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('motor_code', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%");
            });
        }

        // By default show only non-deleted
        if (!$request->boolean('show_deleted')) {
            $query->whereNull('deleted_at');
        }

        $motors = $query->paginate(15)->withQueryString();

        return view('motors.index', compact('motors'));
    }

    public function create()
    {
        return view('motors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'motor_code'        => ['required', 'string', 'max:50', 'unique:motors,motor_code'],
            'location'          => ['required', 'in:MA-1,MA-2,MA-3,MA-4'],
            'area'              => ['required', 'in:DHDT,COOKER,HVU,DCU,RX,PL-2,PL-1,H2P,HCC,PLTU,WTP,HDC,EX-BOILER,JETTY-1,JETTY-3,LPG,JETTY-2,PUMP-HOUSE,SEPARATOR'],
            'category'          => ['required', 'in:<100,100-500,>500'],
        ]);

        Motor::create($validated);

        return redirect()->route('motors.index')
            ->with('success', 'Motor created successfully.');
    }

    public function show(Motor $motor)
    {
        $logs = $motor->maintenanceLogs()
            ->with(['schedule', 'admin', 'activityDetails.activity'])
            ->orderByDesc('inspection_date')
            ->get();

        return view('motors.show', compact('motor', 'logs'));
    }

    public function edit(Motor $motor)
    {
        return view('motors.edit', compact('motor'));
    }

    public function update(Request $request, Motor $motor)
    {
        $validated = $request->validate([
            'motor_code'        => ['required', 'string', 'max:50', "unique:motors,motor_code,{$motor->id}"],
            'location'          => ['required', 'in:MA-1,MA-2,MA-3,MA-4'],
            'area'              => ['required', 'in:DHDT,COOKER,HVU,DCU,RX,PL-2,PL-1,H2P,HCC,PLTU,WTP,HDC,EX-BOILER,JETTY-1,JETTY-3,LPG,JETTY-2,PUMP-HOUSE,SEPARATOR'],
            'category'          => ['required', 'in:<100,100-500,>500'],
        ]);

        $motor->update($validated);

        return redirect()->route('motors.index')
            ->with('success', 'Motor updated successfully.');
    }

    public function destroy(Motor $motor)
    {
        $motor->delete();

        return redirect()->route('motors.index')
            ->with('success', 'Motor soft-deleted successfully.');
    }

    public function restore(int $id)
    {
        $motor = Motor::withTrashed()->findOrFail($id);
        $motor->restore();

        return redirect()->route('motors.index')
            ->with('success', 'Motor restored successfully.');
    }

    public function forceDelete(int $id)
    {
        $motor = Motor::withTrashed()->findOrFail($id);
        $motor->forceDelete();

        return redirect()->route('motors.index')
            ->with('success', 'Motor permanently deleted.');
    }
}
