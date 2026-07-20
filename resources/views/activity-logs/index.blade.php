@extends('layouts.app')
@section('title', 'Activity Log')
@section('page-title', 'Activity Log')

@section('content')
<div class="row mt-4">

    {{-- ── Summary Cards ─────────────────────────────────────────────────── --}}
    <div class="col-xl-4 col-sm-6">
        <div class="card card-panel" style="border-left: 4px solid #3b82f6;">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div style="background:#eff6ff; border-radius:12px; width:48px; height:48px;
                            display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i data-feather="list" style="color:#3b82f6; width:22px; height:22px;"></i>
                </div>
                <div>
                    <p class="mb-0 text-muted" style="font-size:12px;">Total Aktivitas Hari Ini</p>
                    <h4 class="mb-0 fw-bold">{{ $totalToday }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-sm-6">
        <div class="card card-panel" style="border-left: 4px solid #f59e0b;">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div style="background:#fffbeb; border-radius:12px; width:48px; height:48px;
                            display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i data-feather="alert-triangle" style="color:#f59e0b; width:22px; height:22px;"></i>
                </div>
                <div>
                    <p class="mb-0 text-muted" style="font-size:12px;">Warning Hari Ini</p>
                    <h4 class="mb-0 fw-bold" style="color:#f59e0b;">{{ $warningCount }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-sm-6">
        <div class="card card-panel" style="border-left: 4px solid #ef4444;">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div style="background:#fef2f2; border-radius:12px; width:48px; height:48px;
                            display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i data-feather="shield-off" style="color:#ef4444; width:22px; height:22px;"></i>
                </div>
                <div>
                    <p class="mb-0 text-muted" style="font-size:12px;">Illegal / Bahaya Hari Ini</p>
                    <h4 class="mb-0 fw-bold" style="color:#ef4444;">{{ $dangerCount }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Filter Bar ──────────────────────────────────────────────────────── --}}
    <div class="col-12">
        <div class="card card-panel">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('activity-logs.index') }}"
                      class="d-flex flex-wrap gap-2 align-items-end">
                    {{-- Search --}}
                    <div style="flex:1; min-width:200px;">
                        <label class="form-label mb-1" style="font-size:12px; font-weight:600;">Cari Deskripsi</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="form-control form-control-sm" placeholder="Cari aktivitas...">
                    </div>
                    {{-- Status --}}
                    <div style="min-width:140px;">
                        <label class="form-label mb-1" style="font-size:12px; font-weight:600;">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">Semua Status</option>
                            <option value="normal"  {{ request('status') === 'normal'  ? 'selected' : '' }}>Normal</option>
                            <option value="warning" {{ request('status') === 'warning' ? 'selected' : '' }}>Warning</option>
                            <option value="danger"  {{ request('status') === 'danger'  ? 'selected' : '' }}>Danger</option>
                        </select>
                    </div>
                    {{-- Module --}}
                    <div style="min-width:140px;">
                        <label class="form-label mb-1" style="font-size:12px; font-weight:600;">Modul</label>
                        <select name="module" class="form-select form-select-sm">
                            <option value="">Semua Modul</option>
                            <option value="Auth"        {{ request('module') === 'Auth'        ? 'selected' : '' }}>Auth</option>
                            <option value="Maintenance" {{ request('module') === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="Motor"       {{ request('module') === 'Motor'       ? 'selected' : '' }}>Motor</option>
                            <option value="Schedule"    {{ request('module') === 'Schedule'    ? 'selected' : '' }}>Schedule</option>
                            <option value="Integrity"   {{ request('module') === 'Integrity'   ? 'selected' : '' }}>Integrity</option>
                        </select>
                    </div>
                    {{-- Buttons --}}
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i data-feather="search" style="width:14px; height:14px;"></i> Filter
                        </button>
                        <a href="{{ route('activity-logs.index') }}" class="btn btn-outline-secondary btn-sm">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Activity Log Table ──────────────────────────────────────────────── --}}
    <div class="col-12">
        <div class="card card-panel">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="card-panel__title mb-0">
                    <i class="bi bi-shield-lock-fill me-2 text-primary"></i>
                    Activity Log — Semua Aktivitas & Kejadian Keamanan
                </h6>
                <span class="badge bg-light text-dark">{{ $logs->total() }} total</span>
            </div>
            <div class="card-body p-0">
                @if($logs->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i data-feather="inbox" style="width:48px; height:48px; opacity:0.25;"></i>
                        <p class="mt-3 mb-0">Belum ada aktivitas yang tercatat.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th style="width:130px;">Waktu</th>
                                    <th style="width:90px;">Modul</th>
                                    <th style="width:80px;">Status</th>
                                    <th>Deskripsi</th>
                                    <th style="width:140px;">Admin</th>
                                    <th style="width:120px;">IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                @php
                                    // Tentukan warna row berdasarkan status
                                    $rowClass = match($log->status) {
                                        'danger'  => 'table-danger',
                                        'warning' => 'table-warning',
                                        default   => '',
                                    };
                                    // Badge & icon per status
                                    [$badgeBg, $badgeText, $icon] = match($log->status) {
                                        'danger'  => ['#fef2f2', '#dc2626', 'shield-off'],
                                        'warning' => ['#fffbeb', '#d97706', 'alert-triangle'],
                                        default   => ['#f0fdf4', '#16a34a', 'check-circle'],
                                    };
                                    // Icon per modul
                                    $moduleIcon = match($log->module) {
                                        'Auth'        => 'key',
                                        'Maintenance' => 'tool',
                                        'Motor'       => 'zap',
                                        'Schedule'    => 'calendar',
                                        'Integrity'   => 'alert-octagon',
                                        default       => 'activity',
                                    };
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    <td class="text-muted">{{ $log->id }}</td>
                                    <td>
                                        <div style="font-size:12px; font-weight:600;">
                                            {{ $log->created_at->format('d M Y') }}
                                        </div>
                                        <div class="text-muted" style="font-size:11px;">
                                            {{ $log->created_at->format('H:i:s') }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge"
                                              style="background:#e0e7ff; color:#3730a3; font-size:11px;">
                                            <i data-feather="{{ $moduleIcon }}"
                                               style="width:10px; height:10px;"></i>
                                            {{ $log->module }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge d-inline-flex align-items-center gap-1"
                                              style="background:{{ $badgeBg }}; color:{{ $badgeText }}; font-size:11px;">
                                            <i data-feather="{{ $icon }}"
                                               style="width:10px; height:10px;"></i>
                                            {{ ucfirst($log->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span title="{{ $log->description }}">
                                            {{ $log->description }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($log->admin)
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="{{ $log->admin->picture ? asset($log->admin->picture) : asset('assets/images/lamine.webp') }}"
                                                     alt="" style="width:24px; height:24px; border-radius:50%; object-fit:cover;">
                                                <span style="font-size:12px;">{{ $log->admin->full_name }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted" style="font-size:12px;">— Sistem —</span>
                                        @endif
                                    </td>
                                    <td>
                                        <code style="font-size:11px; background:#f1f5f9;
                                                     padding:2px 6px; border-radius:4px;">
                                            {{ $log->ip_address ?? '-' }}
                                        </code>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($logs->hasPages())
                        <div class="d-flex justify-content-center py-3">
                            {{ $logs->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
