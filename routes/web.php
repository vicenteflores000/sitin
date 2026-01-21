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
    });



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
