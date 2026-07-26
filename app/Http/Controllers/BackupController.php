<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\ActivityLog;
use Carbon\Carbon;
use ZipArchive;

class BackupController extends Controller
{
    private string $disk = 'local';
    private string $dir  = 'backups';

    // ─── Index: List all backup files ────────────────────────────────────────
    public function index()
    {
        Storage::disk($this->disk)->makeDirectory($this->dir);

        $files = Storage::disk($this->disk)->files($this->dir);

        $backups = collect($files)
            ->map(fn($f) => [
                'name'     => basename($f),
                'path'     => $f,
                'size'     => Storage::disk($this->disk)->size($f),
                'modified' => Carbon::createFromTimestamp(
                    Storage::disk($this->disk)->lastModified($f)
                ),
                'type'     => Str::endsWith($f, '.sql') ? 'database' : 'files',
            ])
            ->sortByDesc('modified')
            ->values();

        return view('backup.index', compact('backups'));
    }

    // ─── Backup Database (PHP PDO — cross-platform, no mysqldump needed) ─────
    public function backupDatabase()
    {
        try {
            $sql = $this->generateSqlDump();

            $name = 'db_backup_' . now()->format('Ymd_His') . '.sql';
            Storage::disk($this->disk)->put("{$this->dir}/{$name}", $sql);

            ActivityLog::record('Backup', 'backup_created', "Backup database berhasil: {$name}.");
            return back()->with('success', "✅ Backup database berhasil dibuat: <strong>{$name}</strong>");
        } catch (\Throwable $e) {
            ActivityLog::record('Backup', 'backup_failed', 'Gagal backup database: ' . $e->getMessage(), 'danger');
            return back()->with('error', 'Gagal membuat backup database: ' . $e->getMessage());
        }
    }

    // ─── Backup Files/Storage (ZIP) ───────────────────────────────────────────
    public function backupFiles()
    {
        if (!class_exists('ZipArchive')) {
            return back()->with('error', 'ZipArchive PHP extension tidak tersedia di server ini.');
        }

        $srcDir = storage_path('app/public');

        if (!is_dir($srcDir)) {
            return back()->with('error', 'Direktori storage/app/public tidak ditemukan.');
        }

        $name    = 'files_backup_' . now()->format('Ymd_His') . '.zip';
        $zipPath = storage_path("app/{$this->dir}/{$name}");

        Storage::disk($this->disk)->makeDirectory($this->dir);

        try {
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                return back()->with('error', 'Gagal membuat file ZIP.');
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($srcDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            $count = 0;
            foreach ($iterator as $file) {
                if (!$file->isDir()) {
                    $filePath     = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($srcDir) + 1);
                    $zip->addFile($filePath, $relativePath);
                    $count++;
                }
            }

            $zip->close();

            ActivityLog::record('Backup', 'backup_created', "Backup files berhasil: {$name} ({$count} file).");
            return back()->with('success', "✅ Backup file storage berhasil: <strong>{$name}</strong> ({$count} files)");
        } catch (\Throwable $e) {
            ActivityLog::record('Backup', 'backup_failed', 'Gagal backup files: ' . $e->getMessage(), 'danger');
            return back()->with('error', 'Gagal membuat backup files: ' . $e->getMessage());
        }
    }

    // ─── Download a backup ────────────────────────────────────────────────────
    public function download(string $filename)
    {
        // Sanitize: only allow safe filename chars
        $filename = basename($filename);
        $path     = "{$this->dir}/{$filename}";

        if (!Storage::disk($this->disk)->exists($path)) {
            abort(404, 'File backup tidak ditemukan.');
        }

        ActivityLog::record('Backup', 'backup_downloaded', "Backup didownload: {$filename}.");
        return Storage::disk($this->disk)->download($path, $filename);
    }

    // ─── Delete a backup ─────────────────────────────────────────────────────
    public function delete(string $filename)
    {
        $filename = basename($filename);
        $path     = "{$this->dir}/{$filename}";

        if (!Storage::disk($this->disk)->exists($path)) {
            return back()->with('error', 'File backup tidak ditemukan.');
        }

        Storage::disk($this->disk)->delete($path);

        ActivityLog::record('Backup', 'backup_deleted', "Backup dihapus: {$filename}.", 'warning');
        return back()->with('success', "🗑️ Backup <strong>{$filename}</strong> berhasil dihapus.");
    }

