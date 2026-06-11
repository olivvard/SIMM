@extends('layouts.app')
@section('title', 'Reports')
@section('page-title', 'Maintenance Reports')

@section('content')
<div class="mt-3">

    {{-- Filter Card --}}
    <div class="card card-panel mb-4">
        <div class="card-header">
            <h6 class="card-panel__title mb-0">
                <i class="bi bi-funnel-fill me-2 text-primary"></i>Filter Reports
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="month" class="form-label fw-semibold small">Month / Year</label>
                    <input type="month" id="month" name="month"
                        class="form-control"
                        value="{{ request('month') }}">
                </div>
                <div class="col-md-4">
                    <label for="motor_id" class="form-label fw-semibold small">Motor (optional)</label>
                    <select id="motor_id" name="motor_id" class="form-select">
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
                        <i class="bi bi-search me-1"></i>Search
                    </button>
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary flex-fill">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Export Buttons --}}
    @if($logs->isNotEmpty())

    {{-- Hidden POST form for selected PDF export --}}
    <form id="pdfSelectionForm" method="POST" action="{{ route('reports.pdf.post') }}" target="_blank">
        @csrf
        {{-- selected log_ids are injected here dynamically by JS --}}
    </form>

    <div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
        {{-- Default state: Export PDF button --}}
        <button type="button" id="btnEnableSelect"
                class="btn btn-danger"
                onclick="enableSelectionMode()">
            <i class="bi bi-file-earmark-pdf-fill me-1"></i>Export PDF
        </button>

        {{-- Selection mode buttons (hidden by default) --}}
        <button type="button" id="btnDownloadSelected"
                class="btn btn-danger d-none"
                onclick="submitSelection()">
            <i class="bi bi-download me-1"></i>Download Selected
            <span id="selectedCount" class="badge bg-white text-danger ms-1">0</span>
        </button>
        <button type="button" id="btnCancelSelect"
                class="btn btn-outline-secondary d-none"
                onclick="disableSelectionMode()">
            <i class="bi bi-x me-1"></i>Cancel
        </button>

        <a href="{{ route('reports.excel', request()->all()) }}"
           class="btn btn-success">
            <i class="bi bi-file-earmark-spreadsheet-fill me-1"></i>Export Excel
        </a>

        {{-- Selection mode hint --}}
        <span id="selectionHint" class="text-muted small d-none ms-2">
            <i class="bi bi-info-circle me-1"></i>
            Check the rows you want to include, then click <strong>Download Selected</strong>.
        </span>
    </div>
    @endif

    @if($logs->isEmpty())
        <div class="card card-panel">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block opacity-25 mb-2"></i>
                No maintenance records found.
            </div>
        </div>
    @else
        <div class="card card-panel">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="card-panel__title mb-0">
                    <i class="bi bi-table me-2 text-info"></i>
                    Results — {{ $logs->count() }} record(s)
                </h6>
                {{-- Select All (only visible in selection mode) --}}
                <div id="selectAllWrapper" class="d-none">
                    <div class="form-check mb-0 d-flex align-items-center gap-2">
                        <input class="form-check-input" type="checkbox" id="checkAll"
                               style="width:1.1rem;height:1.1rem;cursor:pointer;"
                               onchange="toggleAll(this)">
                        <label class="form-check-label small fw-semibold" for="checkAll">
                            Select All
                        </label>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                {{-- Checkbox column — hidden until selection mode --}}
                                <th id="thCheck" class="d-none" style="width:40px;"></th>
                                <th>#</th>
                                <th>Inspection Date</th>
                                <th>Motor</th>
                                <th>Location</th>
                                <th>Period</th>
                                <th>Admin</th>
                                <th>Activities</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $i => $log)
                            @php
                                $done  = $log->activityDetails->where('is_done', true)->count();
                                $total = $log->activityDetails->count();
                                $pct   = $total > 0 ? round($done / $total * 100) : 0;
                            @endphp
                            <tr class="log-row" data-id="{{ $log->id }}" onclick="handleRowClick(this)" style="cursor:default;">
                                {{-- Checkbox cell (hidden until selection mode) --}}
                                <td class="td-check d-none" style="width:40px;">
                                    <input type="checkbox"
                                           class="form-check-input log-checkbox"
                                           value="{{ $log->id }}"
                                           style="width:1.1rem;height:1.1rem;cursor:pointer;"
                                           onchange="updateCount()">
                                </td>
                                <td>{{ $i + 1 }}</td>
                                <td class="fw-semibold">{{ $log->inspection_date?->format('d M Y') ?? '—' }}</td>
                                <td>
                                    <div class="fw-semibold text-primary">{{ $log->motor?->motor_code ?? '—' }}</div>
                                    <small class="text-muted">{{ $log->motor?->location ?? '—' }}</small>
                                </td>
                                <td><span class="badge badge-location">{{ $log->motor?->location ?? '—' }} ({{ $log->motor?->area ?? '—' }})</span></td>
                                <td>{{ $log->schedule?->period ?? '—' }}</td>
                                <td>{{ $log->admin?->full_name ?? '—' }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-fill" style="height:6px; min-width:60px;">
                                            <div class="progress-bar bg-success" style="width:{{ $pct }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $done }}/{{ $total }}</small>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('maintenance.show', $log) }}"
                                       class="btn btn-sm btn-outline-info view-btn">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    let selectionMode = false;

    function enableSelectionMode() {
        selectionMode = true;

        // Swap buttons
        document.getElementById('btnEnableSelect').classList.add('d-none');
        document.getElementById('btnDownloadSelected').classList.remove('d-none');
        document.getElementById('btnCancelSelect').classList.remove('d-none');
        document.getElementById('selectionHint').classList.remove('d-none');
        document.getElementById('selectAllWrapper').classList.remove('d-none');

        // Show checkbox column
        document.getElementById('thCheck').classList.remove('d-none');
        document.querySelectorAll('.td-check').forEach(td => td.classList.remove('d-none'));

        // Make rows clickable
        document.querySelectorAll('.log-row').forEach(row => {
            row.style.cursor = 'pointer';
        });

        updateCount();
    }

    function disableSelectionMode() {
        selectionMode = false;

        // Swap buttons back
        document.getElementById('btnEnableSelect').classList.remove('d-none');
        document.getElementById('btnDownloadSelected').classList.add('d-none');
        document.getElementById('btnCancelSelect').classList.add('d-none');
        document.getElementById('selectionHint').classList.add('d-none');
        document.getElementById('selectAllWrapper').classList.add('d-none');

        // Hide checkbox column & uncheck all
        document.getElementById('thCheck').classList.add('d-none');
        document.querySelectorAll('.td-check').forEach(td => td.classList.add('d-none'));
        document.querySelectorAll('.log-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('checkAll').checked = false;

        // Reset row styles
        document.querySelectorAll('.log-row').forEach(row => {
            row.style.cursor = 'default';
            row.classList.remove('table-primary');
        });

        updateCount();
    }

    function toggleAll(master) {
        document.querySelectorAll('.log-checkbox').forEach(cb => {
            cb.checked = master.checked;
        });
        document.querySelectorAll('.log-row').forEach(row => {
            row.classList.toggle('table-primary', master.checked);
        });
        updateCount();
    }

    function handleRowClick(row) {
        if (!selectionMode) return;

        // Don't trigger when clicking the checkbox or view button directly
        const cb = row.querySelector('.log-checkbox');
        cb.checked = !cb.checked;
        row.classList.toggle('table-primary', cb.checked);

        // Sync "select all" state
        const all = document.querySelectorAll('.log-checkbox');
        const checked = document.querySelectorAll('.log-checkbox:checked');
        document.getElementById('checkAll').checked = all.length === checked.length;
        document.getElementById('checkAll').indeterminate = checked.length > 0 && checked.length < all.length;

        updateCount();
    }

    function updateCount() {
        const count = document.querySelectorAll('.log-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = count;

        const btn = document.getElementById('btnDownloadSelected');
        btn.disabled = count === 0;
        btn.classList.toggle('opacity-50', count === 0);
    }

    function submitSelection() {
        const selected = [...document.querySelectorAll('.log-checkbox:checked')].map(cb => cb.value);
        if (selected.length === 0) {
            alert('Please select at least one inspection to export.');
            return;
        }

        const form = document.getElementById('pdfSelectionForm');

        // Remove any previously injected hidden inputs
        form.querySelectorAll('input[name="log_ids[]"]').forEach(el => el.remove());

        // Inject selected IDs
        selected.forEach(id => {
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = 'log_ids[]';
            input.value = id;
            form.appendChild(input);
        });

        form.submit();
    }

    // Stop row click from triggering when clicking the view button or checkbox directly
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', e => e.stopPropagation());
    });
    document.querySelectorAll('.log-checkbox').forEach(cb => {
        cb.addEventListener('click', e => e.stopPropagation());
    });
</script>
@endpush
@endsection
