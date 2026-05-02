@extends('layouts.app')
@section('title', 'Maintenance Log Detail')
@section('page-title', 'Maintenance Log Detail')

@section('content')
<div class="mt-3">
    <div class="row g-4 mb-4">
        {{-- Header Info --}}
        <div class="col-lg-6">
            <div class="card card-panel h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-panel__title mb-0">
                        <i class="bi bi-file-text-fill me-2 text-primary"></i>Log Information
                    </h6>
                    <a href="{{ route('maintenance.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back
                    </a>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5 text-muted">Inspection Date</dt>
                        <dd class="col-sm-7 fw-semibold">{{ $maintenanceLog->inspection_date?->format('d M Y') ?? '—' }}</dd>

                        <dt class="col-sm-5 text-muted">Motor</dt>
                        <dd class="col-sm-7">
                            <span class="text-primary fw-semibold">{{ $maintenanceLog->motor?->motor_code ?? '—' }}</span>
                        </dd>

                        <dt class="col-sm-5 text-muted">Location</dt>
                        <dd class="col-sm-7"><span class="badge badge-location">{{ $maintenanceLog->motor?->location ?? '—' }} ({{ $maintenanceLog->motor?->area ?? '—' }})</span></dd>

                        <dt class="col-sm-5 text-muted">Period</dt>
                        <dd class="col-sm-7">{{ $maintenanceLog->schedule?->period ?? '—' }}</dd>

                        <dt class="col-sm-5 text-muted">Admin</dt>
                        <dd class="col-sm-7">{{ $maintenanceLog->admin?->full_name ?? '—' }}</dd>

                        <dt class="col-sm-5 text-muted">General Notes</dt>
                        <dd class="col-sm-7 text-muted">{{ $maintenanceLog->general_notes ?? '—' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Photo --}}
        <div class="col-lg-6">
            <div class="card card-panel h-100">
                <div class="card-header">
                    <h6 class="card-panel__title mb-0">
                        <i class="bi bi-camera-fill me-2 text-info"></i>Maintenance Photo
                    </h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    @if($maintenanceLog->photo_url)
                        <a href="{{ Storage::url($maintenanceLog->photo_url) }}" target="_blank">
                            <img src="{{ Storage::url($maintenanceLog->photo_url) }}"
                                 alt="Maintenance Photo"
                                 class="img-fluid rounded shadow-sm"
                                 style="max-height: 220px; object-fit: cover;">
                        </a>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-image fs-1 opacity-25 d-block mb-2"></i>
                            No photo uploaded
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Activity Checklist --}}
    <div class="card card-panel">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="card-panel__title mb-0">
                <i class="bi bi-list-check me-2 text-success"></i>Activity Checklist
            </h6>
            @php
                $done  = $maintenanceLog->activityDetails->where('is_done', true)->count();
                $total = $maintenanceLog->activityDetails->count();
            @endphp
            <span class="badge bg-primary">{{ $done }}/{{ $total }} completed</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th width="70">Code</th>
                            <th>Activity Name</th>
                            <th width="110">Category</th>
                            <th width="70" class="text-center">Done</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($maintenanceLog->activityDetails->sortBy('activity.activity_code') as $detail)
                        <tr class="{{ $detail->is_done ? '' : 'table-danger bg-opacity-10' }}">
                            <td class="fw-semibold text-primary small">{{ $detail->activity->activity_code }}</td>
                            <td class="small">{{ $detail->activity->activity_name }}</td>
                            <td>
                                <span class="badge badge-category-{{ strtolower($detail->activity->category) }}">
                                    {{ $detail->activity->category }}
                                </span>
                            </td>
                            <td class="text-center fs-5">
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
@endsection
