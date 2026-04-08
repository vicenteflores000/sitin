@php
    $locacionLabel = \App\Support\TicketView::locationLabel($ticket);
    $assignedTechs = $ticket->currentAssignments ?? collect();
    $assignedIds = $assignedTechs->pluck('technician_id')->all();
    $assignedIdsAttr = implode(',', $assignedIds);
    $assignedNames = $assignedTechs->pluck('technician.name')->filter()->join(', ');
    $domainKeys = $ticket->domain_keys ?? [];
    $domainKeysAttr = implode(',', $domainKeys);
    $statusMeta = \App\Support\TicketView::statusMeta($ticket);
    $statusKey = $statusMeta['key'];
    $isCompact = $statusMeta['is_compact'];
    $requesterName = $ticket->usuario ?: ($ticket->requester?->name ?? 'Sin nombre');
    $searchText = strtolower(trim(implode(' ', array_filter([
        $ticket->display_id,
        $ticket->usuario_mail,
        $ticket->usuario,
        $ticket->requester?->name,
        $locacionLabel,
        $ticket->descripcion,
        $statusMeta['label'],
    ]))));
@endphp

<div
    x-show="(showResolved || !['resuelto', 'cerrado'].includes($el.dataset.statusKey)) && (!query || ($el.dataset.search && $el.dataset.search.includes(query.toLowerCase())))"
    x-cloak
    class="group border rounded-lg cursor-pointer {{ $isCompact ? 'px-3 py-2 text-[11px]' : 'p-4' }} {{ $statusMeta['card_class'] }}"
    data-ticket-card
    data-ticket-id="{{ $ticket->id }}"
    data-domain-keys="{{ $domainKeysAttr }}"
    data-technician-ids="{{ $assignedIdsAttr }}"
    data-status-key="{{ $statusKey }}"
    data-search="{{ $searchText }}"
    onclick="window.openAdminTicketModal && window.openAdminTicketModal('{{ $ticket->id }}')"
    role="button"
    tabindex="0">
    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="font-medium {{ $statusMeta['title_class'] }}">
                #{{ $ticket->display_id }} · {{ $requesterName }}
            </div>
            @if($isCompact)
                <div class="text-[11px] {{ $statusMeta['email_class'] }}">
                    {{ $ticket->usuario_mail }}
                </div>
            @else
                <div class="text-sm text-gray-600">{{ $ticket->usuario_mail }}</div>
                <div class="text-xs text-gray-400">Ubicación: {{ $locacionLabel }}</div>
                <div class="mt-2 text-sm text-gray-700">{{ $ticket->descripcion }}</div>
            @endif
        </div>

        <div class="text-xs text-gray-500 text-right">
            <div>Estado: <span data-status-ticket="{{ $ticket->id }}" class="font-medium {{ $statusMeta['text_class'] }}">{{ $statusMeta['label'] }}</span></div>
            @if(!$isCompact)
                <div>Asignado: <span data-assigned-ticket="{{ $ticket->id }}">{{ $assignedNames ?: '—' }}</span></div>
                <div>{{ $ticket->created_at->format('d-m-Y H:i') }}</div>
            @endif
        </div>
    </div>

    <div class="{{ $isCompact ? 'mt-1' : 'mt-3' }} text-[10px] text-gray-400 opacity-0 group-hover:opacity-100 transition">
        Clic para ver las acciones
    </div>
</div>
