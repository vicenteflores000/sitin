<?php

namespace App\Services;

use App\Models\Ticket;

class PriorityService
{
    public function calculate(Ticket $ticket): array
    {
        $priority = $this->baseByImpact($ticket->impacto);

        $priority += $this->areaAdjustment($ticket);
        $priority += $this->categoryAdjustment($ticket);
        $priority += $this->typeAdjustment($ticket);

        // Normalizar entre 1 y 5
        $priority = max(1, min(5, $priority));

        return [
            'prioridad' => $priority,
            'urgencia' => $this->mapUrgency($priority),
        ];
    }

    protected function baseByImpact(string $impacto): int
    {
        return match ($impacto) {
            'Impide atender usuarios' => 5,
            'Dificulta el trabajo' => 3,
            default => 1,
        };
    }

    protected function areaAdjustment(Ticket $ticket): int
    {
        $areasCriticas = ['Urgencia', 'CESFAM', 'Registro Civil'];

        if (in_array($ticket->area, $areasCriticas) && $this->baseByImpact($ticket->impacto) >= 3) {
            return 1;
        }

        return 0;
    }

    protected function categoryAdjustment(Ticket $ticket): int
    {
        return in_array($ticket->categoria, ['Internet', 'Sistema']) ? 1 : 0;
    }

    protected function typeAdjustment(Ticket $ticket): int
    {
        return match ($ticket->tipo) {
            'Mejora' => -3,
            'Administrativo' => -1,
            default => 0,
        };
    }

    protected function mapUrgency(int $priority): string
    {
        return match ($priority) {
            5 => 'Crítica',
            4 => 'Alta',
            3 => 'Media',
            default => 'Baja',
        };
    }
}
