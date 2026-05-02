<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Maintenance Report — {{ $month }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 8pt; color: #1e293b; background: #fff; }
        .report-header { border-bottom: 3px solid #1d4ed8; padding-bottom: 8px; margin-bottom: 12px; }
        .report-header h1 { font-size: 13pt; color: #1d4ed8; font-weight: 700; margin-bottom: 2px; }
        .report-header p { font-size: 7.5pt; color: #64748b; margin: 2px 0; }
        .sum-row { width: 100%; border-collapse: collapse; margin-bottom: 14px; border: 1px solid #bfdbfe; }
        .sum-row td { text-align: center; padding: 8px 6px; border-right: 1px solid #bfdbfe; width: 25%; }
        .sum-row td:last-child { border-right: none; }
        .sum-row .s-label { display: block; font-size: 7pt; color: #1e40af; font-weight: 600; background: #eff6ff; padding: 4px 0; border-bottom: 1px solid #bfdbfe; }
        .sum-row .s-val { display: block; font-size: 15pt; font-weight: 700; color: #1d4ed8; padding: 4px 0; }
        .meta-wrap { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .meta-wrap td { vertical-align: top; }
        .meta-wrap td.col-info { padding-right: 10px; }
        .meta-wrap td.col-photo { width: 140px; }
        .motor-card { border: 1px solid #bfdbfe; background: #f0f9ff; padding: 7px 10px; font-size: 7.5pt; }
        .motor-card .mc { font-weight: 700; color: #1d4ed8; font-size: 9pt; }
        .motor-card .mn { color: #334155; margin-top: 1px; }
        .motor-card .ml { color: #64748b; font-size: 7pt; margin-top: 2px; }
        .photo-box { border: 1px solid #bfdbfe; overflow: hidden; background: #f8fafc; }
        .photo-box img { width: 100%; max-height: 115px; display: block; object-fit: cover; }
        .photo-cap { font-size: 6.5pt; color: #64748b; text-align: center; padding: 3px 4px; border-top: 1px solid #e2e8f0; }
        .photo-none { height: 85px; background: #f1f5f9; text-align: center; padding-top: 28px; font-size: 7pt; color: #94a3b8; }
        table.pivot { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        thead tr.h-motor th { background: #1d4ed8; color: #fff; font-size: 7pt; font-weight: 700; text-align: center; padding: 5px 3px; border: 1px solid #1e40af; }
        thead tr.h-motor th.th-act { background: #0f172a; text-align: left; padding-left: 5px; }
        thead tr.h-date th { background: #dbeafe; color: #1e3a8a; font-size: 6.5pt; text-align: center; padding: 3px 2px; border: 1px solid #bfdbfe; }
        thead tr.h-date th.th-act { background: #e2e8f0; text-align: left; padding-left: 5px; color: #475569; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td { padding: 4px 3px; font-size: 7.5pt; border: 1px solid #e2e8f0; vertical-align: middle; }
        td.td-code { font-weight: 700; color: #1d4ed8; text-align: center; width: 38px; white-space: nowrap; }
        td.td-name { min-width: 120px; }
        td.td-cat { text-align: center; width: 58px; }
        td.td-chk { text-align: center; font-size: 9pt; font-weight: 700; width: 28px; }
        .done-yes { color: #16a34a; }
        .done-no  { color: #dc2626; }
        .done-na  { color: #94a3b8; }
        .cat { font-size: 6pt; font-weight: 700; padding: 1px 3px; border-radius: 3px; display: inline-block; }
        .cat-lubrication { background: #fef3c7; color: #92400e; }
        .cat-inspection  { background: #dbeafe; color: #1e40af; }
        .cat-cleaning    { background: #d1fae5; color: #065f46; }
        .cat-electrical  { background: #ede9fe; color: #4c1d95; }
        .report-footer { text-align: center; font-size: 7pt; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 6px; margin-top: 10px; }
    </style>
</head>
<body>

<div class="report-header">
    <h1>Preventive Maintenance Report</h1>
    <p>Motor PM System &mdash; Generated on {{ now()->format('d M Y H:i') }}</p>
    <p>
        @php
            try { $periodLabel = \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y'); }
            catch (\Exception $e) { $periodLabel = $month; }
        @endphp
        Period: <strong>{{ $periodLabel }}</strong> &nbsp;|&nbsp; Motor: <strong>{{ $motorLabel ?? 'Selected' }}</strong>
    </p>
</div>

@php
    $totalLogs      = $logs->count();
    $motorsServiced = $logs->pluck('motor_id')->unique()->count();
    $actDone        = $logs->sum(fn($l) => $l->activityDetails->where('is_done', true)->count());
    $actSkip        = $logs->sum(fn($l) => $l->activityDetails->where('is_done', false)->count());
    $firstLog       = $logs->first();
    $rawPhoto       = $firstLog?->photo_url ? public_path('storage/' . $firstLog->photo_url) : null;
    $photoSrc       = ($rawPhoto && file_exists($rawPhoto)) ? 'file:///' . str_replace('\\', '/', $rawPhoto) : null;
@endphp

<table class="sum-row">
    <tr>
        <td><span class="s-label">Total Logs</span><span class="s-val">{{ $totalLogs }}</span></td>
        <td><span class="s-label">Motors Serviced</span><span class="s-val">{{ $motorsServiced }}</span></td>
        <td><span class="s-label">Activities Done</span><span class="s-val" style="color:#16a34a;">{{ $actDone }}</span></td>
        <td><span class="s-label">Activities Skipped</span><span class="s-val" style="color:#dc2626;">{{ $actSkip }}</span></td>
    </tr>
</table>

@if($firstLog)
<table class="meta-wrap">
    <tr>
        <td class="col-info">
            <div class="motor-card">
                <div class="mc">{{ $firstLog->motor?->motor_code ?? '—' }}</div>
                <div class="ml">{{ $firstLog->motor?->location ?? '' }} ({{ $firstLog->motor?->area ?? '' }})</div>
                <div style="margin-top:5px; font-size:7.5pt;">
                    <strong>Category:</strong> {{ $firstLog->motor?->category ?? '—' }} HP &nbsp;
                    <strong>Installed:</strong> {{ $firstLog->motor?->installation_date?->format('d M Y') ?? '—' }} &nbsp;
                    <strong>Inspector:</strong> {{ $firstLog->admin?->full_name ?? '—' }} &nbsp;
                    <strong>Period:</strong> {{ $firstLog->schedule?->period ?? '—' }}
                </div>
            </div>
        </td>
        <td class="col-photo">
            <div class="photo-box">
                @if($photoSrc)
                    <img src="{{ $photoSrc }}" alt="Motor Photo">
                    <div class="photo-cap">Inspection Photo<br>{{ $firstLog->inspection_date?->format('d M Y') }}</div>
                @else
                    <div class="photo-none">No Photo</div>
                @endif
            </div>
        </td>
    </tr>
</table>
@endif

@php
    $activities = collect();
    foreach ($logs as $log) {
        foreach ($log->activityDetails as $detail) {
            if ($detail->activity && !$activities->contains('id', $detail->activity_id)) {
                $activities->push($detail->activity);
            }
        }
    }
    $activities = $activities->sortBy('activity_code')->values();
    $lookup = [];
    foreach ($logs as $log) {
        foreach ($log->activityDetails as $detail) {
            $lookup[$log->id][$detail->activity_id] = $detail;
        }
    }
@endphp

<table class="pivot">
    <thead>
        <tr class="h-motor">
            <th class="th-act" style="width:38px;">Code</th>
            <th class="th-act">Activity Name</th>
            <th class="th-act" style="width:58px;">Category</th>
            @foreach($logs as $log)
                <th>{{ $log->motor?->motor_code ?? '—' }}</th>
            @endforeach
        </tr>
        <tr class="h-date">
            <th class="th-act">—</th>
            <th class="th-act">Inspection Date</th>
            <th class="th-act">Period</th>
            @foreach($logs as $log)
                <th>
                    {{ $log->inspection_date?->format('d/m/y') ?? '—' }}<br>
                    <span style="color:#1e40af;">{{ $log->schedule?->period ?? '—' }}</span>
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @forelse($activities as $activity)
        <tr>
            <td class="td-code">{{ $activity->activity_code }}</td>
            <td class="td-name">{{ $activity->activity_name }}</td>
            <td class="td-cat">
                <span class="cat cat-{{ strtolower($activity->category) }}">{{ $activity->category }}</span>
            </td>
            @foreach($logs as $log)
                @php $detail = $lookup[$log->id][$activity->id] ?? null; @endphp
                <td class="td-chk {{ $detail?->is_done ? 'done-yes' : ($detail ? 'done-no' : 'done-na') }}">
                    {{ $detail?->is_done ? '✓' : ($detail ? '✗' : '—') }}
                </td>
            @endforeach
        </tr>
        @empty
        <tr>
            <td colspan="{{ 3 + $logs->count() }}" style="text-align:center; padding:20px; color:#94a3b8;">No activity data found.</td>
        </tr>
        @endforelse

        <tr style="background:#f1f5f9; font-weight:700;">
            <td colspan="2" style="text-align:right; padding-right:8px; font-size:7.5pt;">Done / Total</td>
            <td></td>
            @foreach($logs as $log)
                @php
                    $dn  = $log->activityDetails->where('is_done', true)->count();
                    $tot = $log->activityDetails->count();
                    $pct = $tot > 0 ? round($dn / $tot * 100) : 0;
                @endphp
                <td class="td-chk {{ $pct >= 80 ? 'done-yes' : ($pct >= 50 ? '' : 'done-no') }}" style="font-size:7pt;">
                    {{ $dn }}/{{ $tot }}<br><span style="font-size:6pt;">{{ $pct }}%</span>
                </td>
            @endforeach
        </tr>
    </tbody>
</table>

<div class="report-footer">
    Motor Preventive Maintenance System &copy; {{ date('Y') }} &nbsp;|&nbsp; Generated: {{ now()->format('d M Y H:i') }}
</div>
</body>
</html>