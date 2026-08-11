@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- OVERDUE ALERT BANNER                                            --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
@if($overdueCount > 0)
<div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-3 mt-3" role="alert" style="border-left: 5px solid #ef4444;">
    <i class="fa fa-exclamation-triangle fa-lg flex-shrink-0"></i>
    <div>
        <strong>{{ $overdueCount }} Jadwal Maintenance Terlambat!</strong>
        Terdapat <strong>{{ $overdueCount }}</strong> jadwal yang sudah melewati batas waktu dan belum diselesaikan.
        @if(Auth::user()->isAdmin())
        <a href="{{ route('schedules.index', ['status' => 'overdue']) }}" class="alert-link ms-2">Lihat semua &rarr;</a>
        @endif
    </div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- STAT CARDS ROW                                                  --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="row mt-3 g-3">

    {{-- Total Motors --}}
    <div class="col-xl-3 col-md-6">
        <div class="card o-hidden" style="border-radius: 16px; border: none; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); box-shadow: 0 8px 25px rgba(59,130,246,0.35);">
            <div class="card-body" style="padding: 24px;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div style="background: rgba(255,255,255,0.2); border-radius: 12px; width: 50px; height: 50px; display:flex; align-items:center; justify-content:center;">
                        <i class="fa fa-bolt" style="font-size:1.5rem; color: #fff;"></i>
                    </div>
                    <span style="background: rgba(255,255,255,0.2); color:#fff; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px;">
                        {{ $activeMotors }} Aktif
                    </span>
                </div>
                <div>
                    <h2 class="mb-1 fw-bold" style="color:#fff; font-size: 2.2rem;">{{ $totalMotors }}</h2>
                    <p class="mb-0" style="color: rgba(255,255,255,0.8); font-size: 13px;">Total Motor Terdaftar</p>
                </div>
                <div class="mt-3 pt-3" style="border-top: 1px solid rgba(255,255,255,0.2);">
                    @if(Auth::user()->isAdmin())
                    <a href="{{ route('motors.index') }}" style="color: rgba(255,255,255,0.9); font-size: 12px; text-decoration: none;">
                        Lihat semua motor <i class="fa fa-arrow-right ms-1"></i>
                    </a>
                    @else
                    <span style="color: rgba(255,255,255,0.7); font-size: 12px;">Data motor terdaftar</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Jadwal Pending --}}
    <div class="col-xl-3 col-md-6">
        <div class="card o-hidden" style="border-radius: 16px; border: none; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); box-shadow: 0 8px 25px rgba(245,158,11,0.35);">
            <div class="card-body" style="padding: 24px;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div style="background: rgba(255,255,255,0.2); border-radius: 12px; width: 50px; height: 50px; display:flex; align-items:center; justify-content:center;">
                        <i class="fa fa-clock-o" style="font-size:1.5rem; color: #fff;"></i>
                    </div>
                    <span style="background: rgba(255,255,255,0.2); color:#fff; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px;">
                        Menunggu
                    </span>
                </div>
                <div>
                    <h2 class="mb-1 fw-bold" style="color:#fff; font-size: 2.2rem;">{{ $pendingCount }}</h2>
                    <p class="mb-0" style="color: rgba(255,255,255,0.8); font-size: 13px;">Jadwal Pending</p>
                </div>
                <div class="mt-3 pt-3" style="border-top: 1px solid rgba(255,255,255,0.2);">
                    @if(Auth::user()->isAdmin())
                    <a href="{{ route('schedules.index', ['status' => 'pending']) }}" style="color: rgba(255,255,255,0.9); font-size: 12px; text-decoration: none;">
                        Lihat jadwal pending <i class="fa fa-arrow-right ms-1"></i>
                    </a>
                    @else
                    <span style="color: rgba(255,255,255,0.7); font-size: 12px;">Jadwal menunggu eksekusi</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Jadwal Overdue --}}
    <div class="col-xl-3 col-md-6">
        <div class="card o-hidden" style="border-radius: 16px; border: none; background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); box-shadow: 0 8px 25px rgba(239,68,68,0.35);">
            <div class="card-body" style="padding: 24px;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div style="background: rgba(255,255,255,0.2); border-radius: 12px; width: 50px; height: 50px; display:flex; align-items:center; justify-content:center;">
                        <i class="fa fa-exclamation-triangle" style="font-size:1.5rem; color: #fff;"></i>
                    </div>
                    <span style="background: rgba(255,255,255,0.2); color:#fff; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px;">
                        Terlambat
                    </span>
                </div>
                <div>
                    <h2 class="mb-1 fw-bold" style="color:#fff; font-size: 2.2rem;">{{ $overdueCount }}</h2>
                    <p class="mb-0" style="color: rgba(255,255,255,0.8); font-size: 13px;">Jadwal Overdue</p>
                </div>
                <div class="mt-3 pt-3" style="border-top: 1px solid rgba(255,255,255,0.2);">
                    @if(Auth::user()->isAdmin())
                    <a href="{{ route('schedules.index', ['status' => 'overdue']) }}" style="color: rgba(255,255,255,0.9); font-size: 12px; text-decoration: none;">
                        Lihat jadwal overdue <i class="fa fa-arrow-right ms-1"></i>
                    </a>
                    @else
                    <span style="color: rgba(255,255,255,0.7); font-size: 12px;">Jadwal belum diselesaikan</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Maintenance Bulan Ini --}}
    <div class="col-xl-3 col-md-6">
        <div class="card o-hidden" style="border-radius: 16px; border: none; background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 8px 25px rgba(16,185,129,0.35);">
            <div class="card-body" style="padding: 24px;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div style="background: rgba(255,255,255,0.2); border-radius: 12px; width: 50px; height: 50px; display:flex; align-items:center; justify-content:center;">
                        <i class="fa fa-check-circle" style="font-size:1.5rem; color: #fff;"></i>
                    </div>
                    <span style="background: rgba(255,255,255,0.2); color:#fff; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px;">
                        @if($maintenanceTrend >= 0)
                            <i class="fa fa-arrow-up"></i> {{ $maintenanceTrend }}%
                        @else
                            <i class="fa fa-arrow-down"></i> {{ abs($maintenanceTrend) }}%
                        @endif
                    </span>
                </div>
                <div>
                    <h2 class="mb-1 fw-bold" style="color:#fff; font-size: 2.2rem;">{{ $thisMonth }}</h2>
                    <p class="mb-0" style="color: rgba(255,255,255,0.8); font-size: 13px;">Maintenance Bulan Ini</p>
                </div>
                <div class="mt-3 pt-3" style="border-top: 1px solid rgba(255,255,255,0.2);">
                    <span style="color: rgba(255,255,255,0.9); font-size: 12px;">
                        {{ $lastMonth }} bulan lalu &bull; Total: {{ $totalLogs }} log
                    </span>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- CHARTS ROW                                                      --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="row mt-4 g-3">

    {{-- Monthly Maintenance Chart --}}
    <div class="col-xl-8">
        <div class="card card-panel h-100" style="border-radius: 16px; border: none;">
            <div class="card-header d-flex align-items-center justify-content-between" style="border-bottom: 1px solid #f1f5f9; padding: 18px 24px;">
                <div>
                    <h6 class="card-panel__title mb-0" style="font-size: 14px; font-weight: 700;">
                        <i class="fa fa-bar-chart me-2 text-primary"></i>
                        Tren Maintenance (6 Bulan Terakhir)
                    </h6>
                    <p class="text-muted mb-0" style="font-size: 12px; margin-top: 2px;">Jumlah log maintenance yang diselesaikan per bulan</p>
                </div>
            </div>
            <div class="card-body" style="padding: 20px 24px;">
                <canvas id="maintenanceChart" height="100"></canvas>
            </div>
        </div>
    </div>

    {{-- Schedule Status Donut --}}
    <div class="col-xl-4">
        <div class="card card-panel h-100" style="border-radius: 16px; border: none;">
            <div class="card-header" style="border-bottom: 1px solid #f1f5f9; padding: 18px 24px;">
                <h6 class="card-panel__title mb-0" style="font-size: 14px; font-weight: 700;">
                    <i class="fa fa-pie-chart me-2 text-success"></i>
                    Status Jadwal
                </h6>
                <p class="text-muted mb-0" style="font-size: 12px; margin-top: 2px;">Distribusi status semua jadwal</p>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center" style="padding: 20px;">
                <div style="position: relative; width: 200px; height: 200px;">
                    <canvas id="scheduleDonut"></canvas>
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                        <div style="font-size: 1.8rem; font-weight: 800; line-height: 1;">{{ $pendingCount + $overdueCount + $doneCount }}</div>
                        <div style="font-size: 11px; color: #94a3b8; font-weight: 500;">Total</div>
                    </div>
                </div>
                <div class="d-flex gap-4 mt-3">
                    <div class="text-center">
                        <div class="d-flex align-items-center gap-1 mb-1">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #f59e0b; display: inline-block;"></span>
                            <span style="font-size: 11px; color: #64748b;">Pending</span>
                        </div>
                        <div style="font-size: 1.1rem; font-weight: 700;">{{ $pendingCount }}</div>
                    </div>
                    <div class="text-center">
                        <div class="d-flex align-items-center gap-1 mb-1">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                            <span style="font-size: 11px; color: #64748b;">Done</span>
                        </div>
                        <div style="font-size: 1.1rem; font-weight: 700;">{{ $doneCount }}</div>
                    </div>
                    <div class="text-center">
                        <div class="d-flex align-items-center gap-1 mb-1">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #ef4444; display: inline-block;"></span>
                            <span style="font-size: 11px; color: #64748b;">Overdue</span>
                        </div>
                        <div style="font-size: 1.1rem; font-weight: 700;">{{ $overdueCount }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- UPCOMING SCHEDULES (ADMIN) + MAINTENANCE INPUT (TEKNISI)        --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="row mt-4 g-3">

    {{-- Upcoming H-3 Schedules (Admin) / Jadwal Perlu Dikerjakan (Teknisi) --}}
    <div class="col-xl-8">
        <div class="card card-panel" style="border-radius: 16px; border: none;">
            <div class="card-header d-flex align-items-center justify-content-between" style="border-bottom: 1px solid #f1f5f9; padding: 18px 24px;">
                <div>
                    <h6 class="card-panel__title mb-0" style="font-size: 14px; font-weight: 700;">
                        <i class="fa fa-calendar-check-o me-2 text-warning"></i>
                        Jadwal H-3 (Mendatang)
                    </h6>
                    <p class="text-muted mb-0" style="font-size: 12px; margin-top: 2px;">Jadwal maintenance dalam 3 hari ke depan</p>
                </div>
                @if(Auth::user()->isAdmin())
                <a href="{{ route('schedules.index') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 8px; font-size: 12px;">
                    Lihat Semua
                </a>
                @else
                <a href="{{ route('maintenance.index') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 8px; font-size: 12px;">
                    Lihat Semua
                </a>
                @endif
            </div>
            <div class="card-body p-0">
                @if($upcomingSchedules->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fa fa-calendar-check-o" style="font-size: 3rem; opacity: 0.2; display: block; margin-bottom: 12px;"></i>
                        <p class="mb-0 fw-semibold">Tidak ada jadwal mendatang</p>
                        <small>Dalam 3 hari ke depan semua aman.</small>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="font-size: 12px; padding: 12px 16px;">Motor</th>
                                    <th style="font-size: 12px;">Lokasi</th>
                                    <th style="font-size: 12px;">Tanggal</th>
                                    <th style="font-size: 12px;">Sisa</th>
                                    <th style="font-size: 12px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($upcomingSchedules as $schedule)
                                    @php
                                        $daysLeft = now()->startOfDay()->diffInDays($schedule->schedule_date->startOfDay(), false);
                                    @endphp
                                    <tr>
                                        <td style="padding: 12px 16px;">
                                            <div class="fw-semibold text-primary" style="font-size: 13px;">{{ $schedule->motor?->motor_code ?? '—' }}</div>
                                        </td>
                                        <td>
                                            @if($schedule->motor)
                                                <span class="badge" style="background: #eff6ff; color: #3b82f6; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                                    {{ $schedule->motor->location }}
                                                </span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td><span style="font-size: 12px; font-weight: 600;">{{ $schedule->schedule_date->format('d M Y') }}</span></td>
                                        <td>
                                            @if($daysLeft === 0)
                                                <span class="badge bg-danger" style="border-radius: 6px;">Hari ini!</span>
                                            @elseif($daysLeft === 1)
                                                <span class="badge bg-warning text-dark" style="border-radius: 6px;">Besok</span>
                                            @else
                                                <span class="badge bg-info text-dark" style="border-radius: 6px;">{{ $daysLeft }} hari</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if(Auth::user()->isTeknisi())
                                            <a href="{{ route('maintenance.create', ['schedule_id' => $schedule->id]) }}"
                                               class="btn btn-sm btn-primary" style="border-radius: 8px; font-size: 12px; padding: 4px 12px;">
                                                <i class="fa fa-wrench me-1"></i>Input
                                            </a>
                                            @else
                                            <a href="{{ route('schedules.show', $schedule) }}"
                                               class="btn btn-sm btn-outline-info" style="border-radius: 8px; font-size: 12px; padding: 4px 12px;">
                                                <i class="fa fa-eye me-1"></i>Detail
                                            </a>
                                            @endif
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

    {{-- Summary Info --}}
    <div class="col-xl-4">
        <div class="card card-panel h-100" style="border-radius: 16px; border: none; background: #0f172a;">
            <div class="card-body" style="padding: 24px;">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(99,102,241,0.2); display:flex; align-items:center; justify-content:center;">
                        <i class="fa fa-info-circle" style="font-size: 1.2rem; color: #818cf8;"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold" style="color: #f8fafc; font-size: 13px;">Ringkasan Sistem</h6>
                        <small style="color: #64748b;">Overview WEA PM Motor</small>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3" style="border-bottom: 1px solid #1e293b;">
                    <span style="color: #94a3b8; font-size: 13px;">Total Motor</span>
                    <span style="color: #f8fafc; font-weight: 700; font-size: 15px;">{{ $totalMotors }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3" style="border-bottom: 1px solid #1e293b;">
                    <span style="color: #94a3b8; font-size: 13px;">Total Log</span>
                    <span style="color: #f8fafc; font-weight: 700; font-size: 15px;">{{ $totalLogs }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3" style="border-bottom: 1px solid #1e293b;">
                    <span style="color: #94a3b8; font-size: 13px;">Total User</span>
                    <span style="color: #f8fafc; font-weight: 700; font-size: 15px;">{{ $totalUsers }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3" style="border-bottom: 1px solid #1e293b;">
                    <span style="color: #94a3b8; font-size: 13px;">Teknisi</span>
                    <span style="color: #818cf8; font-weight: 700; font-size: 15px;">{{ $teknisiCount }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span style="color: #94a3b8; font-size: 13px;">Admin</span>
                    <span style="color: #34d399; font-weight: 700; font-size: 15px;">{{ $totalUsers - $teknisiCount }}</span>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- RECENT MAINTENANCE LOGS TABLE                                   --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="row mt-4 g-3 mb-4">
    <div class="col-12">
        <div class="card card-panel" style="border-radius: 16px; border: none;">
            <div class="card-header d-flex align-items-center justify-content-between" style="border-bottom: 1px solid #f1f5f9; padding: 18px 24px;">
                <div>
                    <h6 class="card-panel__title mb-0" style="font-size: 14px; font-weight: 700;">
                        <i class="fa fa-file-text me-2 text-primary"></i>
                        Log Maintenance Terbaru
                    </h6>
                    <p class="text-muted mb-0" style="font-size: 12px; margin-top: 2px;">5 entri maintenance terakhir yang diinput</p>
                </div>
                @if(Auth::user()->isTeknisi())
                <a href="{{ route('maintenance.index') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 8px; font-size: 12px;">
                    Lihat Semua
                </a>
                @else
                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 8px; font-size: 12px;">
                    Lihat Reports
                </a>
                @endif
            </div>
            <div class="card-body p-0">
                @if($recentLogs->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fa fa-folder-open-o" style="font-size: 3rem; opacity: 0.2; display: block; margin-bottom: 12px;"></i>
                        <p class="mb-0 fw-semibold">Belum ada log maintenance</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="font-size: 12px; padding: 12px 16px;">#</th>
                                    <th style="font-size: 12px;">Tanggal Inspeksi</th>
                                    <th style="font-size: 12px;">Motor</th>
                                    <th style="font-size: 12px;">Lokasi</th>
                                    <th style="font-size: 12px;">Teknisi</th>
                                    <th style="font-size: 12px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentLogs as $i => $log)
                                    <tr>
                                        <td style="padding: 12px 16px; font-size: 12px; color: #94a3b8; font-weight: 600;">#{{ $log->id }}</td>
                                        <td>
                                            <span class="fw-semibold" style="font-size: 13px;">
                                                {{ $log->inspection_date?->format('d M Y') ?? '—' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-primary" style="font-size: 13px;">{{ $log->motor?->motor_code ?? '—' }}</div>
                                        </td>
                                        <td>
                                            @if($log->motor)
                                                <span class="badge" style="background: #eff6ff; color: #3b82f6; border-radius: 6px; font-size: 11px;">
                                                    {{ $log->motor->location }}
                                                </span>
                                            @else
                                                <span class="text-muted" style="font-size: 12px;">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div style="width: 28px; height: 28px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #8b5cf6); display:flex; align-items:center; justify-content:center; color:#fff; font-size: 11px; font-weight: 700; flex-shrink: 0;">
                                                    {{ strtoupper(substr($log->admin?->full_name ?? '?', 0, 1)) }}
                                                </div>
                                                <span style="font-size: 12px;">{{ $log->admin?->full_name ?? '—' }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if(Auth::user()->isTeknisi())
                                            <a href="{{ route('maintenance.show', $log) }}"
                                               class="btn btn-sm btn-outline-info" style="border-radius: 8px; font-size: 12px; padding: 4px 12px;">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            @else
                                            <a href="{{ route('reports.index') }}"
                                               class="btn btn-sm btn-outline-secondary" style="border-radius: 8px; font-size: 12px; padding: 4px 12px;">
                                                <i class="fa fa-bar-chart"></i>
                                            </a>
                                            @endif
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Monthly Maintenance Bar Chart ──────────────────────────────
    const mCtx = document.getElementById('maintenanceChart').getContext('2d');
    const gradient = mCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.8)');
    gradient.addColorStop(1, 'rgba(99, 102, 241, 0.2)');

    new Chart(mCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($monthLabels) !!},
            datasets: [{
                label: 'Jumlah Maintenance',
                data: {!! json_encode($monthlyData) !!},
                backgroundColor: gradient,
                borderColor: '#3b82f6',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.parsed.y} log maintenance`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, color: '#94a3b8', font: { size: 11 } },
                    grid: { color: 'rgba(148, 163, 184, 0.1)' }
                },
                x: {
                    ticks: { color: '#94a3b8', font: { size: 11 } },
                    grid: { display: false }
                }
            }
        }
    });

    // ── Schedule Status Donut Chart ─────────────────────────────────
    const dCtx = document.getElementById('scheduleDonut').getContext('2d');
    const pending = {{ $scheduleStatusData['pending'] }};
    const done    = {{ $scheduleStatusData['done'] }};
    const overdue = {{ $scheduleStatusData['overdue'] }};
    const total   = pending + done + overdue;

    new Chart(dCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Done', 'Overdue'],
            datasets: [{
                data: total > 0 ? [pending, done, overdue] : [1, 0, 0],
                backgroundColor: ['#f59e0b', '#10b981', '#ef4444'],
                borderColor: ['#fff', '#fff', '#fff'],
                borderWidth: 3,
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.label}: ${ctx.parsed} jadwal`
                    }
                }
            }
        }
    });

});
</script>
@endpush