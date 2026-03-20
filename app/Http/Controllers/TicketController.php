<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Services\IaSuggestionService;
use App\Services\PriorityService;
use App\Models\Ticket;
use App\Models\Locacion;
use App\Models\TicketAttachment;
use App\Models\TicketStatusEvent;
use App\Mail\TicketCreated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TicketController extends Controller
{
    public function create()
    {
        $locacionesQuery = Locacion::whereNull('locacion_padre_id')
            ->orderBy('nombre');

        if (auth()->check()) {
            $email = auth()->user()?->email;
            $domain = strtolower(trim(substr(strrchr($email ?? '', '@'), 1) ?: ''));
            if ($domain !== '') {
                $locacionesQuery->whereHas('allowedDomains', function ($domainQuery) use ($domain) {
                    $domainQuery->where('domain', $domain);
                });
            }
        }

        $locaciones = $locacionesQuery->get();

        return view('ticket.create', compact('locaciones'));
    }

    public function store(StoreTicketRequest $request)
    {
        if (!auth()->check()) {
            return $this->authErrorResponse($request, 'Debes iniciar sesión con Outlook.', 401);
        }

        $user = auth()->user();
        if ($user && ! $user->isActive()) {
            auth()->logout();
            return $this->authErrorResponse($request, 'Usuario desactivado.', 403);
        }
        if ($user?->must_change_password) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Debes cambiar tu clave provisoria antes de enviar un ticket.',
                    'redirect' => route('password.force.show'),
                ], 423);
            }

            $request->session()->put('url.intended', $request->fullUrl());

            return redirect()->route('password.force.show');
        }

        $ticket = Ticket::create([
            'tipo' => $request->tipo,
            'area' => $request->input('area') ?: 'No especificado',
            'categoria' => $request->input('categoria') ?: 'Otro',
            'impacto' => $request->input('impacto') ?: 'No especificado',
            'descripcion' => trim($request->descripcion),
            'usuario_mail' => $user->email,
            'locacion_id' => $request->locacion_id,
            'locacion_hija_texto' => trim($request->input('locacion_hija_texto') ?? ''),

            // datos ocultos
            'pc' => gethostname(),
            'usuario' => $_SERVER['USERNAME'] ?? null,
            'ip_origen' => $request->ip(),
            'origen' => 'Formulario TI',
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
                Log::warning('No se pudo guardar adjunto de ticket', [
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
            'changed_by' => $user->id,
        ]);
        $ticket->load('locacion.padre');

        $priorityData = app(PriorityService::class)->calculate($ticket);
        $ticket->update($priorityData);
        $iaData = app(IaSuggestionService::class)->analyze($ticket->descripcion);
        $ticket->update($iaData);
        try {
            Mail::to('informatica@mdonihue.cl')
                ->cc($user->email)
                ->send(new TicketCreated($ticket));
        } catch (\Throwable $exception) {
            Log::warning('No se pudo enviar correo de ticket', [
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
            ->route('ticket.create')
            ->with('success', "Ticket enviado correctamente con el ID: {$ticket->display_id}");
    }

    protected function authErrorResponse(StoreTicketRequest $request, string $message, int $status = 422, array $meta = [])
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                ...$meta,
            ], $status);
        }

        return back()
            ->withErrors(['auth_email' => $message])
            ->withInput($request->except('auth_password'));
    }
}
