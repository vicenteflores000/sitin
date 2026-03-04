<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketSchedule;
use App\Mail\TicketScheduled;
use App\Services\OutlookCalendarService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminCalendarController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with('locacion.padre', 'currentAssignment.technician')
            ->whereHas('currentAssignment', function ($query) {
                $query->where('technician_id', auth()->id());
            })
            ->orderByDesc('created_at')
            ->get();

        return view('admin.calendar.index', compact('tickets'));
    }

    public function events(): JsonResponse
    {
        $events = TicketSchedule::with('ticket.locacion.padre')
            ->where('technician_id', auth()->id())
            ->orderBy('start_at')
            ->get()
            ->map(function (TicketSchedule $schedule) {
                return $this->buildEvent($schedule);
            });

        return response()->json($events->values());
    }

    public function store(Request $request, OutlookCalendarService $outlook): JsonResponse
    {
        $data = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'modality' => 'required|in:remota,terreno',
        ]);

        $ticket = Ticket::with('currentAssignment')->findOrFail($data['ticket_id']);
        if (! $ticket->currentAssignment || $ticket->currentAssignment->technician_id !== auth()->id()) {
            return response()->json(['message' => 'No puedes agendar un ticket no asignado.'], 403);
        }

        $schedule = TicketSchedule::create([
            'ticket_id' => $ticket->id,
            'technician_id' => auth()->id(),
            'start_at' => $data['start_at'],
            'end_at' => $data['end_at'],
            'modality' => $data['modality'],
            'created_by' => auth()->id(),
        ]);

        $eventId = $this->createOutlookEvent($outlook, $ticket, $schedule);
        if ($eventId) {
            $schedule->update([
                'outlook_event_id' => $eventId,
                'outlook_status' => 'synced',
                'outlook_error' => null,
            ]);
        } else {
            $schedule->update([
                'outlook_status' => 'error',
                'outlook_error' => 'sync_failed',
            ]);
        }

        $this->sendScheduleNotification($ticket, $schedule);

        return response()->json($this->buildEvent($schedule), 201);
    }

    public function update(Request $request, TicketSchedule $schedule, OutlookCalendarService $outlook): JsonResponse
    {
        if ($schedule->technician_id !== auth()->id()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $data = $request->validate([
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'modality' => 'nullable|in:remota,terreno',
        ]);

        $schedule->update([
            'start_at' => $data['start_at'],
            'end_at' => $data['end_at'],
            'modality' => $data['modality'] ?? $schedule->modality,
        ]);

        $this->updateOutlookEvent($outlook, $schedule);

        $schedule->refresh();

        $this->sendScheduleNotification($schedule->ticket, $schedule, 'updated');

        return response()->json($this->buildEvent($schedule));
    }

    public function destroy(TicketSchedule $schedule, OutlookCalendarService $outlook): JsonResponse
    {
        if ($schedule->technician_id !== auth()->id()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $schedule->loadMissing('ticket', 'ticket.currentAssignment.technician', 'ticket.locacion.padre');

        $this->deleteOutlookEvent($outlook, $schedule);
        $schedule->delete();

        $this->sendScheduleNotification($schedule->ticket, $schedule, 'deleted');

        return response()->json(['status' => 'ok']);
    }

    protected function buildEvent(TicketSchedule $schedule): array
    {
        $ticket = $schedule->ticket;
        $location = $ticket?->locacion?->padre?->nombre
            ? $ticket->locacion->padre->nombre . ' - ' . $ticket->locacion->nombre
            : ($ticket?->locacion?->nombre ?? 'Sin ubicación');
        $synced = $schedule->outlook_status === 'synced';
        $className = $synced ? 'event-synced' : 'event-error';

        return [
            'id' => (string) $schedule->id,
            'title' => "Ticket #{$ticket->id} · {$ticket->categoria}",
            'start' => $schedule->start_at->toIso8601String(),
            'end' => $schedule->end_at->toIso8601String(),
            'classNames' => [$className],
            'extendedProps' => [
                'ticket_id' => $ticket->id,
                'location' => $location,
                'user' => $ticket->usuario_mail,
                'modality' => $schedule->modality,
            ],
        ];
    }

    protected function createOutlookEvent(OutlookCalendarService $outlook, Ticket $ticket, TicketSchedule $schedule): ?string
    {
        $technician = $ticket->currentAssignment?->technician;
        if (! $technician) {
            return null;
        }

        $modalityLabel = $schedule->modality === 'terreno' ? 'Visita en terreno' : 'Atención remota';

        $description = nl2br(e($ticket->descripcion ?? ''));
        $payload = [
            'subject' => "Ticket #{$ticket->id} · {$ticket->categoria}",
            'body' => [
                'contentType' => 'HTML',
                'content' => "<strong>Ticket #{$ticket->id}</strong><br>{$description}<br><br>Modalidad: {$modalityLabel}",
            ],
            'start' => [
                'dateTime' => $schedule->start_at->toIso8601String(),
                'timeZone' => config('services.microsoft_graph.timezone', 'UTC'),
            ],
            'end' => [
                'dateTime' => $schedule->end_at->toIso8601String(),
                'timeZone' => config('services.microsoft_graph.timezone', 'UTC'),
            ],
            'attendees' => [
                [
                    'emailAddress' => [
                        'address' => $technician->email,
                        'name' => $technician->name,
                    ],
                    'type' => 'required',
                ],
            ],
        ];

        $event = $outlook->createEvent($payload);
        if (! $event || empty($event['id'])) {
            Log::warning('No se pudo crear evento en Outlook', ['ticket_id' => $ticket->id]);
            return null;
        }

        return $event['id'];
    }

    protected function updateOutlookEvent(OutlookCalendarService $outlook, TicketSchedule $schedule): void
    {
        if (! $schedule->outlook_event_id) {
            $schedule->update([
                'outlook_status' => 'error',
                'outlook_error' => 'missing_event_id',
            ]);
            return;
        }

        $schedule->loadMissing('ticket', 'ticket.currentAssignment.technician');
        $modalityLabel = $schedule->modality === 'terreno' ? 'Visita en terreno' : 'Atención remota';

        $description = nl2br(e($schedule->ticket->descripcion ?? ''));
        $payload = [
            'start' => [
                'dateTime' => $schedule->start_at->toIso8601String(),
                'timeZone' => config('services.microsoft_graph.timezone', 'UTC'),
            ],
            'end' => [
                'dateTime' => $schedule->end_at->toIso8601String(),
                'timeZone' => config('services.microsoft_graph.timezone', 'UTC'),
            ],
            'body' => [
                'contentType' => 'HTML',
                'content' => "<strong>Ticket #{$schedule->ticket->id}</strong><br>{$description}<br><br>Modalidad: {$modalityLabel}",
            ],
        ];

        $updated = $outlook->updateEvent($schedule->outlook_event_id, $payload);
        if ($updated) {
            $schedule->update([
                'outlook_status' => 'synced',
                'outlook_error' => null,
            ]);
        } else {
            $schedule->update([
                'outlook_status' => 'error',
                'outlook_error' => 'sync_failed',
            ]);
        }
    }

    protected function deleteOutlookEvent(OutlookCalendarService $outlook, TicketSchedule $schedule): void
    {
        if (! $schedule->outlook_event_id) {
            return;
        }

        $outlook->deleteEvent($schedule->outlook_event_id);
    }

    protected function sendScheduleNotification(Ticket $ticket, TicketSchedule $schedule, string $mode = 'created'): void
    {
        try {
            Mail::to($ticket->usuario_mail)->send(new TicketScheduled($ticket, $schedule, $mode));
        } catch (\Throwable $exception) {
            Log::warning('No se pudo enviar correo de agenda', [
                'ticket_id' => $ticket->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
