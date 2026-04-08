<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/home', function () {
    return view('home');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/mark', [AttendanceController::class, 'store'])
        ->middleware('permission:mark-attendance')
        ->name('attendance.store');

    Route::get('/leave-requests', [LeaveRequestController::class, 'index'])->name('leave.index');
    Route::post('/leave-requests', [LeaveRequestController::class, 'store'])
        ->middleware('permission:submit-leave')
        ->name('leave.store');
    Route::put('/leave-requests/{leaveRequest}', [LeaveRequestController::class, 'update'])
        ->middleware('permission:approve-leave')
        ->name('leave.update');

    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [TaskController::class, 'store'])
        ->middleware('permission:assign-task')
        ->name('tasks.store');
    Route::post('/tasks/{task}/respond', [TaskController::class, 'respond'])
        ->middleware('permission:submit-task')
        ->name('tasks.respond');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])
        ->middleware('permission:approve-task')
        ->name('tasks.update');
    Route::get('/tasks/{task}/download/{type}', [TaskController::class, 'download'])->name('tasks.download');

    Route::get('/admin/attendance', [App\Http\Controllers\AdminController::class, 'attendance'])
        ->middleware('permission:view-attendance')
        ->name('admin.attendance');

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/students', [App\Http\Controllers\AdminController::class, 'students'])->name('admin.students');
        Route::post('/admin/users/{user}/role', [App\Http\Controllers\AdminController::class, 'updateUserRole'])->name('admin.users.updateRole');
        Route::delete('/admin/users/{user}', [App\Http\Controllers\AdminController::class, 'destroyUser'])->name('admin.users.destroy');
        Route::post('/admin/attendance', [App\Http\Controllers\AdminController::class, 'attendanceStore'])->name('admin.attendance.store');
        Route::put('/admin/attendance/{attendance}', [App\Http\Controllers\AdminController::class, 'attendanceUpdate'])->name('admin.attendance.update');
        Route::delete('/admin/attendance/{attendance}', [App\Http\Controllers\AdminController::class, 'attendanceDestroy'])->name('admin.attendance.destroy');
    });

    Route::get('/admin/reports', [App\Http\Controllers\AdminController::class, 'reports'])
        ->middleware('permission:view-reports')
        ->name('admin.reports');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
