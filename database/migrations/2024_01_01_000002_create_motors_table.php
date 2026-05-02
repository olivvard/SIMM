<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('motors', function (Blueprint $table) {
            $table->id();
            $table->string('motor_code')->unique();
            $table->enum('location', ['MA-1', 'MA-2', 'MA-3', 'MA-4']);
            $table->enum('area', ['DHDT', 'COOKER', 'HVU', 'DCU', 'RX', 'PL-2', 'PL-1', 'H2P', 'HCC', 'PLTU', 'WTP', 'HDC', 'EX-BOILER', 'JETTY-1', 'JETTY-3', 'LPG', 'JETTY-2', 'PUMP-HOUSE', 'SEPARATOR']);
            $table->enum('category', ['<100', '100-500', '>500'])->comment('HP');
            $table->date('installation_date');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('motors');
    }
};
