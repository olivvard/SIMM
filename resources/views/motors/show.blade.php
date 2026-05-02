@extends('layouts.app')
@section('title', $motor->motor_code . ' — Detail')
@section('page-title', 'Motor Detail')

@php use Illuminate\Support\Facades\Storage; @endphp

@section('content')
<div class="mt-3">
    {{-- Motor Info Card --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-5">
            <div class="card card-panel h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="card-panel__title mb-0">
                        <i class="bi bi-lightning-charge-fill me-2 text-primary"></i>Motor Info
                    </h6>
                    <a href="{{ route('motors.edit', $motor) }}" class="btn btn-sm btn-outline-warning">
                        <i class="bi bi-pencil-fill me-1"></i>Edit
                    </a>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5 text-muted">Motor Code</dt>
                        <dd class="col-sm-7 fw-semibold text-primary">{{ $motor->motor_code }}</dd>

                        <dt class="col-sm-5 text-muted">Location</dt>
                        <dd class="col-sm-7"><span class="badge badge-location">{{ $motor->location }} ({{ $motor->area }})</span></dd>

                        <dt class="col-sm-5 text-muted">Category</dt>
                        <dd class="col-sm-7">{{ $motor->category }} HP</dd>

                        <dt class="col-sm-5 text-muted">Install Date</dt>
                        <dd class="col-sm-7">{{ $motor->installation_date->format('d M Y') }}</dd>

                        <dt class="col-sm-5 text-muted">Status</dt>
                        <dd class="col-sm-7">
                            @if($motor->trashed())
                                <span class="badge bg-secondary">Deleted</span>
                            @else
                                <span class="badge bg-success">Active</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card card-panel h-100">
                <div class="card-header">
                    <h6 class="card-panel__title mb-0">
                        <i class="bi bi-bar-chart-fill me-2 text-success"></i>Maintenance Summary
                    </h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="row text-center g-3">
                        <div class="col-4">
                            <div class="p-3 rounded-3 bg-primary bg-opacity-10">
                                <div class="fs-2 fw-bold text-primary">{{ $logs->count() }}</div>
                                <div class="small text-muted">Total Logs</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 rounded-3 bg-success bg-opacity-10">
                                <div class="fs-2 fw-bold text-success">{{ $motor->schedules->where('status','done')->count() }}</div>
                                <div class="small text-muted">Done</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 rounded-3 bg-danger bg-opacity-10">
                                <div class="fs-2 fw-bold text-danger">{{ $motor->schedules->where('status','overdue')->count() }}</div>
                                <div class="small text-muted">Overdue</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Maintenance History --}}
    <div class="card card-panel">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="card-panel__title mb-0">
                <i class="bi bi-clock-history me-2 text-info"></i>Maintenance History
            </h6>
            @php $pendingSchedule = $motor->schedules->where('status','pending')->first(); @endphp
            @if($pendingSchedule)
            <a href="{{ route('maintenance.create', ['schedule_id' => $pendingSchedule->id]) }}"
               class="btn btn-sm btn-primary">
                <i class="bi bi-plus me-1"></i>New Log
            </a>
            @endif
        </div>
        <div class="card-body p-0">
            @if($logs->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-journal-x fs-1 d-block opacity-25 mb-2"></i>
                    No maintenance logs yet.
                </div>
            @else
                <div class="accordion accordion-flush" id="logsAccordion">
                    @foreach($logs as $i => $log)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}" type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#log-{{ $log->id }}"
                                aria-expanded="{{ $i === 0 ? 'true' : 'false' }}">
                                <div class="d-flex align-items-center gap-3 w-100 me-3">
                                    <span class="badge bg-primary">{{ $log->inspection_date->format('d M Y') }}</span>
                                    <span class="fw-semibold">{{ $log->schedule->period ?? 'N/A' }}</span>
                                    <span class="text-muted small ms-auto">By: {{ $log->admin->full_name }}</span>
                                </div>
                            </button>
                        </h2>
                        <div id="log-{{ $log->id }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}">
                            <div class="accordion-body">
                                @if($log->general_notes)
                                    <div class="mb-3">
                                        <strong>General Notes:</strong>
                                        <p class="text-muted mb-0">{{ $log->general_notes }}</p>
                                    </div>
                                @endif

                                @if($log->photo_url)
                                    <div class="mb-3">
                                        <strong>Photo:</strong><br>
                                        <a href="{{ Storage::url($log->photo_url) }}" target="_blank">
                                            <img src="{{ Storage::url($log->photo_url) }}"
                                                 alt="Maintenance Photo"
                                                 class="img-thumbnail mt-1"
                                                 style="max-height: 180px; object-fit: cover;">
                                        </a>
                                    </div>
                                @endif

                                <strong>Activity Checklist:</strong>
                                <div class="table-responsive mt-2">
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="80">Code</th>
                                                <th>Activity</th>
                                                <th width="90">Category</th>
                                                <th width="70" class="text-center">Done</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($log->activityDetails->sortBy('activity.activity_code') as $detail)
                                            <tr>
                                                <td class="fw-semibold text-primary small">{{ $detail->activity->activity_code }}</td>
                                                <td class="small">{{ $detail->activity->activity_name }}</td>
                                                <td>
                                                    <span class="badge badge-category-{{ strtolower($detail->activity->category) }}">
                                                        {{ $detail->activity->category }}
                                                    </span>
                                                </td>
                                                <td class="text-center fs-6">
                                                    @if($detail->is_done)
                                                        <i class="bi bi-check-circle-fill text-success"></i>
                                                    @else
                                                        <i class="bi bi-x-circle-fill text-danger"></i>
                                                    @endif
                                                </td>
                                                <td class="small text-muted">{{ $detail->notes ?? '—' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
