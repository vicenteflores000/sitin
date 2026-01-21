<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Actions\ReenviarTicketsPendientesAGlpi;

class ReenviarTicketsGlpi extends Command
{
    protected $signature = 'glpi:reenviar';

    protected $description = 'Reenvía a GLPI los tickets pendientes o con error';

    public function handle(
        ReenviarTicketsPendientesAGlpi $action
    ): int {
        $this->info('Reenviando tickets pendientes a GLPI...');

        $result = $action->execute();

        $this->info("✔ Enviados: {$result['enviados']}");
        $this->info("✖ Fallidos: {$result['fallidos']}");

        return 0;
    }
}
