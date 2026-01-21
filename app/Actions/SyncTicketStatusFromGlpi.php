<?php

namespace App\Actions;

use App\Models\Ticket;
use App\Services\GlpiService;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncTicketStatusFromGlpi
{
    public function __construct(
        protected GlpiService $glpi
    ) {}

    public function execute(): array
    {
        $updated = 0;
        $failed = 0;

        $tickets = Ticket::whereNotNull('glpi_ticket_id')->get();

        foreach ($tickets as $ticket) {
            try {
                $glpiTicket = $this->glpi->getTicket($ticket->glpi_ticket_id);

                if (! $glpiTicket || ! isset($glpiTicket['status'])) {
                    $failed++;
                    continue;
                }

                $estado = $this->glpi->mapStatus((int) $glpiTicket['status']);

                $ticket->update([
                    'estado_glpi' => $estado,
                    'updated_at_estado_glpi' => now(),
                ]);

                $updated++;
            } catch (Throwable $e) {
                Log::warning('Error sincronizando ticket GLPI', [
                    'ticket_id' => $ticket->id,
                    'error' => $e->getMessage(),
                ]);

                $failed++;
            }
        }

        return compact('updated', 'failed');
    }
}
