@extends('layouts.app')
@section('title', 'Maintenance Logs')
@section('page-title', 'Maintenance Logs')

@section('content')


<div class="d-flex justify-content-between align-items-center mt-3 mb-4 flex-wrap gap-2">
    <p class="text-muted mb-0">All recorded maintenance logs.</p>

    <div class="d-flex align-items-center gap-2">
        @if(Auth::user()->isTeknisi())
        <a href="{{ route('maintenance.create') }}" class="btn btn-primary">
            <i class="fa fa-plus-square"></i> New Maintenance Input
        </a>
        @endif
    </div>
</div>

<div class="card card-panel">

    {{-- Card header with Select All (shown only in selection mode) --}}
    <div class="card-header d-flex align-items-center justify-content-between" style="min-height:48px;">
        <h6 class="card-panel__title mb-0">
            <i class="bi bi-journal-text me-2 text-primary"></i>
            Logs — {{ $logs->total() }} record(s)
        </h6>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
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
                    <tr>
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
                                    <i class="fa fa-eye"></i>
                                </a>
                                @if(Auth::user()->isTeknisi())
                                <form method="POST" action="{{ route('maintenance.destroy', $log) }}"
                                      onsubmit="return confirm('Delete this maintenance log?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
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
    // Prevent row-click from navigating when clicking action buttons
    document.querySelectorAll('.view-btn, .btn-outline-danger').forEach(el => {
        el.addEventListener('click', e => e.stopPropagation());
    });
</script>
@endpush

@endsection
