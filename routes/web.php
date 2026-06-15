<?php

use App\Http\Controllers\DashboardRedirectController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/public.php';

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', DashboardRedirectController::class)->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    require __DIR__.'/platform.php';
    require __DIR__.'/organizer.php';
});

require __DIR__.'/auth.php';
