@extends('layouts.app')
@section('title', 'New Maintenance Input')
@section('page-title', 'Maintenance Input')

@section('content')
<div class="mt-3">
    <div class="card card-panel">
        <div class="card-header">
            <h6 class="card-panel__title mb-0">
                <i class="bi bi-tools me-2 text-primary"></i>Record Maintenance
            </h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('maintenance.store') }}" enctype="multipart/form-data" novalidate>
                @csrf

                {{-- Schedule & Motor selection --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="schedule_id" class="form-label fw-semibold">
                            Schedule <span class="text-danger">*</span>
                        </label>
                        <select id="schedule_id" name="schedule_id"
                            class="form-select @error('schedule_id') is-invalid @enderror" required>
                            <option value="">— Select Schedule —</option>
                            @foreach($schedules as $schedule)
                                <option value="{{ $schedule->id }}"
                                    data-motor-code="{{ $schedule->motor?->motor_code ?? '' }}"
                                    data-period="{{ $schedule->period }}"
                                    {{ (old('schedule_id', optional($selectedSchedule)->id) == $schedule->id) ? 'selected' : '' }}>
                                    [{{ ucfirst($schedule->status) }}] {{ $schedule->motor?->motor_code ?? 'Deleted Motor' }} —
                                    {{ $schedule->period }} — {{ $schedule->schedule_date->format('d M Y') }}
                                </option>
                            @endforeach
                        </select>
                        @error('schedule_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Motor Code</label>
                        <input type="text" id="auto-motor-code" class="form-control bg-light" readonly
                               value="{{ optional($selectedSchedule)->motor?->motor_code ?? '' }}">
                    </div>

                    <div class="col-md-4">
                        <label for="inspection_date" class="form-label fw-semibold">
                            Inspection Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" id="inspection_date" name="inspection_date"
                            class="form-control @error('inspection_date') is-invalid @enderror"
                            value="{{ old('inspection_date', date('Y-m-d')) }}" required>
                        @error('inspection_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-8">
                        <label for="general_notes" class="form-label fw-semibold">General Notes</label>
                        <textarea id="general_notes" name="general_notes" rows="2"
                            class="form-control @error('general_notes') is-invalid @enderror"
                            placeholder="Overall condition, issues found…">{{ old('general_notes') }}</textarea>
                        @error('general_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="photo" class="form-label fw-semibold">Photo (optional)</label>
                        <input type="file" id="photo" name="photo" accept="image/*"
                            class="form-control @error('photo') is-invalid @enderror">
                        <div class="form-text">Max 4 MB. JPG, PNG, WEBP.</div>
                        @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Activity Checklist --}}
                <h6 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-list-check me-2 text-success"></i>Activity Checklist (15 Items)
                </h6>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="activity-table">
                        <thead class="table-dark">
                            <tr>
                                <th width="70">Code</th>
                                <th>Activity Name</th>
                                <th width="100">Category</th>
                                <th width="80" class="text-center">Done</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activities as $activity)
                            <tr>
                                <td class="fw-semibold text-primary small">{{ $activity->activity_code }}</td>
                                <td class="small">{{ $activity->activity_name }}</td>
                                <td>
                                    <span class="badge badge-category-{{ strtolower($activity->category) }}">
                                        {{ $activity->category }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-check-inline m-0">
                                        <input class="form-check-input activity-check"
                                               type="checkbox"
                                               name="activities[{{ $activity->id }}][is_done]"
                                               value="1"
                                               id="done_{{ $activity->id }}"
                                               {{ old("activities.{$activity->id}.is_done") ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td>
                                    <input type="text"
                                           name="activities[{{ $activity->id }}][notes]"
                                           class="form-control form-control-sm"
                                           placeholder="Notes…"
                                           value="{{ old("activities.{$activity->id}.notes") }}">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="3" class="text-end fw-semibold small">Checked:</td>
                                <td class="text-center fw-bold" id="checked-count">0 / {{ $activities->count() }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-success" id="check-all">
                                        <i class="bi bi-check-all me-1"></i>Check All
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary ms-1" id="uncheck-all">
                                        <i class="bi bi-x-lg me-1"></i>Uncheck All
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-save-fill me-1"></i>Save Maintenance Log
                    </button>
                    <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-arrow-left me-1"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-fill motor fields when schedule is selected
const scheduleSelect = document.getElementById('schedule_id');
const motorCode      = document.getElementById('auto-motor-code');

function fillMotor() {
    const opt = scheduleSelect.options[scheduleSelect.selectedIndex];
    motorCode.value = opt.dataset.motorCode || '';
}

scheduleSelect.addEventListener('change', fillMotor);
// Run on load (if schedule pre-selected)
if (scheduleSelect.value) fillMotor();

// Check/uncheck all activities
const checks = () => document.querySelectorAll('.activity-check');
const counter = document.getElementById('checked-count');
const total   = {{ $activities->count() }};

function updateCount() {
    const done = [...checks()].filter(c => c.checked).length;
    counter.textContent = done + ' / ' + total;
}

document.getElementById('check-all').addEventListener('click', function () {
    checks().forEach(c => c.checked = true);
    updateCount();
});
document.getElementById('uncheck-all').addEventListener('click', function () {
    checks().forEach(c => c.checked = false);
    updateCount();
});
checks().forEach(c => c.addEventListener('change', updateCount));
updateCount();
</script>
@endpush
