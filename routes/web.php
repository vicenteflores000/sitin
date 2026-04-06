<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocacionController;
use App\Http\Controllers\PrinterController;
use App\Http\Controllers\AdminTicketController;
use App\Http\Controllers\AdminCalendarController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminErrorLogController;
use App\Http\Controllers\AllowedDomainController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketAttachmentController;
use App\Http\Controllers\TicketMessageController;
use App\Http\Controllers\AssistedTicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TicketController::class, 'create'])
    ->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'active.user', 'force.password'])
    ->name('dashboard');

Route::get('/admin-dashboard', [DashboardController::class, 'admin'])
    ->middleware(['auth', 'active.user', 'force.password', 'admin'])
    ->name('admin.dashboard');
Route::get('/admin-dashboard/tecnicos/{technician}', [DashboardController::class, 'adminTech'])
    ->middleware(['auth', 'active.user', 'force.password', 'admin'])
    ->name('admin.dashboard.tech');

Route::get('/ticket', [TicketController::class, 'create'])
    ->name('ticket.create');
Route::post('/ticket', [TicketController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('ticket.store');

Route::get('/tickets/attachments/{attachment}', [TicketAttachmentController::class, 'show'])
    ->middleware(['auth', 'active.user', 'force.password'])
    ->name('tickets.attachments.show');


Route::middleware(['auth', 'active.user', 'force.password', 'admin'])
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

Route::middleware(['auth', 'active.user', 'force.password'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'updateSelf'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::get('/tickets/{ticket}/messages', [TicketMessageController::class, 'index'])
        ->name('tickets.messages.index');
    Route::post('/tickets/{ticket}/messages', [TicketMessageController::class, 'store'])
        ->name('tickets.messages.store');
});

Route::middleware(['auth', 'active.user', 'force.password', 'admin'])
    ->prefix('admin/locaciones')
    ->group(function () {

        Route::get('/', [LocacionController::class, 'index'])
            ->name('admin.locaciones.index');

        Route::get('/create', [LocacionController::class, 'create'])
            ->name('admin.locaciones.create');
        Route::post('/', [LocacionController::class, 'store'])
            ->name('admin.locaciones.store');

        Route::get('/{locacion}/edit', [LocacionController::class, 'edit'])
            ->name('admin.locaciones.edit');

        Route::put('/{locacion}', [LocacionController::class, 'update'])
            ->name('admin.locaciones.update');
        Route::delete('/{locacion}', [LocacionController::class, 'destroy'])
            ->name('admin.locaciones.destroy');
        Route::post('/{locacion}/funcionarios', [LocacionController::class, 'assignFuncionario'])
            ->name('admin.locaciones.funcionarios.assign');
        Route::delete('/{locacion}/funcionarios/{user}', [LocacionController::class, 'removeFuncionario'])
            ->name('admin.locaciones.funcionarios.remove');
        Route::put('/{locacion}/domains', [LocacionController::class, 'updateDomains'])
            ->name('admin.locaciones.domains.update');
    });

Route::middleware(['auth', 'active.user', 'force.password', 'admin'])
    ->prefix('admin/dominios')
    ->group(function () {
        Route::post('/', [AllowedDomainController::class, 'store'])
            ->name('admin.domains.store');
        Route::delete('/{domain}', [AllowedDomainController::class, 'destroy'])
            ->name('admin.domains.destroy');
    });

Route::middleware(['auth', 'active.user', 'force.password', 'admin'])
    ->prefix('admin/impresoras')
    ->group(function () {
        Route::get('/', [PrinterController::class, 'index'])
            ->name('admin.printers.index');
    });

Route::middleware(['auth', 'active.user', 'force.password', 'admin'])
    ->prefix('admin/tickets')
    ->group(function () {
        Route::get('/', [AdminTicketController::class, 'index'])
            ->name('admin.tickets.index');
        Route::get('/asistido', [AssistedTicketController::class, 'create'])
            ->name('admin.tickets.assisted.create');
        Route::get('/asistido/users', [AssistedTicketController::class, 'users'])
            ->name('admin.tickets.assisted.users');
        Route::post('/asistido', [AssistedTicketController::class, 'store'])
            ->name('admin.tickets.assisted.store');
        Route::post('/{ticket}/assign', [AdminTicketController::class, 'assignToMe'])
            ->name('admin.tickets.assign');
        Route::post('/{ticket}/assign-user', [AdminTicketController::class, 'assignUser'])
            ->name('admin.tickets.assign-user');
        Route::post('/{ticket}/assign-multiple', [AdminTicketController::class, 'syncAssignments'])
            ->name('admin.tickets.assign-multiple');
        Route::post('/{ticket}/status', [AdminTicketController::class, 'updateStatus'])
            ->name('admin.tickets.status');
        Route::post('/{ticket}/resolve', [AdminTicketController::class, 'resolve'])
            ->name('admin.tickets.resolve');
        Route::post('/{ticket}/quick-close', [AdminTicketController::class, 'quickClose'])
            ->name('admin.tickets.quick-close');
        Route::post('/{ticket}/parts', [AdminTicketController::class, 'addPart'])
            ->name('admin.tickets.parts');
        Route::post('/{ticket}/actions', [AdminTicketController::class, 'addAction'])
            ->name('admin.tickets.actions');
        Route::post('/{ticket}/classification', [AdminTicketController::class, 'updateClassification'])
            ->name('admin.tickets.classification');
    });

Route::middleware(['auth', 'active.user', 'force.password', 'admin'])
    ->prefix('admin/logs')
    ->group(function () {
        Route::get('/', [AdminErrorLogController::class, 'index'])
            ->name('admin.logs.index');
    });

Route::middleware(['auth', 'active.user', 'force.password', 'admin'])
    ->prefix('admin/calendario')
    ->group(function () {
        Route::get('/', [AdminCalendarController::class, 'index'])
            ->name('admin.calendar.index');
        Route::get('/events', [AdminCalendarController::class, 'events'])
            ->name('admin.calendar.events');
        Route::post('/events', [AdminCalendarController::class, 'store'])
            ->name('admin.calendar.store');
        Route::put('/events/{schedule}', [AdminCalendarController::class, 'update'])
            ->name('admin.calendar.update');
        Route::delete('/events/{schedule}', [AdminCalendarController::class, 'destroy'])
            ->name('admin.calendar.destroy');
    });
require __DIR__ . '/auth.php';
