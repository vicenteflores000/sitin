<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Services\IaSuggestionService;
use App\Services\PriorityService;
use App\Services\GlpiService;
use App\Models\Ticket;
use App\Models\GlpiLocation;

class TicketController extends Controller
{
    public function create()
    {
        $locations = GlpiLocation::orderBy('name')->get();

        return view('ticket.create', compact('locations'));
    }

    public function store(StoreTicketRequest $request)
    {
        $user = auth()->user();

        if (! $user->glpi_user_id) {
            abort(403, 'Usuario no vinculado con GLPI');
        }

        $ticket = Ticket::create([
            'tipo' => $request->tipo,
            'area' => $request->area,
            'categoria' => $request->categoria,
            'impacto' => $request->impacto,
            'descripcion' => trim($request->descripcion),
            'usuario_mail' => auth()->user()->email,
            'glpi_location_id' => $request->glpi_location_id,
            'estado_glpi' => 'Enviado',

            // datos ocultos
            'pc' => gethostname(),
            'usuario' => $_SERVER['USERNAME'] ?? null,
            'ip_origen' => $request->ip(),
            'origen' => 'Formulario TI',
            'estado_envio_glpi' => 'pendiente',
        ]);
        $priorityData = app(PriorityService::class)->calculate($ticket);
        $ticket->update($priorityData);
        $iaData = app(IaSuggestionService::class)->analyze($ticket->descripcion);
        $ticket->update($iaData);
        $glpi = app(GlpiService::class)->createTicket($ticket);


        return view('ticket.confirmacion', [
            'ticket_id' => $ticket->id
        ]);
    }
}
