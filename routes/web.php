<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MotorController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\MaintenanceLogController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IntegrityController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BackupController;
use Illuminate\Support\Facades\Route;

// ── Public: Auth ──────────────────────────────────────────────────────────────
Route::get('/',        [AuthController::class, 'showLogin'])->name('login');
Route::get('/login',   [AuthController::class, 'showLogin'])->name('login.show');
Route::post('/login',  [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register',   [AuthController::class, 'showRegister'])->name('register.show');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::get('/captcha',  [AuthController::class, 'getCaptcha'])->name('captcha');

// ── Shared Protected: Common Pages (Admin & Teknisi) ──────────────────────────
Route::middleware(['admin'])->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::patch('/profile/picture', [ProfileController::class, 'updatePicture'])->name('profile.update_picture');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Integrity Check & Activity Logs
    Route::get('/integrity-check', [IntegrityController::class, 'check'])->name('integrity.check');
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

    // Reports (Read Only / Export)
    Route::get('/reports',              [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/pdf',   [ReportController::class, 'exportPdf'])->name('reports.pdf');
    Route::post('/reports/export/pdf',  [ReportController::class, 'exportPdf'])->name('reports.pdf.post');
    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');
});

// ── Motors Routes ─────────────────────────────────────────────────────────────
Route::middleware(['admin'])->group(function () {
    Route::get('/motors', [MotorController::class, 'index'])->name('motors.index');

    // Admin Only CUD for Motors
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/motors/create', [MotorController::class, 'create'])->name('motors.create');
        Route::post('/motors', [MotorController::class, 'store'])->name('motors.store');
        Route::get('/motors/trashed/{id}/restore', [MotorController::class, 'restore'])->name('motors.restore');
        Route::delete('/motors/trashed/{id}/force', [MotorController::class, 'forceDelete'])->name('motors.forceDelete');
        Route::get('/motors/{motor}/edit', [MotorController::class, 'edit'])->name('motors.edit');
        Route::put('/motors/{motor}', [MotorController::class, 'update'])->name('motors.update');
        Route::delete('/motors/{motor}', [MotorController::class, 'destroy'])->name('motors.destroy');
    });

    // Show Motor Detail (Must be defined AFTER /create)
    Route::get('/motors/{motor}', [MotorController::class, 'show'])->name('motors.show');
});

// ── Schedules Routes ──────────────────────────────────────────────────────────
Route::middleware(['admin'])->group(function () {
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');

    // Admin Only CUD for Schedules
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/schedules/create', [ScheduleController::class, 'create'])->name('schedules.create');
        Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
        Route::get('/schedules/{schedule}/edit', [ScheduleController::class, 'edit'])->name('schedules.edit');
        Route::put('/schedules/{schedule}', [ScheduleController::class, 'update'])->name('schedules.update');
        Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
    });

    // Show Schedule Detail (Must be defined AFTER /create)
    Route::get('/schedules/{schedule}', [ScheduleController::class, 'show'])->name('schedules.show');
});

// ── Maintenance Logs Routes ───────────────────────────────────────────────────
Route::middleware(['admin'])->group(function () {
    Route::get('/maintenance', [MaintenanceLogController::class, 'index'])->name('maintenance.index');

    // Teknisi Only CUD for Maintenance
    Route::middleware(['role:teknisi'])->group(function () {
        Route::get('/maintenance/create', [MaintenanceLogController::class, 'create'])->name('maintenance.create');
        Route::post('/maintenance', [MaintenanceLogController::class, 'store'])->name('maintenance.store');
        Route::delete('/maintenance/{maintenanceLog}', [MaintenanceLogController::class, 'destroy'])->name('maintenance.destroy');
    });

    // Show Maintenance Detail (Must be defined AFTER /create)
    Route::get('/maintenance/{maintenanceLog}', [MaintenanceLogController::class, 'show'])->name('maintenance.show');
});

// ── Backup & Recovery Routes (Admin only) ─────────────────────────────────────
Route::middleware(['admin', 'role:admin'])->prefix('backup')->name('backup.')->group(function () {
    Route::get('/',                           [BackupController::class, 'index'])->name('index');
    Route::post('/database',                  [BackupController::class, 'backupDatabase'])->name('database');
    Route::post('/files',                     [BackupController::class, 'backupFiles'])->name('files');
    Route::get('/download/{filename}',        [BackupController::class, 'download'])->name('download');
    Route::delete('/delete/{filename}',       [BackupController::class, 'delete'])->name('delete');
    Route::post('/restore',                   [BackupController::class, 'restore'])->name('restore');
    Route::post('/restore/{filename}',        [BackupController::class, 'restoreFromStorage'])->name('restore.storage');
});