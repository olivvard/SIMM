<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Maintenance Report — {{ $month }}</title>
    <style>
        @page { margin: 2cm; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 8pt; color: #000000; background: #fff; line-height: 1.25; }
        .report-header { border-bottom: 2px solid #000000; padding-bottom: 6px; margin-bottom: 10px; }
        .report-header h1 { font-size: 13pt; color: #000000; font-weight: 700; margin-bottom: 2px; }
        .report-header p { font-size: 7.5pt; color: #333333; margin: 1px 0; }
        .motor-card { border: 1px solid #cbd5e1; background: #f8fafc; padding: 6px 10px; font-size: 7.5pt; margin-bottom: 10px; }
        .motor-card .mc { font-weight: 700; color: #000000; font-size: 9pt; }
        .motor-card .mn { color: #000000; margin-top: 1px; }
        .motor-card .ml { color: #333333; font-size: 7pt; margin-top: 2px; }
        table.pivot { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        thead tr.h-motor th { background: #334155; color: #ffffff; font-size: 7pt; font-weight: 700; text-align: center; padding: 4px 3px; border: 1px solid #334155; }
        thead tr.h-motor th.th-act { background: #334155; text-align: left; padding-left: 5px; }
        thead tr.h-date th { background: #e2e8f0; color: #000000; font-size: 6.5pt; text-align: center; padding: 3px 2px; border: 1px solid #cbd5e1; }
        thead tr.h-date th.th-act { background: #e2e8f0; text-align: left; padding-left: 5px; color: #000000; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td { padding: 3px 3px; font-size: 7.5pt; border: 1px solid #cbd5e1; vertical-align: middle; }
        td.td-code { font-weight: 700; color: #000000; text-align: center; width: 38px; white-space: nowrap; }
        td.td-name { min-width: 120px; }
        td.td-chk { text-align: center; font-size: 9pt; font-weight: 700; width: 28px; }
        .done-yes { color: #000000; font-weight: bold; }
        .done-no  { color: #000000; font-weight: normal; }
        .done-na  { color: #94a3b8; }
        .report-footer { text-align: center; font-size: 7pt; color: #94a3b8; border-top: 1px solid #cbd5e1; padding-top: 6px; margin-top: 8px; }
    </style>
</head>
<body>
@foreach($logs->groupBy('motor_id') as $motorId => $motorLogs)
    @php
        $firstLog = $motorLogs->first();
        $activities = collect();
        foreach ($motorLogs as $log) {
            foreach ($log->activityDetails as $detail) {
                if ($detail->activity && !$activities->contains('id', $detail->activity_id)) {
                    $activities->push($detail->activity);
                }
            }
        }
        $activities = $activities->sortBy('activity_code')->values();
        $lookup = [];
        foreach ($motorLogs as $log) {
            foreach ($log->activityDetails as $detail) {
                $lookup[$log->id][$detail->activity_id] = $detail;
            }
        }
    @endphp

    <div class="report-header">
        <h1>Preventive Maintenance Report</h1>
        <p>Motor PM System &mdash; Generated on {{ now()->format('d M Y H:i') }}</p>
        <p>
            Motor: <strong>{{ $firstLog->motor?->motor_code ?? '—' }}</strong>
        </p>
    </div>

    @if($firstLog)
    <div class="motor-card">
        <div class="mc">{{ $firstLog->motor?->motor_code ?? '—' }}</div>
        <div class="ml">{{ $firstLog->motor?->location ?? '' }} ({{ $firstLog->motor?->area ?? '' }})</div>
        <div style="margin-top:5px; font-size:7.5pt;">
            <strong>Category:</strong> {{ $firstLog->motor?->category ?? '—' }} HP &nbsp;|&nbsp;
            <strong>Inspector:</strong> {{ Auth::user()->full_name ?? '—' }}
        </div>
    </div>
    @endif

    <table class="pivot" style="width:100%;">
        <thead>
            <tr class="h-motor">
                <th class="th-act" style="width:36px;">#</th>
                <th class="th-act">Activity Name</th>
                @foreach($motorLogs as $log)
                    <th>{{ $log->motor?->motor_code ?? '—' }}</th>
                @endforeach
            </tr>
            <tr class="h-date">
                <th class="th-act">—</th>
                <th class="th-act">Inspection Date</th>
                @foreach($motorLogs as $log)
                    <th>
                        {{ $log->inspection_date?->format('d/m/y') ?? '—' }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($activities as $activity)
            <tr>
                <td class="td-code" style="text-align:center;">{{ $loop->iteration }}</td>
                <td class="td-name">{{ $activity->activity_name }}</td>
                @foreach($motorLogs as $log)
                    @php $detail = $lookup[$log->id][$activity->id] ?? null; @endphp
                    <td class="td-chk {{ $detail?->is_done ? 'done-yes' : ($detail ? 'done-no' : 'done-na') }}">
                        {{ $detail?->is_done ? '✓' : ($detail ? '✗' : '—') }}
                    </td>
                @endforeach
            </tr>
            @empty
            <tr>
                <td colspan="{{ 2 + $motorLogs->count() }}" style="text-align:center; padding:15px; color:#94a3b8;">No activity data found.</td>
            </tr>
            @endforelse

            <tr style="background:#f1f5f9; font-weight:700;">
                <td colspan="2" style="text-align:right; padding-right:8px; font-size:7.5pt;">Done / Total</td>
                @foreach($motorLogs as $log)
                    @php
                        $dn  = $log->activityDetails->where('is_done', true)->count();
                        $tot = $log->activityDetails->count();
                        $pct = $tot > 0 ? round($dn / $tot * 100) : 0;
                      @endphp
                    <td class="td-chk" style="font-size:7pt;">
                        {{ $dn }}/{{ $tot }}<br><span style="font-size:6pt;">{{ $pct }}%</span>
                    </td>
                @endforeach
            </tr>
        </tbody>
    </table>

    <table style="width:100%; border-collapse:collapse; margin-top:10px; margin-bottom:10px; page-break-inside:avoid;">
        <tr>
            <td style="width:35%; vertical-align:top; padding-right:8px;">
                <h3 style="font-size:8pt; margin-bottom:4px; font-weight:bold; text-transform:uppercase;">Workers</h3>
                <table style="width:100%; border-collapse:collapse; font-size:7.5pt;">
                    <thead>
                        <tr>
                            <th style="background:#e2e8f0; border:1px solid #cbd5e1; padding:4px; text-align:center; width:25px;">No</th>
                            <th style="background:#e2e8f0; border:1px solid #cbd5e1; padding:4px; text-align:left;">Worker Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i = 1; $i <= 4; $i++)
                        <tr>
                            <td style="border:1px solid #cbd5e1; padding:6px; text-align:center;">{{ $i }}</td>
                            <td style="border:1px solid #cbd5e1; padding:6px;">&nbsp;</td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </td>
            <td style="width:65%; vertical-align:top; padding-left:8px;">
                <h3 style="font-size:8pt; margin-bottom:4px; font-weight:bold; text-transform:uppercase;">General Note</h3>
                <div style="width:100%; height:91px; border:1px solid #cbd5e1; background:#fcfcfc; padding:8px; font-size:7.5pt;">
                    &nbsp;
                </div>
            </td>
        </tr>
    </table>

    <table style="width: 100%; border-collapse: collapse; margin-top: 25px; page-break-inside: avoid; font-size: 7.5pt;">
        <tr>
            <td style="width: 25%; text-align: center; vertical-align: bottom; height: 30px; padding-bottom: 4px; font-weight: bold;">
                Report By:<br>
                PT. Wholesale Electric Asia Indonesia
            </td>
            <td style="width: 25%; text-align: center; vertical-align: bottom; height: 30px; padding-bottom: 4px; font-weight: bold;">
                Acknowledge By:<br>
                PT. Pertamina RU II Dumai
            </td>
            <td style="width: 25%; text-align: center; vertical-align: bottom; height: 30px; padding-bottom: 4px; font-weight: bold;">
                Acknowledge By:<br>
                PT. Pertamina RU II Dumai
            </td>
            <td style="width: 25%; text-align: center; vertical-align: bottom; height: 30px; padding-bottom: 4px; font-weight: bold;">
                Acknowledge By:<br>
                PT. Pertamina RU II Dumai
                &nbsp;
            </td>
        </tr>
        <tr>
            <td style="text-align: center; padding-top: 35px; vertical-align: top;">
                _______________________<br>
                <span style="font-weight: bold;">Electrical</span>
                <div style="text-align: left; width: 110px; margin: 4px auto 0 auto; line-height: 1.3;">
                    Name:<br>
                    Date:
                </div>
            </td>
            <td style="text-align: center; padding-top: 35px; vertical-align: top;">
                _______________________<br>
                <span style="font-weight: bold;">Operation</span>
                <div style="text-align: left; width: 110px; margin: 4px auto 0 auto; line-height: 1.3;">
                    Name:<br>
                    Date:
                </div>
            </td>
            <td style="text-align: center; padding-top: 35px; vertical-align: top;">
                _______________________<br>
                <span style="font-weight: bold;">Electrical MA I/II/III/IV</span>
                <div style="text-align: left; width: 110px; margin: 4px auto 0 auto; line-height: 1.3;">
                    Name:<br>
                    Date:
                </div>
            </td>
            <td style="text-align: center; padding-top: 35px; vertical-align: top;">
                _______________________<br>
                <span style="font-weight: bold;">EIIE</span>
                <div style="text-align: left; width: 110px; margin: 4px auto 0 auto; line-height: 1.3;">
                    Name:<br>
                    Date:
                </div>
            </td>
        </tr>
    </table>

    @if(!$loop->last)
        <div style="page-break-after: always;"></div>
    @endif
@endforeach
</body>
</html>