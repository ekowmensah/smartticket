<?php

use App\Http\Controllers\Platform\DashboardController;
use App\Http\Controllers\Platform\AuditLogController;
use App\Http\Controllers\Platform\OrganizationApprovalController;
use App\Http\Controllers\Platform\OrganizationController;
use App\Http\Controllers\Platform\OrganizationKycReviewController;
use App\Http\Controllers\Platform\SettingsController;
use App\Http\Middleware\EnsurePlatformAccess;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->middleware([EnsurePlatformAccess::class])
    ->name('platform.')
    ->group(function (): void {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
        Route::get('/organizations', [OrganizationController::class, 'index'])->name('organizations.index');
        Route::get('/organizations/{organization:slug}', [OrganizationController::class, 'show'])->name('organizations.show');
        Route::patch('/organizations/{organization:slug}/review', [OrganizationApprovalController::class, 'update'])->name('organizations.review');
        Route::patch('/organizations/{organization:slug}/kyc-review', [OrganizationKycReviewController::class, 'update'])->name('organizations.kyc.review');
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    });
