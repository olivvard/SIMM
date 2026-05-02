@extends('layouts.app')
@section('title', 'Maintenance Logs')
@section('page-title', 'Maintenance Logs')

@section('content')

{{-- Hidden POST form for selected PDF export --}}
<form id="pdfSelectionForm" method="POST" action="{{ route('reports.pdf.post') }}" target="_blank">
    @csrf
    {{-- selected log_ids are injected here dynamically by JS --}}
</form>

<div class="d-flex justify-content-between align-items-center mt-3 mb-4 flex-wrap gap-2">
    <p class="text-muted mb-0">All recorded maintenance logs.</p>

    <div class="d-flex align-items-center gap-2 flex-wrap">

        {{-- Default: Export PDF button --}}
        <button type="button" id="btnEnableSelect"
                class="btn btn-danger"
                onclick="enableSelectionMode()">
            <i class="bi bi-file-earmark-pdf-fill me-1"></i>Export PDF
        </button>

        {{-- Selection mode: Download + Cancel --}}
        <button type="button" id="btnDownloadSelected"
                class="btn btn-danger d-none"
                onclick="submitSelection()" disabled>
            <i class="bi bi-download me-1"></i>Download Selected
            <span id="selectedCount" class="badge bg-white text-danger ms-1">0</span>
        </button>
        <button type="button" id="btnCancelSelect"
                class="btn btn-outline-secondary d-none"
                onclick="disableSelectionMode()">
            <i class="bi bi-x me-1"></i>Cancel
        </button>

        {{-- Hint text --}}
        <span id="selectionHint" class="text-muted small d-none ms-1">
            <i class="bi bi-info-circle me-1"></i>
            Tick the rows you want, then click <strong>Download Selected</strong>.
        </span>

        <a href="{{ route('maintenance.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle-fill me-1"></i>New Maintenance Input
        </a>
    </div>
</div>

<div class="card card-panel">

    {{-- Card header with Select All (shown only in selection mode) --}}
    <div class="card-header d-flex align-items-center justify-content-between" style="min-height:48px;">
        <h6 class="card-panel__title mb-0">
            <i class="bi bi-journal-text me-2 text-primary"></i>
            Logs — {{ $logs->total() }} record(s)
        </h6>
        <div id="selectAllWrapper" class="d-none">
            <div class="form-check mb-0 d-flex align-items-center gap-2">
                <input class="form-check-input" type="checkbox" id="checkAll"
                       style="width:1.1rem;height:1.1rem;cursor:pointer;"
                       onchange="toggleAll(this)">
                <label class="form-check-label small fw-semibold" for="checkAll">Select All</label>
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
                        <th>Period</th>
                        <th>Admin</th>
                        <th>Activities</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $i => $log)
                    <tr class="log-row" data-id="{{ $log->id }}" onclick="handleRowClick(this)" style="cursor:default;">
                        {{-- Checkbox cell --}}
                        <td class="td-check d-none" style="width:40px;">
                            <input type="checkbox"
                                   class="form-check-input log-checkbox"
                                   value="{{ $log->id }}"
                                   style="width:1.1rem;height:1.1rem;cursor:pointer;"
                                   onchange="updateCount()">
                        </td>
                        <td>{{ $logs->firstItem() + $i }}</td>
                        <td>
                            <span class="fw-semibold">{{ $log->inspection_date?->format('d M Y') ?? '—' }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold text-primary">{{ $log->motor?->motor_code ?? '—' }}</div>
                            <small class="text-muted">{{ $log->motor?->location ?? '—' }}</small>
                        </td>
                        <td>{{ $log->schedule?->period ?? '—' }}</td>
                        <td>{{ $log->admin?->full_name ?? '—' }}</td>
                        <td>
                            @php
                                $done  = $log->activityDetails->where('is_done', true)->count();
                                $total = $log->activityDetails->count();
                                $pct   = $total > 0 ? round($done / $total * 100) : 0;
                            @endphp
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-fill" style="height:6px; min-width:60px;">
                                    <div class="progress-bar bg-success" style="width:{{ $pct }}%"></div>
                                </div>
                                <small class="text-muted">{{ $done }}/{{ $total }}</small>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('maintenance.show', $log) }}"
                                   class="btn btn-outline-info view-btn" title="View">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <form method="POST" action="{{ route('maintenance.destroy', $log) }}"
                                      onsubmit="return confirm('Delete this maintenance log?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x fs-1 d-block opacity-25 mb-2"></i>
                            No maintenance logs recorded yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($logs->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
        <small class="text-muted">Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }}</small>
        {{ $logs->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@push('scripts')
<script>
    let selectionMode = false;

    function enableSelectionMode() {
        selectionMode = true;

        document.getElementById('btnEnableSelect').classList.add('d-none');
        document.getElementById('btnDownloadSelected').classList.remove('d-none');
        document.getElementById('btnCancelSelect').classList.remove('d-none');
        document.getElementById('selectionHint').classList.remove('d-none');
        document.getElementById('selectAllWrapper').classList.remove('d-none');

        // Show checkbox column header + cells
        document.getElementById('thCheck').classList.remove('d-none');
        document.querySelectorAll('.td-check').forEach(td => td.classList.remove('d-none'));

        // Make rows feel clickable
        document.querySelectorAll('.log-row').forEach(row => row.style.cursor = 'pointer');

        updateCount();
    }

    function disableSelectionMode() {
        selectionMode = false;

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
        document.getElementById('checkAll').indeterminate = false;

        // Reset row styles
        document.querySelectorAll('.log-row').forEach(row => {
            row.style.cursor = 'default';
            row.classList.remove('table-primary');
        });

        updateCount();
    }

    function toggleAll(master) {
        document.querySelectorAll('.log-checkbox').forEach(cb => cb.checked = master.checked);
        document.querySelectorAll('.log-row').forEach(row => row.classList.toggle('table-primary', master.checked));
        updateCount();
    }

    function handleRowClick(row) {
        if (!selectionMode) return;

        const cb = row.querySelector('.log-checkbox');
        cb.checked = !cb.checked;
        row.classList.toggle('table-primary', cb.checked);

        const all     = document.querySelectorAll('.log-checkbox');
        const checked = document.querySelectorAll('.log-checkbox:checked');
        document.getElementById('checkAll').checked       = all.length === checked.length;
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

    // Prevent row-click from firing when clicking the view button, delete button, or checkbox directly
    document.querySelectorAll('.view-btn, .btn-outline-danger, .log-checkbox').forEach(el => {
        el.addEventListener('click', e => e.stopPropagation());
    });
</script>
@endpush

@endsection
