<?php

use App\Http\Controllers\Organizer\DashboardController;
use App\Http\Controllers\Organizer\KycSubmissionController;
use App\Http\Controllers\Organizer\TeamController;
use App\Http\Middleware\EnsureOrganizationAccess;
use Illuminate\Support\Facades\Route;

Route::prefix('organizer/{organization:slug}')
    ->middleware([EnsureOrganizationAccess::class])
    ->name('organizer.')
    ->group(function (): void {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
        Route::get('/kyc', [KycSubmissionController::class, 'edit'])->name('kyc.edit');
        Route::put('/kyc', [KycSubmissionController::class, 'update'])->name('kyc.update');
        Route::get('/team', [TeamController::class, 'index'])->name('team.index');
        Route::post('/team/invitations', [TeamController::class, 'store'])->name('team.store');
    });
