<?php

namespace App\Actions;

use App\Models\Ticket;
use App\Services\GlpiService;
use Illuminate\Support\Facades\Log;
use Throwable;

class ReenviarTicketsPendientesAGlpi
{
    public function __construct(
        protected GlpiService $glpi
    ) {}

    public function execute(): array
    {
        $enviados = 0;
        $fallidos = 0;

        $tickets = Ticket::whereIn('estado_envio_glpi', [
            'pendiente',
            'error',
        ])->get();

        foreach ($tickets as $ticket) {
            try {
                if (! $ticket->usuario_mail) {
                    throw new \RuntimeException('Ticket sin usuario_mail');
                }

                $resultado = $this->glpi->createTicket(
                    $ticket
                );

                if ($resultado['status'] === 'enviado') {
                    $enviados++;
                } else {
                    $fallidos++;
                }
            } catch (Throwable $e) {
                Log::warning('Error reenviando ticket a GLPI', [
                    'ticket_id' => $ticket->id,
                    'error' => $e->getMessage(),
                ]);

                $fallidos++;
            }
        }

        return compact('enviados', 'fallidos');
    }
}
