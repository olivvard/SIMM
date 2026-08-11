<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Menambahkan kolom digital_signature ke tabel maintenance_logs.
 *
 * Kolom ini menyimpan HMAC-SHA256 yang di-generate saat log dibuat.
 * Signature di-generate dari kombinasi: admin_id + motor_id + schedule_id + inspection_date + general_notes.
 * Jika ada yang mengubah data tersebut secara langsung di database (tampered),
 * signature akan tidak cocok saat diverifikasi.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_logs', function (Blueprint $table) {
            // Kolom signature disimpan sebagai string (HMAC-SHA256 = 64 karakter hex)
            $table->string('digital_signature', 64)->nullable()->after('photo_url');
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->dropColumn('digital_signature');
        });
    }
};
