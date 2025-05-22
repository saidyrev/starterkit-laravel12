<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update.post'); // Alternative
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Additional profile routes
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::post('/profile/upload-avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.upload-avatar');
    
    // User Management - hanya admin yang bisa akses
    Route::middleware('role:admin')->group(function () {
        // Users Resource Routes
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class);
    });
});

require __DIR__.'/auth.php';