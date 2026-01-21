<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GlpiSyncController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');
Route::post('/dashboard/sync-estados', [DashboardController::class, 'syncEstados'])
    ->middleware('auth')
    ->name('dashboard.sync-estados');
Route::post(
    '/dashboard/reenviar-pendientes',
    [DashboardController::class, 'reenviarPendientes']
)->name('dashboard.reenviar-pendientes');

Route::get('/ticket', [TicketController::class, 'create'])
    ->middleware(['auth'])
    ->name('ticket.create');
Route::post('/ticket', [TicketController::class, 'store'])
    ->middleware('auth');

Route::post('/admin/glpi/sync-locations', [GlpiSyncController::class, 'syncLocations'])
    ->middleware('auth');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
