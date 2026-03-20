<x-layouts.clean>
    <div id="page-loader" class="fixed inset-0 z-50 flex items-center justify-center bg-[#FAFAF7] transition-opacity duration-200">
        <div class="h-10 w-10 rounded-full border-4 border-gray-200 border-t-gray-500 animate-spin"></div>
    </div>
    <div class="w-full h-screen flex flex-col items-center px-4 bg-[#FAFAF7] overflow-hidden">

        {{-- Contenedor principal centrado verticalmente --}}
        <div class="w-full max-w-7xl py-8 flex flex-col justify-center" style="height: calc(100vh - 2rem);">

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
                        $statusRaw = $ticket->latestStatusEvent?->to_status ?? 'nuevo';
                        $esPendiente = in_array($statusRaw, [
                            'nuevo',
                            'recibido',
                            'asignado',
                            'en_progreso',
                            'standby',
                        ], true);

                        $idCompuesto = $ticket->display_id;
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

                        @php
                            $attachmentsPayload = $ticket->attachments->map(function ($attachment) {
                                return [
                                    'url' => route('tickets.attachments.show', $attachment),
                                    'name' => $attachment->original_name,
                                    'mime' => $attachment->mime_type,
                                ];
                            })->values();
                        @endphp
                        <div id="ticket-attachments-{{ $ticket->id }}" class="hidden"
                            data-attachments='@json($attachmentsPayload)'></div>
                        @empty
                        <div class="text-center text-gray-500 py-6">
                            No hay tickets registrados
                        </div>
                        @endforelse
                    </div>

                </div>
            </div>

            <div id="ticket-history-modal" class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50 hidden" aria-hidden="true">
                <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl p-6 relative" role="dialog" aria-modal="true" aria-labelledby="ticket-history-title">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Historial</p>
                            <h3 id="ticket-history-title" class="text-lg font-semibold text-gray-800">Historial ticket</h3>
                        </div>
                        <button type="button" id="ticket-history-close" class="text-gray-500 hover:text-gray-700">✕</button>
                    </div>

                    <div id="ticket-history-content" class="max-h-[60vh] overflow-y-auto pr-2"></div>

                    <button type="button" id="ticket-attachments-open"
                        class="absolute bottom-4 right-4 rounded-full border border-gray-300 bg-white px-4 py-2 text-xs text-gray-700 shadow-sm hover:bg-gray-50 hidden">
                        Ver adjuntos
                    </button>
                </div>
            </div>

            <div id="ticket-attachments-viewer" class="fixed inset-0 bg-black/90 z-50 hidden" aria-hidden="true">
                <div class="absolute inset-0 flex items-center justify-center">
                    <button type="button" id="attachments-prev"
                        class="absolute left-4 text-white text-3xl font-light px-3 py-1 hover:text-gray-200">‹</button>
                    <div id="attachments-stage" class="max-w-[90vw] max-h-[85vh]"></div>
                    <button type="button" id="attachments-next"
                        class="absolute right-4 text-white text-3xl font-light px-3 py-1 hover:text-gray-200">›</button>
                </div>
                <div class="absolute top-4 right-4 flex items-center gap-2">
                    <div id="attachments-caption" class="text-xs text-gray-200"></div>
                    <button type="button" id="attachments-close"
                        class="text-white text-2xl leading-none hover:text-gray-200">✕</button>
                </div>
            </div>

            <script>
                (function () {
                    const cards = document.querySelectorAll('.ticket-card');
                    const modal = document.getElementById('ticket-history-modal');
                    const modalContent = document.getElementById('ticket-history-content');
                    const modalTitle = document.getElementById('ticket-history-title');
                    const modalClose = document.getElementById('ticket-history-close');
                    const attachmentsButton = document.getElementById('ticket-attachments-open');
                    const viewer = document.getElementById('ticket-attachments-viewer');
                    const viewerStage = document.getElementById('attachments-stage');
                    const viewerCaption = document.getElementById('attachments-caption');
                    const viewerPrev = document.getElementById('attachments-prev');
                    const viewerNext = document.getElementById('attachments-next');
                    const viewerClose = document.getElementById('attachments-close');
                    let currentAttachments = [];
                    let currentIndex = 0;

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
                            const attachmentsEl = ticketId ? document.getElementById(`ticket-attachments-${ticketId}`) : null;
                            currentAttachments = [];
                            if (attachmentsEl) {
                                try {
                                    currentAttachments = JSON.parse(attachmentsEl.dataset.attachments || '[]');
                                } catch {
                                    currentAttachments = [];
                                }
                            }
                            if (attachmentsButton) {
                                attachmentsButton.classList.toggle('hidden', currentAttachments.length === 0);
                            }
                            openModal(title, contentEl ? contentEl.innerHTML : null);
                        });
                    });

                    function renderAttachment() {
                        if (!viewerStage) return;
                        const item = currentAttachments[currentIndex];
                        if (!item) {
                            viewerStage.innerHTML = '';
                            return;
                        }
                        const isImage = item.mime && item.mime.startsWith('image/');
                        viewerStage.innerHTML = '';
                        if (isImage) {
                            const img = document.createElement('img');
                            img.src = item.url;
                            img.alt = item.name || 'Adjunto';
                            img.className = 'max-w-[90vw] max-h-[85vh] object-contain';
                            viewerStage.appendChild(img);
                        } else {
                            const frame = document.createElement('iframe');
                            frame.src = item.url;
                            frame.title = item.name || 'Adjunto';
                            frame.className = 'w-[90vw] h-[85vh] bg-white';
                            viewerStage.appendChild(frame);
                        }
                        if (viewerCaption) {
                            viewerCaption.textContent = `${currentIndex + 1} / ${currentAttachments.length} ${item.name ? '· ' + item.name : ''}`;
                        }
                    }

                    function openViewer() {
                        if (!viewer || currentAttachments.length === 0) return;
                        currentIndex = 0;
                        renderAttachment();
                        viewer.classList.remove('hidden');
                        viewer.setAttribute('aria-hidden', 'false');
                    }

                    function closeViewer() {
                        if (!viewer) return;
                        viewer.classList.add('hidden');
                        viewer.setAttribute('aria-hidden', 'true');
                    }

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
                    if (attachmentsButton) {
                        attachmentsButton.addEventListener('click', openViewer);
                    }
                    if (viewerPrev) {
                        viewerPrev.addEventListener('click', () => {
                            if (currentAttachments.length === 0) return;
                            currentIndex = (currentIndex - 1 + currentAttachments.length) % currentAttachments.length;
                            renderAttachment();
                        });
                    }
                    if (viewerNext) {
                        viewerNext.addEventListener('click', () => {
                            if (currentAttachments.length === 0) return;
                            currentIndex = (currentIndex + 1) % currentAttachments.length;
                            renderAttachment();
                        });
                    }
                    if (viewerClose) {
                        viewerClose.addEventListener('click', closeViewer);
                    }
                    if (viewer) {
                        viewer.addEventListener('click', (event) => {
                            const clickedInsideStage = viewerStage && viewerStage.contains(event.target);
                            const clickedPrev = viewerPrev && viewerPrev.contains(event.target);
                            const clickedNext = viewerNext && viewerNext.contains(event.target);
                            const clickedClose = viewerClose && viewerClose.contains(event.target);
                            if (!clickedInsideStage && !clickedPrev && !clickedNext && !clickedClose) {
                                closeViewer();
                            }
                        });
                    }
                    window.addEventListener('keydown', (event) => {
                        if (event.key === 'Escape') {
                            closeViewer();
                            closeModal();
                        }
                        if (event.key === 'ArrowRight' && !viewer?.classList.contains('hidden')) {
                            if (currentAttachments.length === 0) return;
                            currentIndex = (currentIndex + 1) % currentAttachments.length;
                            renderAttachment();
                        }
                        if (event.key === 'ArrowLeft' && !viewer?.classList.contains('hidden')) {
                            if (currentAttachments.length === 0) return;
                            currentIndex = (currentIndex - 1 + currentAttachments.length) % currentAttachments.length;
                            renderAttachment();
                        }
                    });
                })();
            </script>
        </div>
    </div>

    <script>
        window.addEventListener('load', () => {
            const loader = document.getElementById('page-loader');
            if (!loader) return;
            loader.classList.add('opacity-0');
            window.setTimeout(() => {
                loader.classList.add('hidden');
            }, 200);
        });
    </script>

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
