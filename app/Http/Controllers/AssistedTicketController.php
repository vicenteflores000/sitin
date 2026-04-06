<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssistedTicketRequest;
use App\Models\Locacion;
use App\Models\AllowedDomain;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketStatusEvent;
use App\Mail\TicketCreated;
use App\Services\IaSuggestionService;
use App\Services\PriorityService;
use App\Services\GraphDirectoryService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AssistedTicketController extends Controller
{
    public function create()
    {
        $locaciones = Locacion::whereNull('locacion_padre_id')
            ->orderBy('nombre')
            ->get();

        return view('ticket.create', [
            'locaciones' => $locaciones,
            'assisted' => true,
            'formAction' => route('admin.tickets.assisted.store'),
        ]);
    }

    public function store(StoreAssistedTicketRequest $request)
    {
        $admin = auth()->user();

        $ticket = Ticket::create([
            'tipo' => $request->tipo,
            'area' => $request->input('area') ?: 'No especificado',
            'categoria' => $request->input('categoria') ?: 'Otro',
            'impacto' => $request->input('impacto') ?: 'No especificado',
            'descripcion' => trim($request->descripcion),
            'usuario' => trim($request->assisted_user_name),
            'usuario_mail' => strtolower(trim($request->assisted_user_email)),
            'locacion_id' => $request->locacion_id,
            'locacion_hija_texto' => trim($request->input('locacion_hija_texto') ?? ''),
            'pc' => gethostname(),
            'ip_origen' => $request->ip(),
            'origen' => 'Ticket asistido',
            'assisted_by' => $admin?->id,
            'assisted_channel' => $request->assisted_channel,
            'assisted_reason' => trim($request->assisted_reason),
        ]);

        $attachments = $request->file('attachments', []);
        foreach ($attachments as $file) {
            try {
                $path = $file->store("tickets/{$ticket->id}", 'local');
                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType() ?? $file->getClientMimeType(),
                    'size' => $file->getSize() ?? 0,
                ]);
            } catch (\Throwable $exception) {
                Log::warning('No se pudo guardar adjunto de ticket asistido', [
                    'ticket_id' => $ticket->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        TicketStatusEvent::create([
            'ticket_id' => $ticket->id,
            'from_status' => null,
            'to_status' => 'nuevo',
            'started_at' => now(),
            'changed_by' => $admin?->id,
        ]);

        $ticket->load('locacion.padre', 'assistedBy');

        $priorityData = app(PriorityService::class)->calculate($ticket);
        $ticket->update($priorityData);
        $iaData = app(IaSuggestionService::class)->analyze($ticket->descripcion);
        $ticket->update($iaData);

        try {
            Mail::to('informatica@mdonihue.cl')
                ->cc($ticket->usuario_mail)
                ->send(new TicketCreated($ticket));
        } catch (\Throwable $exception) {
            Log::warning('No se pudo enviar correo de ticket asistido', [
                'ticket_id' => $ticket->id,
                'error' => $exception->getMessage(),
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'ok',
                'ticket_id' => $ticket->id,
            ]);
        }

        return redirect()
            ->route('admin.dashboard')
            ->with('success', "Ticket asistido enviado correctamente con el ID: {$ticket->display_id}");
    }

    public function users(Request $request, GraphDirectoryService $graph): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));
        $limit = (int) $request->query('limit', 50);
        $limit = max(10, min($limit, 200));

        $allowedDomains = AllowedDomain::pluck('domain')
            ->map(fn ($domain) => strtolower(trim($domain)))
            ->filter()
            ->values()
            ->all();

        $users = $graph->searchUsers($query, $limit * 2);
        $results = [];

        foreach ($users as $user) {
            $email = $user['mail'] ?? $user['userPrincipalName'] ?? null;
            $name = trim((string) ($user['displayName'] ?? ''));
            if (! $email || $name === '') {
                continue;
            }

            $domain = strtolower(trim(substr(strrchr($email, '@'), 1) ?: ''));
            if ($domain === '' || (! empty($allowedDomains) && ! in_array($domain, $allowedDomains, true))) {
                continue;
            }

            $results[] = [
                'id' => (string) ($user['id'] ?? $email),
                'text' => $name . ' · ' . $email,
                'name' => $name,
                'email' => $email,
            ];

            if (count($results) >= $limit) {
                break;
            }
        }

        return response()->json($results);
    }
}
