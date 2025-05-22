<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // User Management Routes - hanya admin yang bisa akses
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class);
    });
    
    // Alternative: menggunakan permission middleware
    // Route::middleware('permission:manage_users')->group(function () {
    //     Route::resource('users', UserController::class);
    // });
    
    // Route::middleware('permission:manage_roles')->group(function () {
    //     Route::resource('roles', RoleController::class);
    // });
});

require __DIR__.'/auth.php';