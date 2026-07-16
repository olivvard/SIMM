@extends('layouts.app')
@section('title', 'Add Schedule')
@section('page-title', 'Add Schedule')

@section('content')
<div class="row justify-content-center mt-3">
    <div class="col-lg-7">
        <div class="card card-panel">
            <div class="card-header">
                <h6 class="card-panel__title mb-0">
                    <i class="bi bi-calendar-plus-fill me-2 text-primary"></i>New Maintenance Schedule
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('schedules.store') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="motor_id" class="form-label fw-semibold">Motor <span class="text-danger">*</span></label>
                        <select id="motor_id" name="motor_id"
                            class="form-select @error('motor_id') is-invalid @enderror" required>
                            <option value="">— Select Motor —</option>
                            @foreach($motors as $motor)
                                <option value="{{ $motor->id }}" {{ old('motor_id') == $motor->id ? 'selected' : '' }}>
                                    {{ $motor->motor_code }}
                                </option>
                            @endforeach
                        </select>
                        @error('motor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label for="schedule_date" class="form-label fw-semibold">Schedule Date <span class="text-danger">*</span></label>
                        <input type="date" id="schedule_date" name="schedule_date"
                            class="form-control @error('schedule_date') is-invalid @enderror"
                            value="{{ old('schedule_date') }}" required>
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Dates in the past will be automatically marked as <strong>Overdue</strong>.
                        </div>
                        @error('schedule_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Save Schedule
                        </button>
                        <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
