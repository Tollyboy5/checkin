<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\AttendanceRecordController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RosterController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');
Route::post('/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.check-in');
Route::post('/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::redirect('/', '/admin/dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('staff', StaffController::class)->except(['show']);
        Route::resource('shifts', ShiftController::class)->except(['show']);
        Route::resource('rosters', RosterController::class)->except(['show']);
        Route::resource('attendance', AttendanceRecordController::class)->only(['index', 'edit', 'update']);
    });
});
