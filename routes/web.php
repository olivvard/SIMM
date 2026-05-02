<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MotorController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\MaintenanceLogController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// ── Public: Auth ──────────────────────────────────────────────────────────────
Route::get('/',        [AuthController::class, 'showLogin'])->name('login');
Route::get('/login',   [AuthController::class, 'showLogin'])->name('login.show');
Route::post('/login',  [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Protected: Admin only ─────────────────────────────────────────────────────
Route::middleware(['admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Motors
    Route::get('/motors/trashed/{id}/restore', [MotorController::class, 'restore'])->name('motors.restore');
    Route::delete('/motors/trashed/{id}/force', [MotorController::class, 'forceDelete'])->name('motors.forceDelete');
    Route::resource('motors', MotorController::class);

    // Schedules
    Route::resource('schedules', ScheduleController::class);

    // Maintenance Logs
    Route::resource('maintenance', MaintenanceLogController::class)
        ->except(['edit', 'update'])
        ->parameters(['maintenance' => 'maintenanceLog']);

    // Reports
    Route::get('/reports',              [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/pdf',   [ReportController::class, 'exportPdf'])->name('reports.pdf');
    Route::post('/reports/export/pdf',  [ReportController::class, 'exportPdf'])->name('reports.pdf.post');
    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');
});
