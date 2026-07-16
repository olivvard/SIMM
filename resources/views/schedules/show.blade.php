@extends('layouts.app')
@section('title', 'Schedule #' . $schedule->id)
@section('page-title', 'Schedule Detail')

@section('content')
<div class="mt-3 row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-panel">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-panel__title mb-0">
                    <i class="bi bi-calendar-check-fill me-2 text-primary"></i>Schedule #{{ $schedule->id }}
                </h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('schedules.edit', $schedule) }}" class="btn btn-sm btn-outline-warning">
                        <i class="bi bi-pencil-fill me-1"></i>Edit
                    </a>
                    <a href="{{ route('schedules.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4 text-muted">Motor</dt>
                    <dd class="col-sm-8">
                        <span class="fw-semibold text-primary">
                            {{ $schedule->motor?->motor_code ?? 'Deleted Motor' }}
                            @if($schedule->motor?->trashed())
                                <span class="badge bg-secondary ms-1">Deleted</span>
                            @endif
                        </span>
                    </dd>

                    <dt class="col-sm-4 text-muted">Location</dt>
                    <dd class="col-sm-8">
                        @if($schedule->motor)
                            <span class="badge badge-location">{{ $schedule->motor->location }} ({{ $schedule->motor->area }})</span>
                        @else
                            <span class="badge badge-location bg-secondary">N/A</span>
                        @endif
                    </dd>


                    <dt class="col-sm-4 text-muted">Schedule Date</dt>
                    <dd class="col-sm-8 fw-semibold">{{ $schedule->schedule_date->format('d M Y') }}</dd>

                    <dt class="col-sm-4 text-muted">Status</dt>
                    <dd class="col-sm-8">
                        @php $badges = ['pending'=>'bg-warning text-dark','done'=>'bg-success','overdue'=>'bg-danger']; @endphp
                        <span class="badge {{ $badges[$schedule->status] ?? 'bg-secondary' }}">
                            {{ ucfirst($schedule->status) }}
                        </span>
                    </dd>
                </dl>

                @if($schedule->status !== 'done')
                    <hr>
                    <a href="{{ route('maintenance.create', ['schedule_id' => $schedule->id]) }}"
                       class="btn btn-primary">
                        <i class="bi bi-tools me-1"></i>Input Maintenance
                    </a>
                @endif
            </div>
        </div>

        {{-- Linked Maintenance Logs --}}
        @if($schedule->maintenanceLogs->isNotEmpty())
        <div class="card card-panel mt-4">
            <div class="card-header">
                <h6 class="card-panel__title mb-0">
                    <i class="bi bi-journal-text me-2 text-success"></i>Linked Maintenance Logs
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Inspection Date</th>
                            <th>Admin</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedule->maintenanceLogs as $log)
                        <tr>
                            <td>{{ $log->inspection_date?->format('d M Y') ?? '—' }}</td>
                            <td>{{ $log->admin->full_name }}</td>
                            <td class="text-center">
                                <a href="{{ route('maintenance.show', $log) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
