<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketSchedule;
use App\Models\TicketStatusEvent;
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
        $tickets = Ticket::with('locacion.padre', 'currentAssignment.technician', 'currentAssignments.technician', 'requester')
            ->whereHas('currentAssignments', function ($query) {
                $query->where('technician_id', auth()->id());
            })
            ->orderByDesc('created_at')
            ->get();

        return view('admin.calendar.index', compact('tickets'));
    }

    public function events(Request $request): JsonResponse
    {
        $query = TicketSchedule::with('ticket.locacion.padre', 'ticket.requester')
            ->orderBy('start_at');

        $scope = $request->query('scope');
        if ($scope !== 'all') {
            $query->where('technician_id', auth()->id());
        }

        $events = $query->get()->map(function (TicketSchedule $schedule) {
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

        $ticket = Ticket::with('currentAssignments')->findOrFail($data['ticket_id']);
        if (! $ticket->currentAssignments->contains('technician_id', auth()->id())) {
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

        $this->ensureAgendadoStatus($ticket);

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

        $this->ensureAgendadoStatus($schedule->ticket);

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

        $this->maybeRevertStatusAfterScheduleRemoval($schedule->ticket);

        $this->sendScheduleNotification($schedule->ticket, $schedule, 'deleted');

        return response()->json(['status' => 'ok']);
    }

    protected function buildEvent(TicketSchedule $schedule): array
    {
        $ticket = $schedule->ticket;
        $location = $ticket?->locacion?->nombre ?? 'Sin ubicación';
        if ($ticket?->locacion_hija_texto) {
            $location .= ' - ' . $ticket->locacion_hija_texto;
        }
        $synced = $schedule->outlook_status === 'synced';
        $className = $synced ? 'event-synced' : 'event-error';
        $domainKeys = $this->resolveDomainKeys($ticket);
        $canEdit = $schedule->technician_id === auth()->id();
        $classNames = [$className];
        if (! $canEdit) {
            $classNames[] = 'event-readonly';
        }
        $statusKey = $ticket?->latestStatusEvent?->to_status ?? 'nuevo';

        return [
            'id' => (string) $schedule->id,
            'title' => "Ticket #{$ticket->display_id} · {$ticket->categoria}",
            'start' => $schedule->start_at->toIso8601String(),
            'end' => $schedule->end_at->toIso8601String(),
            'classNames' => $classNames,
            'editable' => $canEdit,
            'extendedProps' => [
                'ticket_id' => $ticket->id,
                'location' => $location,
                'user' => $ticket->usuario_mail,
                'modality' => $schedule->modality,
                'domain_keys' => $domainKeys,
                'can_edit' => $canEdit,
                'technician_id' => $schedule->technician_id,
                'status_key' => $statusKey,
            ],
        ];
    }

    protected function resolveDomainKeys(?Ticket $ticket): array
    {
        if (! $ticket) {
            return [];
        }

        $domainMap = [
            'salud.mdonihue.cl' => 'salud',
            'edudonihue.cl' => 'educacion',
            'mdonihue.cl' => 'municipal',
        ];

        $email = $ticket->usuario_mail ?? '';
        if (!str_contains($email, '@')) {
            return [];
        }

        $domain = strtolower(substr(strrchr($email, '@'), 1) ?: '');
        $domain = str_replace(['@', ' '], '', $domain);
        $domain = iconv('UTF-8', 'ASCII//TRANSLIT', $domain) ?: $domain;
        $key = $domainMap[$domain] ?? null;

        return $key ? [$key] : [];
    }

    protected function createOutlookEvent(OutlookCalendarService $outlook, Ticket $ticket, TicketSchedule $schedule): ?string
    {
        $schedule->loadMissing('technician');
        $technician = $schedule->technician ?? $ticket->currentAssignment?->technician;
        if (! $technician) {
            return null;
        }

        $modalityLabel = $schedule->modality === 'terreno' ? 'Visita en terreno' : 'Atención remota';

        $description = nl2br(e($ticket->descripcion ?? ''));
        $payload = [
            'subject' => "Ticket #{$ticket->display_id} · {$ticket->categoria}",
            'body' => [
                'contentType' => 'HTML',
                'content' => "<strong>Ticket #{$ticket->display_id}</strong><br>{$description}<br><br>Modalidad: {$modalityLabel}",
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
                'content' => "<strong>Ticket #{$schedule->ticket->display_id}</strong><br>{$description}<br><br>Modalidad: {$modalityLabel}",
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

    protected function ensureAgendadoStatus(Ticket $ticket): void
    {
        $ticket->loadMissing('latestStatusEvent');
        $currentStatus = $ticket->latestStatusEvent?->to_status;

        if (in_array($currentStatus, ['resuelto', 'cerrado'], true)) {
            return;
        }

        if ($currentStatus === 'agendado' || $currentStatus === 'standby') {
            return;
        }

        $this->changeStatus($ticket, 'agendado', null);
    }

    protected function maybeRevertStatusAfterScheduleRemoval(Ticket $ticket): void
    {
        $ticket->loadMissing('latestStatusEvent', 'currentAssignments');
        $currentStatus = $ticket->latestStatusEvent?->to_status;

        if ($currentStatus !== 'agendado') {
            return;
        }

        $hasSchedules = TicketSchedule::where('ticket_id', $ticket->id)->exists();
        if ($hasSchedules) {
            return;
        }

        $nextStatus = $ticket->currentAssignments()->exists() ? 'asignado' : 'nuevo';
        $this->changeStatus($ticket, $nextStatus, null);
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
}
