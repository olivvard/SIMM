@extends('layouts.app')
@section('title', 'Edit Schedule')
@section('page-title', 'Edit Schedule')

@section('content')
<div class="row justify-content-center mt-3">
    <div class="col-lg-7">
        <div class="card card-panel">
            <div class="card-header">
                <h6 class="card-panel__title mb-0">
                    <i class="bi bi-calendar-check-fill me-2 text-warning"></i>Edit Schedule #{{ $schedule->id }}
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('schedules.update', $schedule) }}" novalidate>
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label for="motor_id" class="form-label fw-semibold">Motor <span class="text-danger">*</span></label>
                        <select id="motor_id" name="motor_id"
                            class="form-select @error('motor_id') is-invalid @enderror" required>
                            <option value="">— Select Motor —</option>
                            @foreach($motors as $motor)
                                <option value="{{ $motor->id }}"
                                    {{ old('motor_id', $schedule->motor_id) == $motor->id ? 'selected' : '' }}>
                                    {{ $motor->motor_code }}
                                </option>
                            @endforeach
                        </select>
                        @error('motor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="schedule_date" class="form-label fw-semibold">Schedule Date <span class="text-danger">*</span></label>
                        <input type="date" id="schedule_date" name="schedule_date"
                            class="form-control @error('schedule_date') is-invalid @enderror"
                            value="{{ old('schedule_date', $schedule->schedule_date->format('Y-m-d')) }}" required>
                        @error('schedule_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select id="status" name="status"
                            class="form-select @error('status') is-invalid @enderror" required>
                            <option value="pending" {{ old('status', $schedule->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="done"    {{ old('status', $schedule->status) === 'done'    ? 'selected' : '' }}>Done</option>
                            <option value="overdue" {{ old('status', $schedule->status) === 'overdue' ? 'selected' : '' }}>Overdue</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning text-dark fw-semibold">
                            <i class="bi bi-check-lg me-1"></i>Update Schedule
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
