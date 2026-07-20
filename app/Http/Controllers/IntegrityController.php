<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\MaintenanceLog;
use Illuminate\Http\JsonResponse;

class IntegrityController extends Controller
{
    /**
     * Endpoint polling deteksi tampering maintenance_logs.
     * Setiap 30 detik di-hit oleh browser.
     * Return JSON berisi log yang tampere beserta before/after diff.
     */
    public function check(): JsonResponse
    {
        $logs = MaintenanceLog::with(['motor', 'admin'])
            ->whereNotNull('digital_signature')
            ->get();

        $tamperedLogs = $logs->filter(fn (MaintenanceLog $log) => ! $log->verifySignature());

        if ($tamperedLogs->isEmpty()) {
            return response()->json([
                'status'  => 'clean',
                'message' => 'All maintenance logs are intact.',
                'count'   => 0,
                'logs'    => [],
            ]);
        }

        // Build detail lengkap termasuk before/after diff
        $details = $tamperedLogs->values()->map(function (MaintenanceLog $log) {

            // Hitung diff (before/after per field)
            $diff = $log->getDiff();

            // ── [ACTIVITY LOG] Catat tampering (maks 1x per log per hari) ────
            $diffText = collect($diff)->map(fn ($d) =>
                "[{$d['field']}] '{$d['before']}' → '{$d['after']}'"
            )->implode(' | ');

            $desc = 'PELANGGARAN INTEGRITAS: Maintenance Log #' . $log->id
                  . ' (Motor: ' . ($log->motor?->motor_code ?? 'Unknown') . ')'
                  . ' terdeteksi telah dimodifikasi langsung di database.'
                  . ($diffText ? ' Perubahan: ' . $diffText : ' (snapshot tidak tersedia)');

            $alreadyLogged = ActivityLog::where('action', 'tampered_detected')
                ->where('description', 'like', '%Log #' . $log->id . '%')
                ->where('status', 'danger')
                ->whereDate('created_at', today())
                ->exists();

            if (! $alreadyLogged) {
                ActivityLog::record(
                    module:      'Integrity',
                    action:      'tampered_detected',
                    description: $desc,
                    status:      'danger',
                    adminId:     null
                );
            }

            return [
                'id'              => $log->id,
                'motor_code'      => $log->motor?->motor_code ?? 'Unknown Motor',
                'admin_name'      => $log->admin?->full_name ?? 'Unknown Admin',
                'inspection_date' => $log->inspection_date?->format('d M Y') ?? '-',
                'url'             => route('maintenance.show', $log->id),
                'diff'            => $diff,   // array of { field, before, after }
                'has_snapshot'    => ! empty($log->payload_snapshot),
            ];
        });

        return response()->json([
            'status'  => 'tampered',
            'message' => 'Data integrity violation detected!',
            'count'   => $tamperedLogs->count(),
            'logs'    => $details,
        ]);
    }
}
