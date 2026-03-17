<x-layouts.clean>
    <div class="w-full h-screen flex flex-col items-center px-4 bg-[#FAFAF7] overflow-hidden">

        {{-- Contenedor principal centrado verticalmente --}}
        <div class="w-full max-w-xl py-8 flex flex-col justify-center" style="height: calc(100vh - 2rem);">

            {{-- Header sobrio --}}
            <div class="mb-10 text-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Tickets TI" class="mx-auto h-12" style="width: 200px; height: auto;">
                <p class="text-sm text-gray-500 mt-0">
                    Siempre en tu Línea
                </p>
            </div>

            {{-- Chips de contexto --}}
            <div class="flex items-center justify-center gap-3 mb-6 flex-shrink-0">

                {{-- Usuario --}}
                <div x-data="{ open: false }" class="relative">

                    <button
                        @click="open = !open"
                        @click.outside="open = false"
                        class="flex items-center gap-2 px-3 py-1.5 rounded-full border border-gray-300 text-sm text-gray-700 bg-white hover:bg-gray-50 transition">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5.121 17.804A9 9 0 1118.88 17.8M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>

                        {{ auth()->user()->email }}

                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    {{-- Dropdown --}}
                    <div
                        x-show="open"
                        x-transition
                        class="absolute right-0 mt-2 w-44 bg-white border border-gray-200 rounded-xl shadow-sm z-50">
                        <a
                            href="{{ route('profile.edit') }}"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-xl">
                            Ver perfil
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                type="submit"
                                class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-xl">
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Estado GLPI oculto --}}

            </div>

            {{-- Card principal --}}
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 p-6 flex flex-col"
                style="height: 420px; min-height: 420px; max-height: 420px;">

                <div class="flex items-center justify-between mb-4 gap-3">
                    <h2 class="text-lg font-medium">
                        Últimos tickets
                    </h2>

                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-500">
                            Total: {{ $total }}
                        </span>

                        <a
                            href="{{ route('ticket.create') }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-[#6B8E23] text-sm font-medium text-[#6B8E23] bg-[#F4F7EE] hover:bg-[#E9F0DF] transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Nuevo ticket
                        </a>
                    </div>
                </div>

                {{-- Contenedor scrollable --}}
                <div class="flex-1 overflow-y-auto" style="max-height: calc(420px - 5rem);">

                    <div class="space-y-3 pr-2">
                        @forelse($ultimos as $ticket)
                        @php
                        $esPendiente = in_array($ticket->estado_glpi, [
                        'recibido',
                        'en_proceso',
                        'en_espera',
                        ]);

                        $establecimientoId = $ticket->locacion_id ?? 0;
                        $usuarioId = auth()->id() ?? 0;
                        $idCompuesto = sprintf('%03d%03d%03d', $establecimientoId, $usuarioId, $ticket->id);

                        $statusRaw = $ticket->estado_glpi ?: $ticket->latestStatusEvent?->to_status;
                        $statusLabel = $statusRaw
                            ? ucfirst(str_replace('_', ' ', $statusRaw))
                            : 'Sin estado';
                        @endphp

                        <div
                            class="ticket-card group border rounded-xl mb-2 p-4 cursor-pointer
                                {{ $esPendiente ? 'border-[#6B8E23] bg-[#F4F7EE]' : 'border-gray-200 bg-white' }}"
                            data-ticket-id="{{ $ticket->id }}"
                            data-ticket-title="Ticket #{{ $idCompuesto }}">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2">
                                    <div class="text-sm font-semibold text-gray-800">Ticket #{{ $idCompuesto }}</div>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full border border-gray-200 bg-white text-gray-600">
                                        {{ $statusLabel }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-400">
                                    {{ $ticket->created_at->format('d-m-Y H:i') }}
                                </div>
                            </div>

                            <div class="text-sm text-gray-700 mt-2 truncate" title="{{ $ticket->descripcion }}">
                                {{ $ticket->descripcion ?: 'Sin descripción' }}
                            </div>

                            <div class="text-xs text-gray-400 mt-2 opacity-0 group-hover:opacity-100 transition">
                                Clic para ver las acciones
                            </div>
                        </div>

                        <div id="ticket-history-{{ $ticket->id }}" class="hidden">
                            <div class="space-y-4">
                                <div>
                                    <div class="text-xs uppercase tracking-wide text-gray-500 mb-2">Asignaciones</div>
                                    @if ($ticket->assignments->count() > 0)
                                        <div class="space-y-2 text-sm text-gray-700">
                                            @foreach ($ticket->assignments->sortByDesc('assigned_at') as $assignment)
                                                <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
                                                    {{ $assignment->assigned_at?->format('d-m-Y H:i') ?? 'Sin fecha' }}
                                                    — {{ $assignment->technician?->name ?? 'Sin técnico' }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-500">Sin asignaciones registradas.</div>
                                    @endif
                                </div>

                                <div>
                                    <div class="text-xs uppercase tracking-wide text-gray-500 mb-2">Agendamiento</div>
                                    @if ($ticket->schedules->count() > 0)
                                        <div class="space-y-2 text-sm text-gray-700">
                                            @foreach ($ticket->schedules->sortByDesc('start_at') as $schedule)
                                                <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
                                                    {{ $schedule->start_at?->format('d-m-Y H:i') ?? 'Sin fecha' }}
                                                    — {{ $schedule->end_at?->format('d-m-Y H:i') ?? 'Sin fecha' }}
                                                    @if ($schedule->modality)
                                                        ({{ $schedule->modality }})
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-500">Sin agendamientos registrados.</div>
                                    @endif
                                </div>

                                <div>
                                    <div class="text-xs uppercase tracking-wide text-gray-500 mb-2">Acciones</div>
                                    @if ($ticket->actions->count() > 0)
                                        <div class="space-y-2 text-sm text-gray-700">
                                            @foreach ($ticket->actions as $action)
                                                <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
                                                    <div class="text-xs text-gray-500">
                                                        {{ $action->created_at?->format('d-m-Y H:i') ?? 'Sin fecha' }}
                                                    </div>
                                                    <div class="font-medium">
                                                        {{ ucfirst($action->action_type) }} — {{ ucfirst(str_replace('_', ' ', $action->status)) }}
                                                    </div>
                                                    <div class="text-gray-600">{{ $action->description }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-500">Sin acciones registradas.</div>
                                    @endif
                                </div>

                                <div>
                                    <div class="text-xs uppercase tracking-wide text-gray-500 mb-2">Resolución</div>
                                    @if ($ticket->resolution)
                                        <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700">
                                            <div class="text-xs text-gray-500 mb-1">
                                                {{ $ticket->resolution->resolved_at?->format('d-m-Y H:i') ?? 'Sin fecha' }}
                                            </div>
                                            <div>{{ $ticket->resolution->resolution_text }}</div>
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-500">Aún no se registra resolución.</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-gray-500 py-6">
                            No hay tickets registrados
                        </div>
                        @endforelse
                    </div>

                </div>
            </div>

            @if(auth()->user()->role === 'admin')
            {{-- Acciones administrativas --}}
            <div class="mt-6 pb-4 flex-shrink-0 flex justify-center">
                <div
                    x-data="{ open: false, timer: null }"
                    @mouseenter="clearTimeout(timer); open = true"
                    @mouseleave="timer = setTimeout(() => open = false, 250)"
                    class="relative w-full">
                    <button
                        type="button"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-white-50 transition">
                        Menú de Administrador
                    </button>

                    <div
                        x-show="open"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-2"
                        @mouseenter="clearTimeout(timer); open = true"
                        @mouseleave="timer = setTimeout(() => open = false, 250)"
                        class="absolute left-0 right-0 bottom-full mb-2">
                        <div class="bg-white border border-gray-200 rounded-xl shadow-lg p-2 space-y-2">
                            {{-- Opciones GLPI ocultas temporalmente --}}

                            <form action="{{ route('admin.profiles.index') }}" method="GET">
                                <button
                                    type="submit"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    Gestionar usuarios
                                </button>
                            </form>

                            <form action="{{ route('admin.locaciones.index') }}" method="GET">
                                <button
                                    type="submit"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    Gestionar locaciones
                                </button>
                            </form>

                            <form action="{{ route('admin.tickets.index') }}" method="GET">
                                <button
                                    type="submit"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    Gestionar tickets
                                </button>
                            </form>

                            <form action="{{ route('admin.calendar.index') }}" method="GET">
                                <button
                                    type="submit"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    Calendario
                                </button>
                            </form>

                            <form action="{{ route('admin.printers.index') }}" method="GET">
                                <button
                                    type="submit"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    Gestionar impresoras
                                </button>
                            </form>

                            <form action="{{ route('admin.logs.index') }}" method="GET">
                                <button
                                    type="submit"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    Log general
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div id="ticket-history-modal" class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50 hidden" aria-hidden="true">
                <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl p-6" role="dialog" aria-modal="true" aria-labelledby="ticket-history-title">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Historial</p>
                            <h3 id="ticket-history-title" class="text-lg font-semibold text-gray-800">Historial ticket</h3>
                        </div>
                        <button type="button" id="ticket-history-close" class="text-gray-500 hover:text-gray-700">✕</button>
                    </div>

                    <div id="ticket-history-content" class="max-h-[60vh] overflow-y-auto pr-2"></div>
                </div>
            </div>

            <script>
                (function () {
                    const cards = document.querySelectorAll('.ticket-card');
                    const modal = document.getElementById('ticket-history-modal');
                    const modalContent = document.getElementById('ticket-history-content');
                    const modalTitle = document.getElementById('ticket-history-title');
                    const modalClose = document.getElementById('ticket-history-close');

                    function openModal(title, content) {
                        if (!modal || !modalContent || !modalTitle) return;
                        modalTitle.textContent = title || 'Historial ticket';
                        modalContent.innerHTML = content || '<div class="text-sm text-gray-500">Sin historial disponible.</div>';
                        modal.classList.remove('hidden');
                        modal.setAttribute('aria-hidden', 'false');
                    }

                    function closeModal() {
                        if (!modal) return;
                        modal.classList.add('hidden');
                        modal.setAttribute('aria-hidden', 'true');
                    }

                    cards.forEach((card) => {
                        card.addEventListener('click', () => {
                            const ticketId = card.dataset.ticketId;
                            const title = card.dataset.ticketTitle || 'Historial ticket';
                            const contentEl = ticketId ? document.getElementById(`ticket-history-${ticketId}`) : null;
                            openModal(title, contentEl ? contentEl.innerHTML : null);
                        });
                    });

                    if (modalClose) {
                        modalClose.addEventListener('click', closeModal);
                    }
                    if (modal) {
                        modal.addEventListener('click', (event) => {
                            if (event.target === modal) {
                                closeModal();
                            }
                        });
                    }
                    window.addEventListener('keydown', (event) => {
                        if (event.key === 'Escape') {
                            closeModal();
                        }
                    });
                })();
            </script>
        </div>
    </div>

    <style>
        /* Estilos para centrado perfecto */
        .dashboard-container {
            height: 100vh;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Estilo para scrollbar */
        .flex-1::-webkit-scrollbar {
            width: 6px;
        }

        .flex-1::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .flex-1::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .flex-1::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
</x-layouts.clean>
