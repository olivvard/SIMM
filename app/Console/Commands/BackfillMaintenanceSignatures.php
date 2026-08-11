<?php

namespace App\Console\Commands;

use App\Models\MaintenanceLog;
use Illuminate\Console\Command;

class BackfillMaintenanceSignatures extends Command
{
    protected $signature   = 'maintenance:backfill-signatures';
    protected $description = 'Generate (atau re-generate) digital signatures + payload snapshots untuk semua maintenance logs.';

    public function handle(): int
    {
        // Re-generate semua: termasuk log lama yang belum punya snapshot
        $logs = MaintenanceLog::all();

        if ($logs->isEmpty()) {
            $this->info('Tidak ada maintenance log di database.');
            return self::SUCCESS;
        }

        $this->info("Ditemukan {$logs->count()} log. Generating signatures + snapshots...");
        $bar = $this->output->createProgressBar($logs->count());
        $bar->start();

        foreach ($logs as $log) {
            // generateSignature() sekarang juga mengisi $log->payload_snapshot
            $signature = $log->generateSignature();
            $log->update([
                'digital_signature' => $signature,
                'payload_snapshot'  => $log->payload_snapshot,
            ]);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Selesai! Semua signature dan snapshot telah di-generate ulang.');

        return self::SUCCESS;
    }
}
