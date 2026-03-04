<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketAssignment;
use App\Models\TicketAction;
use App\Models\TicketPart;
use App\Models\TicketResolution;
use App\Models\TicketStatusEvent;
use App\Models\User;
use App\Mail\TicketAssigned;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminTicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with([
            'locacion.padre',
            'latestStatusEvent',
            'currentAssignment.technician',
            'resolution',
            'parts',
            'actions.creator',
        ])->orderByDesc('created_at')->get();

        $admins = User::where('role', 'admin')->orderBy('name')->get();

        return view('admin.tickets.index', compact('tickets', 'admins'));
    }

    public function assignToMe(Ticket $ticket): RedirectResponse
    {
        $this->assignTicket($ticket, auth()->id());

        return back();
    }

    public function assignUser(Request $request, Ticket $ticket): RedirectResponse
    {
        $data = $request->validate([
            'technician_id' => 'required|exists:users,id',
        ]);

        $technician = User::find($data['technician_id']);
        if (! $technician || $technician->role !== 'admin') {
            return back()->withErrors(['technician_id' => 'El usuario seleccionado no es técnico administrador.']);
        }

        $this->assignTicket($ticket, $technician->id);

        return back();
    }

    public function updateStatus(Request $request, Ticket $ticket): RedirectResponse
    {
        if (! $this->canManageTicket($ticket)) {
            return back()->withErrors(['status' => 'Solo el técnico asignado puede cambiar el estado.']);
        }

        $data = $request->validate([
            'to_status' => 'required|in:nuevo,asignado,en_progreso,standby,resuelto,cerrado',
            'reason' => 'nullable|string|max:255',
        ]);

        if ($data['to_status'] === 'standby' && empty($data['reason'])) {
            return back()->withErrors(['reason' => 'Debe indicar un motivo para Standby.']);
        }

        $this->changeStatus($ticket, $data['to_status'], $data['reason'] ?? null);

        return back();
    }

    public function resolve(Request $request, Ticket $ticket): RedirectResponse
    {
        if (! $this->canManageTicket($ticket)) {
            return back()->withErrors(['resolution' => 'Solo el técnico asignado puede cerrar este ticket.']);
        }

        $data = $request->validate([
            'resolution_text' => 'required|string',
        ]);

        $categoriaInterna = $ticket->categoria_interna;
        $problemType = $ticket->problem_type;
        $rootCause = $ticket->root_cause;
        $actionsCount = $ticket->actions()->count();

        if ($actionsCount < 1) {
            return back()->withErrors(['resolution' => 'Registra al menos una acción antes de cerrar el ticket.']);
        }

        if (! $categoriaInterna || ! $problemType || ! $rootCause) {
            return back()->withErrors(['resolution' => 'Completa la clasificación antes de cerrar el ticket.']);
        }

        $ticket->update([
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        TicketResolution::updateOrCreate(
            ['ticket_id' => $ticket->id],
            [
                'categoria_interna' => $categoriaInterna,
                'root_cause' => $rootCause,
                'resolution_text' => $data['resolution_text'],
                'resolved_by' => auth()->id(),
                'resolved_at' => now(),
            ]
        );

        $this->changeStatus($ticket, 'resuelto', null);

        return back();
    }

    public function addAction(Request $request, Ticket $ticket): RedirectResponse
    {
        if (! $this->canManageTicket($ticket)) {
            return back()->withErrors(['action' => 'Solo el técnico asignado puede registrar acciones.']);
        }

        $data = $request->validate([
            'action_type' => 'required|in:repuesto,instalacion,compra,otro',
            'description' => 'required|string',
            'status' => 'required|in:pendiente,en_progreso,completado',
        ]);

        TicketAction::create([
            'ticket_id' => $ticket->id,
            'action_type' => $data['action_type'],
            'description' => $data['description'],
            'status' => $data['status'],
            'created_by' => auth()->id(),
        ]);

        return back();
    }

    public function updateClassification(Request $request, Ticket $ticket): RedirectResponse
    {
        if (! $this->canManageTicket($ticket)) {
            return back()->withErrors(['classification' => 'Solo el técnico asignado puede clasificar este ticket.']);
        }

        $data = $request->validate([
            'categoria_interna' => 'required|string|max:255',
            'problem_type' => 'required|string|max:255',
            'root_cause' => 'required|string|max:255',
        ]);

        $ticket->update([
            'categoria_interna' => trim($data['categoria_interna']),
            'problem_type' => trim($data['problem_type']),
            'root_cause' => trim($data['root_cause']),
        ]);

        return back();
    }

    public function addPart(Request $request, Ticket $ticket): RedirectResponse
    {
        if (! $this->canManageTicket($ticket)) {
            return back()->withErrors(['part' => 'Solo el técnico asignado puede agregar repuestos.']);
        }

        $data = $request->validate([
            'part_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'status' => 'required|in:required,used,missing',
        ]);

        TicketPart::create([
            'ticket_id' => $ticket->id,
            'part_name' => $data['part_name'],
            'quantity' => $data['quantity'],
            'status' => $data['status'],
            'noted_at' => now(),
        ]);

        return back();
    }

    protected function changeStatus(Ticket $ticket, string $toStatus, ?string $reason): void
    {
        $current = $ticket->latestStatusEvent;
        if ($current && $current->ended_at === null) {
            $current->update(['ended_at' => now()]);
        }

        TicketStatusEvent::create([
            'ticket_id' => $ticket->id,
            'from_status' => $current?->to_status,
            'to_status' => $toStatus,
            'started_at' => now(),
            'changed_by' => auth()->id(),
            'reason' => $reason,
        ]);
    }

    protected function assignTicket(Ticket $ticket, int $technicianId): void
    {
        TicketAssignment::where('ticket_id', $ticket->id)
            ->whereNull('unassigned_at')
            ->update(['unassigned_at' => now()]);

        TicketAssignment::create([
            'ticket_id' => $ticket->id,
            'technician_id' => $technicianId,
            'assigned_at' => now(),
            'assigned_by' => auth()->id(),
        ]);

        $this->changeStatus($ticket, 'asignado', null);

        $ticket->loadMissing('locacion.padre', 'currentAssignment.technician');
        $this->sendAssignmentNotification($ticket);
    }

    protected function sendAssignmentNotification(Ticket $ticket): void
    {
        $technician = $ticket->currentAssignment?->technician;
        if (! $technician) {
            return;
        }

        try {
            Mail::to('informatica@mdonihue.cl')
                ->send(new TicketAssigned($ticket, $technician));
        } catch (\Throwable $exception) {
            Log::warning('No se pudo enviar correo de asignación de ticket', [
                'ticket_id' => $ticket->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    protected function canManageTicket(Ticket $ticket): bool
    {
        $assignment = $ticket->currentAssignment;

        if (! $assignment) {
            return false;
        }

        return $assignment->technician_id === auth()->id();
    }
}
