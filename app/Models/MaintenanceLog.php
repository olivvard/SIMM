<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MaintenanceLog
 *
 * @property int         $id
 * @property int         $motor_id
 * @property int         $schedule_id
 * @property int         $admin_id
 * @property \Illuminate\Support\Carbon $inspection_date
 * @property string|null $general_notes
 * @property string|null $photo_url
 * @property string|null $digital_signature  — HMAC-SHA256 untuk deteksi tampering
 * @property array|null  $payload_snapshot   — Snapshot nilai asli saat signature dibuat
 */
class MaintenanceLog extends Model
{
    protected $fillable = [
        'motor_id',
        'schedule_id',
        'admin_id',
        'inspection_date',
        'general_notes',
        'photo_url',
        'digital_signature',
        'payload_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'inspection_date'  => 'date',
            'payload_snapshot' => 'array',   // otomatis encode/decode JSON
        ];
    }

    // =========================================================================
    //  DIGITAL SIGNATURE — AUDIT TRAIL & DATA INTEGRITY
    // =========================================================================

    /**
     * Bangun payload string dari field-field krusial.
     * Digunakan sebagai input untuk HMAC-SHA256.
     */
    private function buildPayload(): string
    {
        return implode('|', [
            $this->admin_id,
            $this->motor_id,
            $this->schedule_id,
            $this->inspection_date instanceof \Carbon\Carbon
                ? $this->inspection_date->toDateString()
                : (string) $this->inspection_date,
            (string) ($this->general_notes ?? ''),
        ]);
    }

    /**
     * Ambil nilai snapshot saat ini dari model sebagai array.
     * Snapshot ini disimpan ke DB bersamaan dengan digital_signature.
     */
    private function buildSnapshot(): array
    {
        return [
            'admin_id'        => $this->admin_id,
            'motor_id'        => $this->motor_id,
            'schedule_id'     => $this->schedule_id,
            'inspection_date' => $this->inspection_date instanceof \Carbon\Carbon
                ? $this->inspection_date->toDateString()
                : (string) $this->inspection_date,
            'general_notes'   => $this->general_notes ?? '',
        ];
    }

    /**
     * Generate Digital Signature (HMAC-SHA256).
     * Simpan juga snapshot nilai asli ke $this->payload_snapshot.
     *
     * @return string  64-karakter hex string
     */
    public function generateSignature(): string
    {
        // Simpan snapshot nilai saat ini bersamaan dengan generate signature
        $this->payload_snapshot = $this->buildSnapshot();

        return hash_hmac('sha256', $this->buildPayload(), config('app.key'));
    }

    /**
     * Verifikasi apakah data log masih valid (belum ditamper).
     *
     * @return bool  true = data asli, false = data sudah dimanipulasi
     */
    public function verifySignature(): bool
    {
        if (empty($this->digital_signature)) {
            return false;
        }

        $freshPayload    = $this->buildPayload();
        $freshSignature  = hash_hmac('sha256', $freshPayload, config('app.key'));

        return hash_equals($this->digital_signature, $freshSignature);
    }

    // =========================================================================
    //  BEFORE / AFTER DIFF — Tampilkan apa yang berubah
    // =========================================================================

    /**
     * Label human-readable untuk setiap field di payload.
     */
    private array $fieldLabels = [
        'admin_id'        => 'Admin ID',
        'motor_id'        => 'Motor ID',
        'schedule_id'     => 'Schedule ID',
        'inspection_date' => 'Tanggal Inspeksi',
        'general_notes'   => 'Catatan Umum',
    ];

    /**
     * Bandingkan snapshot asli dengan nilai saat ini di DB.
     * Kembalikan array berisi field-field yang berubah beserta nilai before/after.
     *
     * Contoh output:
     * [
     *   [
     *     'field'  => 'Catatan Umum',
     *     'before' => 'Motor normal',
     *     'after'  => 'DATA DIUBAH!'
     *   ]
     * ]
     *
     * @return array  Kosong jika tidak ada yang berubah atau snapshot belum ada.
     */
    public function getDiff(): array
    {
        if (empty($this->payload_snapshot)) {
            return [];
        }

        $snapshot = $this->payload_snapshot;
        $current  = $this->buildSnapshot();
        $diffs    = [];

        foreach ($snapshot as $field => $originalValue) {
            $currentValue = $current[$field] ?? '';

            if ((string) $originalValue !== (string) $currentValue) {
                $diffs[] = [
                    'field'  => $this->fieldLabels[$field] ?? $field,
                    'before' => $originalValue === '' ? '(kosong)' : $originalValue,
                    'after'  => $currentValue  === '' ? '(kosong)' : $currentValue,
                ];
            }
        }

        return $diffs;
    }

    // =========================================================================
    //  RELATIONS
    // =========================================================================

    public function motor()
    {
        return $this->belongsTo(Motor::class)->withTrashed();
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function activityDetails()
    {
        return $this->hasMany(MaintenanceActivityDetail::class);
    }
}