    // ─── Restore from uploaded .sql file ─────────────────────────────────────
    public function restore(Request $request)
    {
        $request->validate([
            'sql_file' => ['required', 'file', 'max:102400'], // max 100MB
        ]);

        $file = $request->file('sql_file');

        // Validate extension manually (mimes validator may not handle .sql well)
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['sql', 'txt'])) {
            return back()->with('error', 'Hanya file .sql yang diizinkan untuk restore.');
        }

        try {
            $sql = file_get_contents($file->getRealPath());
            $this->executeSqlDump($sql);

            ActivityLog::record('Backup', 'restore_success',
                'Database di-restore dari upload: ' . $file->getClientOriginalName(), 'warning');

            return back()->with('success',
                '✅ Database berhasil di-restore dari <strong>' . $file->getClientOriginalName() . '</strong>. Harap login ulang jika sesi terputus.');
        } catch (\Throwable $e) {
            ActivityLog::record('Backup', 'restore_failed',
                'Restore gagal: ' . $e->getMessage(), 'danger');
            return back()->with('error', 'Restore gagal: ' . $e->getMessage());
        }
    }

    // ─── Restore from existing backup in storage ──────────────────────────────
    public function restoreFromStorage(string $filename)
    {
        $filename = basename($filename);
        $path     = "{$this->dir}/{$filename}";
        $fullPath = storage_path("app/{$path}");

        if (!Storage::disk($this->disk)->exists($path)) {
            return back()->with('error', 'File backup tidak ditemukan.');
        }

        if (!Str::endsWith($filename, '.sql')) {
            return back()->with('error', 'Hanya file .sql yang dapat di-restore. File ZIP berisi asset, bukan database.');
        }

        try {
            $sql = file_get_contents($fullPath);
            $this->executeSqlDump($sql);

            ActivityLog::record('Backup', 'restore_success',
                "Database di-restore dari storage: {$filename}", 'warning');

            return back()->with('success', "✅ Database berhasil di-restore dari <strong>{$filename}</strong>.");
        } catch (\Throwable $e) {
            ActivityLog::record('Backup', 'restore_failed',
                "Restore dari {$filename} gagal: " . $e->getMessage(), 'danger');
            return back()->with('error', 'Restore gagal: ' . $e->getMessage());
        }
    }

    // =========================================================================
    //  PRIVATE HELPERS
    // =========================================================================

    /**
     * Generate a full SQL dump using PHP PDO (no mysqldump needed).
     */
    private function generateSqlDump(): string
    {
        $pdo    = DB::getPdo();
        $sql    = "-- SIMM Database Backup\n";
        $sql   .= "-- Generated: " . now()->toDateTimeString() . "\n\n";
        $sql   .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            // Drop + Create
            $create = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
            $sql   .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql   .= array_values($create)[1] . ";\n\n";

            // Rows
            $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(\PDO::FETCH_ASSOC);
            if (!empty($rows)) {
                $cols  = '`' . implode('`, `', array_keys($rows[0])) . '`';
                foreach ($rows as $row) {
                    $vals = array_map(function ($v) use ($pdo) {
                        return $v === null ? 'NULL' : $pdo->quote((string)$v);
                    }, array_values($row));
                    $sql .= "INSERT INTO `{$table}` ({$cols}) VALUES (" . implode(', ', $vals) . ");\n";
                }
                $sql .= "\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        return $sql;
    }

    /**
     * Execute SQL dump content via PDO (split on semicolons).
     */
    private function executeSqlDump(string $sql): void
    {
        DB::unprepared("SET FOREIGN_KEY_CHECKS=0;");

        // Split by semicolons, skip comments
        $statements = array_filter(
            array_map('trim', explode(";\n", $sql)),
            fn($s) => !empty($s) && !str_starts_with($s, '--')
        );

        foreach ($statements as $statement) {
            if (!empty(trim($statement))) {
                DB::unprepared($statement);
            }
        }

        DB::unprepared("SET FOREIGN_KEY_CHECKS=1;");
    }

    /**
     * Format bytes to human-readable size.
     */
    public static function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
