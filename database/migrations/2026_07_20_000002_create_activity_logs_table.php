<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel activity_logs mencatat semua aktivitas admin di dalam sistem,
 * termasuk login, logout, CRUD data, dan deteksi tampering (illegal access).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // Admin yang melakukan aksi (nullable untuk aksi sistem/anonymous)
            $table->foreignId('admin_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Modul tempat aksi terjadi (e.g., Auth, Maintenance, Motor, Integrity)
            $table->string('module', 50);

            // Nama aksi singkat (e.g., login, logout, create, delete, tampered_detected)
            $table->string('action', 50);

            // Deskripsi lengkap aksi yang dilakukan
            $table->text('description');

            // Status keparahan: normal | warning | danger
            $table->enum('status', ['normal', 'warning', 'danger'])->default('normal');

            // Network info untuk audit
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
