<?php

use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\OrganizationInvitationController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/invitations/{token}', [OrganizationInvitationController::class, 'show'])->name('invitations.show');
Route::post('/invitations/{token}', [OrganizationInvitationController::class, 'store'])->name('invitations.accept');
