@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    {{-- Upcoming Schedules H-3 --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-panel">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="card-panel__title mb-0">
                            <i class="bi bi-calendar-event-fill me-2 text-warning"></i>
                            Upcoming Schedules (Next 3 Days)
                        </h6>
                    </div>
                    <a href="{{ route('schedules.index') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($upcomingSchedules->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-check fs-1 mb-2 d-block opacity-25"></i>
                            <p class="mb-0">No upcoming schedules in the next 3 days</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Motor</th>
                                        <th>Location</th>
                                        <th>Period</th>
                                        <th>Schedule Date</th>
                                        <th>Days Left</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingSchedules as $schedule)
                                        @php
                                            $daysLeft = now()->startOfDay()->diffInDays($schedule->schedule_date->startOfDay(), false);
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">
                                                    {{ $schedule->motor?->motor_code ?? 'Deleted Motor' }}
                                                    @if($schedule->motor?->trashed())
                                                        <span class="badge bg-secondary ms-1">Deleted</span>
                                                    @endif
                                                </div>
                                                <small class="text-muted">{{ $schedule->motor?->location ?? 'N/A' }}</small>
                                            </td>
                                            <td>
                                                @if($schedule->motor)
                                                    <span class="badge badge-location">{{ $schedule->motor->location }}
                                                        ({{ $schedule->motor->area }})</span>
                                                @else
                                                    <span class="badge badge-location bg-secondary">N/A</span>
                                                @endif
                                            </td>
                                            <td>{{ $schedule->period }}</td>
                                            <td>{{ $schedule->schedule_date->format('d M Y') }}</td>
                                            <td>
                                                @if($daysLeft === 0)
                                                    <span class="badge bg-danger">Today</span>
                                                @elseif($daysLeft === 1)
                                                    <span class="badge bg-warning text-dark">Tomorrow</span>
                                                @else
                                                    <span class="badge bg-info text-dark">{{ $daysLeft }} days</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('maintenance.create', ['schedule_id' => $schedule->id]) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="bi bi-tools me-1"></i>Input
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Reverb real-time alerts are wired in app.js --}}
@endpush