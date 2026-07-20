<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah kolom payload_snapshot ke maintenance_logs.
 *
 * Kolom ini menyimpan snapshot JSON dari nilai-nilai field yang digunakan
 * untuk membuat digital signature. Ketika tampering terdeteksi,
 * snapshot ini dibandingkan dengan nilai saat ini untuk menampilkan
 * perubahan yang terjadi (before/after diff).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->json('payload_snapshot')->nullable()->after('digital_signature');
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->dropColumn('payload_snapshot');
        });
    }
};
