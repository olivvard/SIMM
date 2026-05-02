<?php

namespace App\Exports;

use App\Models\MaintenanceLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Font;

class MaintenanceExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    protected string  $month;
    protected ?int    $motorId;

    public function __construct(string $month, ?int $motorId = null)
    {
        $this->month   = $month;
        $this->motorId = $motorId;
    }

    public function collection()
    {
        [$year, $month] = explode('-', $this->month);

        $query = MaintenanceLog::with(['motor', 'schedule', 'admin'])
            ->whereYear('inspection_date', $year)
            ->whereMonth('inspection_date', $month)
            ->orderBy('inspection_date');

        if ($this->motorId) {
            $query->where('motor_id', $this->motorId);
        }

        return $query->get()->map(function ($log, $index) {
            $doneCnt  = $log->activityDetails->where('is_done', true)->count();
            $totalCnt = $log->activityDetails->count();

            return [
                'No'               => $index + 1,
                'Inspection Date'  => $log->inspection_date->format('d/m/Y'),
                'Motor Code'       => $log->motor->motor_code,
                'Location'         => $log->motor->location,
                'Period'           => $log->schedule->period ?? '-',
                'Admin'            => $log->admin->full_name,
                'Activities Done'  => "{$doneCnt}/{$totalCnt}",
                'General Notes'    => $log->general_notes ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Inspection Date',
            'Motor Code',
            'Location',
            'Period',
            'Admin',
            'Activities Done',
            'General Notes',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Bold the header row (row 1)
            1 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }

    public function title(): string
    {
        return 'Maintenance Report ' . $this->month;
    }
}
