<?php

namespace App\Support;

use App\Models\Ticket;

class TicketView
{
    public static function locationLabel(Ticket $ticket, string $fallback = 'Sin ubicación'): string
    {
        $parent = $ticket->locacion?->nombre ?? $fallback;

        if ($ticket->locacion_hija_texto) {
            return $parent . ' - ' . $ticket->locacion_hija_texto;
        }

        return $parent;
    }

    public static function statusMeta(Ticket $ticket): array
    {
        $status = $ticket->latestStatusEvent?->to_status ?? 'nuevo';
        $isResolved = in_array($status, ['resuelto', 'cerrado'], true);
        $isStandby = in_array($status, ['standby', 'en_espera'], true);
        $label = self::statusLabel($status, 'raw');

        return [
            'key' => $status,
            'label' => $label,
            'is_resolved' => $isResolved,
            'is_standby' => $isStandby,
            'is_compact' => $isResolved || $isStandby,
            'text_class' => $isResolved ? 'text-gray-500' : ($isStandby ? 'text-orange-700' : 'text-gray-700'),
            'card_class' => $isResolved ? 'bg-gray-50 text-gray-500 border-gray-200' : ($isStandby ? 'bg-orange-50 text-orange-800 border-orange-200' : 'bg-gray-50 border-gray-200'),
            'title_class' => $isResolved ? 'text-gray-500' : ($isStandby ? 'text-orange-800' : 'text-gray-800'),
            'email_class' => $isStandby ? 'text-orange-600' : 'text-gray-500',
        ];
    }

    public static function statusLabel(?string $status, string $style = 'raw'): string
    {
        if (! $status) {
            return 'Sin estado';
        }

        $base = $status === 'standby' ? 'en espera' : $status;

        if ($style === 'code') {
            return $status;
        }

        if ($style === 'title') {
            return ucfirst(str_replace('_', ' ', $base));
        }

        return $base;
    }
}
