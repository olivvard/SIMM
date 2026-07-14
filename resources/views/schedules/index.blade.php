@extends('layouts.app')
@section('title', 'Schedules')
@section('page-title', 'Maintenance Schedules')

@section('content')
<div class="d-flex justify-content-between align-items-center mt-3 mb-4">
    <p class="text-muted mb-0">Manage preventive maintenance schedules for all motors.</p>
    <a href="{{ route('schedules.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle-fill me-1"></i> Add Schedule
    </a>
</div>

{{-- Filters --}}
<div class="card card-panel mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('schedules.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Filter by Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                    <option value="done"     {{ request('status') === 'done'     ? 'selected' : '' }}>Done</option>
                    <option value="overdue"  {{ request('status') === 'overdue'  ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Filter by Motor</label>
                <select name="motor_id" class="form-select">
                    <option value="">All Motors</option>
                    @foreach($motors as $motor)
                        <option value="{{ $motor->id }}" {{ request('motor_id') == $motor->id ? 'selected' : '' }}>
                            {{ $motor->motor_code }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="bi bi-funnel-fill me-1"></i>Filter
                </button>
                <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary flex-fill">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card card-panel">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Motor</th>
                        <th>Period</th>
                        <th>Schedule Date</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $i => $schedule)
                    <tr>
                        <td>{{ $schedules->firstItem() + $i }}</td>
                        <td>
                            <div class="fw-semibold">
                                {{ $schedule->motor?->motor_code ?? 'Deleted Motor' }}
                                @if($schedule->motor?->trashed())
                                    <span class="badge bg-secondary ms-1">Deleted</span>
                                @endif
                            </div>
                        </td>
                        <td>{{ $schedule->period }}</td>
                        <td>{{ $schedule->schedule_date->format('d M Y') }}</td>
                        <td>
                            @php
                                $badges = [
                                    'pending' => 'bg-warning text-dark',
                                    'done'    => 'bg-success',
                                    'overdue' => 'bg-danger',
                                ];
                            @endphp
                            <span class="badge {{ $badges[$schedule->status] ?? 'bg-secondary' }}">
                                {{ ucfirst($schedule->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                @if($schedule->status !== 'done')
                                    <a href="{{ route('maintenance.create', ['schedule_id' => $schedule->id]) }}"
                                       class="btn btn-outline-primary" title="Input Maintenance">
                                        <i class="icon-eye"></i>
                                    </a>
                                @endif
                                <a href="{{ route('schedules.edit', $schedule) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="icon-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('schedules.destroy', $schedule) }}"
                                      onsubmit="return confirm('Delete this schedule?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                        <i class="icon-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="icon-eye fs-1 d-block opacity-25 mb-2"></i>
                            No schedules found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($schedules->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
        <small class="text-muted">Showing {{ $schedules->firstItem() }}–{{ $schedules->lastItem() }} of {{ $schedules->total() }}</small>
        {{ $schedules->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
