<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    protected $fillable = [
        'admin_id',
        'module',
        'action',
        'description',
        'status',
        'ip_address',
        'user_agent',
    ];

    // =========================================================================
    //  STATIC HELPER — Catat aktivitas dari mana saja dengan satu baris kode
    // =========================================================================

    /**
     * Catat satu baris aktivitas ke database.
     *
     * Contoh penggunaan:
     *   ActivityLog::record('Auth', 'login', 'Admin "john" berhasil login.');
     *   ActivityLog::record('Integrity', 'tampered_detected', 'Log #3 dimanipulasi!', 'danger');
     *
     * @param string      $module       Nama modul (Auth, Maintenance, Motor, Schedule, Integrity)
     * @param string      $action       Nama aksi singkat (login, logout, create, delete, ...)
     * @param string      $description  Pesan deskripsi lengkap
     * @param string      $status       Tingkat keparahan: normal | warning | danger
     * @param int|null    $adminId      Override admin_id (default: ambil dari Auth::id())
     */
    public static function record(
        string $module,
        string $action,
        string $description,
        string $status = 'normal',
        ?int $adminId = null
    ): void {
        static::create([
            'admin_id'    => $adminId ?? Auth::id(),
            'module'      => $module,
            'action'      => $action,
            'description' => $description,
            'status'      => $status,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);
    }

    // =========================================================================
    //  RELATIONS
    // =========================================================================

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
