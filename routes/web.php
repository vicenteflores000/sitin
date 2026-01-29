<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GlpiSyncController;
use App\Http\Controllers\LocacionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'active.user'])
    ->name('dashboard');

Route::get('/ticket', [TicketController::class, 'create'])
    ->middleware(['auth'])
    ->name('ticket.create');
Route::post('/ticket', [TicketController::class, 'store'])
    ->middleware('auth');


Route::middleware(['auth', 'admin'])->group(function () {
    Route::post('/admin/glpi/sync-locations', [GlpiSyncController::class, 'syncLocations']);
    Route::post('/dashboard/sync-estados', [DashboardController::class, 'syncEstados'])
        ->name('dashboard.sync-estados');
    Route::post('/dashboard/reenviar-pendientes', [DashboardController::class, 'reenviarPendientes'])
        ->name('dashboard.reenviar-pendientes');
});

Route::middleware(['auth', 'admin'])
    ->prefix('admin/profiles')
    ->group(function () {

        Route::get('/', [ProfileController::class, 'index'])
            ->name('admin.profiles.index');

        Route::get('/create', [ProfileController::class, 'create'])
            ->name('admin.profiles.create');

        Route::post('/', [ProfileController::class, 'store'])
            ->name('admin.profiles.store');

        Route::get('/{user}/edit', [ProfileController::class, 'edit'])
            ->name('admin.profiles.edit');

        Route::put('/{user}', [ProfileController::class, 'update'])
            ->name('admin.profiles.update');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

Route::middleware(['auth', 'admin'])
    ->prefix('admin/locaciones')
    ->group(function () {

        Route::get('/', [LocacionController::class, 'index'])
            ->name('admin.locaciones.index');

        Route::get('/create', [LocacionController::class, 'create'])
            ->name('admin.locaciones.create');
        Route::post('/', [LocacionController::class, 'store'])
            ->name('admin.locaciones.store');

        Route::get('/{user}/edit', [LocacionController::class, 'edit'])
            ->name('admin.locaciones.edit');

        Route::put('/{user}', [LocacionController::class, 'update'])
            ->name('admin.locaciones.update');
        Route::get('/locaciones', [LocacionController::class, 'edit'])->name('locacion.edit');
        Route::patch('/locaciones', [LocacionController::class, 'update'])->name('locacion.update');
        Route::delete('/locaciones', [LocacionController::class, 'destroy'])->name('locacion.destroy');
    });
require __DIR__ . '/auth.php';
