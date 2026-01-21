<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Actions\SyncTicketStatusFromGlpi;
use Illuminate\Http\RedirectResponse;
use App\Actions\ReenviarTicketsPendientesAGlpi;

class DashboardController extends Controller
{
    public function index()
    {
        $userMail = auth()->user()->email;

        $tickets = Ticket::where('usuario_mail', $userMail)->get();

        $total = $tickets->count();

        $abiertos = $tickets->whereIn('estado_glpi', [
            'recibido',
            'en_proceso',
            'en_espera',
        ])->count();

        $cerrados = $tickets->where('estado_glpi', 'cerrado')->count();

        $ultimos = $tickets
            ->sortByDesc('created_at');

            $glpiStatus = 'online';

        return view('dashboard', compact(
            'total',
            'abiertos',
            'cerrados',
            'ultimos',
            'glpiStatus'
        ));
    }

    public function reenviarPendientes(
        ReenviarTicketsPendientesAGlpi $action
    ) {
        $result = $action->execute();

        return redirect()
            ->route('dashboard')
            ->with('reenviar_result', $result);
    }

    public function syncEstados(
        SyncTicketStatusFromGlpi $sync
    ): RedirectResponse {
        $result = $sync->execute();

        return redirect()
            ->route('dashboard')
            ->with('sync_result', $result);
    }
}
