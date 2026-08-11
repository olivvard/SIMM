<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MotorController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\MaintenanceLogController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ── Public: Auth ──────────────────────────────────────────────────────────────
Route::get('/',        [AuthController::class, 'showLogin'])->name('login');
Route::get('/login',   [AuthController::class, 'showLogin'])->name('login.show');
Route::post('/login',  [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register',   [AuthController::class, 'showRegister'])->name('register.show');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::get('/captcha',  [AuthController::class, 'getCaptcha'])->name('captcha');

// ── Protected: Semua user yang sudah login ────────────────────────────────────
Route::middleware(['admin'])->group(function () {

    // Profile (semua role)
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::patch('/profile/picture', [ProfileController::class, 'updatePicture'])->name('profile.update_picture');

    // Dashboard (semua role)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Reports (semua role — Read Only / Export)
    Route::get('/reports',              [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/pdf',   [ReportController::class, 'exportPdf'])->name('reports.pdf');
    Route::post('/reports/export/pdf',  [ReportController::class, 'exportPdf'])->name('reports.pdf.post');
    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');

    // ── ADMIN ONLY ────────────────────────────────────────────────────────────
    Route::middleware(['role:admin'])->group(function () {

        // Motors (CRUD)
        Route::get('/motors', [MotorController::class, 'index'])->name('motors.index');
        Route::get('/motors/create', [MotorController::class, 'create'])->name('motors.create');
        Route::post('/motors', [MotorController::class, 'store'])->name('motors.store');
        Route::get('/motors/trashed/{id}/restore', [MotorController::class, 'restore'])->name('motors.restore');
        Route::delete('/motors/trashed/{id}/force', [MotorController::class, 'forceDelete'])->name('motors.forceDelete');
        Route::get('/motors/{motor}/edit', [MotorController::class, 'edit'])->name('motors.edit');
        Route::put('/motors/{motor}', [MotorController::class, 'update'])->name('motors.update');
        Route::delete('/motors/{motor}', [MotorController::class, 'destroy'])->name('motors.destroy');
        // Show Motor Detail (Must be defined AFTER /create)
        Route::get('/motors/{motor}', [MotorController::class, 'show'])->name('motors.show');

        // Schedules (CRUD)
        Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
        Route::get('/schedules/create', [ScheduleController::class, 'create'])->name('schedules.create');
        Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
        Route::get('/schedules/{schedule}/edit', [ScheduleController::class, 'edit'])->name('schedules.edit');
        Route::put('/schedules/{schedule}', [ScheduleController::class, 'update'])->name('schedules.update');
        Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
        // Show Schedule Detail (Must be defined AFTER /create)
        Route::get('/schedules/{schedule}', [ScheduleController::class, 'show'])->name('schedules.show');
    });

    // ── TEKNISI ONLY ──────────────────────────────────────────────────────────
    Route::middleware(['role:teknisi'])->group(function () {

        // Maintenance Logs (CRUD)
        Route::get('/maintenance', [MaintenanceLogController::class, 'index'])->name('maintenance.index');
        Route::get('/maintenance/create', [MaintenanceLogController::class, 'create'])->name('maintenance.create');
        Route::post('/maintenance', [MaintenanceLogController::class, 'store'])->name('maintenance.store');
        Route::delete('/maintenance/{maintenanceLog}', [MaintenanceLogController::class, 'destroy'])->name('maintenance.destroy');
        // Show Maintenance Detail (Must be defined AFTER /create)
        Route::get('/maintenance/{maintenanceLog}', [MaintenanceLogController::class, 'show'])->name('maintenance.show');
    });
});