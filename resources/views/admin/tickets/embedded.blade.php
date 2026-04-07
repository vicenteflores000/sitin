<div id="admin-ticket-manager" data-auth-id="{{ auth()->id() }}" data-modal-url="{{ route('admin.tickets.modal', ['ticket' => 'TICKET_ID']) }}" class="bg-white rounded-xl shadow-xl border border-gray-200 p-6 flex flex-col h-full" x-data="{ showResolved: false, query: '' }">
    <div class="flex items-center justify-between mb-4 gap-3">
        <div class="flex items-center gap-2" x-data="{ openSearch: false }">
            <div
                class="admin-search-pill flex items-center rounded-full bg-[#F4F7EE] text-[#6B8E23] border border-[#6B8E23] transition-all duration-200 overflow-hidden h-8 px-2"
                :class="openSearch ? 'w-64 px-3' : 'w-8 px-2'"
                @click="openSearch = true; $nextTick(() => $refs.searchInput?.focus())">
                <svg class="w-4 h-4 text-[#6B8E23] shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                    x-ref="searchInput"
                    type="text"
                    x-model="query"
                    @focus="openSearch = true"
                    @blur="openSearch = !!query"
                    :class="openSearch ? 'ml-2 w-full opacity-100' : 'w-0 opacity-0'"
                    class="admin-search-input bg-transparent text-[11px] text-gray-700 border-0 outline-none ring-0 focus:ring-0 focus:border-transparent focus:outline-none focus-visible:outline-none shadow-none appearance-none transition-all duration-200" />
            </div>
        </div>
        <button type="button"
            class="text-xs text-gray-500 hover:text-gray-700 underline"
            @click="showResolved = !showResolved"
            x-text="showResolved ? 'ocultar resueltos' : 'ver resueltos'">
        </button>
    </div>

    <div class="flex-1 overflow-y-auto">
        <div class="space-y-4 pr-2">
            @forelse($tickets as $ticket)
            @php
                $status = $ticket->latestStatusEvent?->to_status ?? 'nuevo';
                $locacionPadre = $ticket->locacion?->nombre ?? 'Sin ubicación';
                $locacionLabel = $ticket->locacion_hija_texto
                    ? $locacionPadre . ' - ' . $ticket->locacion_hija_texto
                    : $locacionPadre;
            @endphp
            @php
                $assignedTechs = $ticket->currentAssignments ?? collect();
                $assignedIds = $assignedTechs->pluck('technician_id')->all();
                $assignedIdsAttr = implode(',', $assignedIds);
                $assignedNames = $assignedTechs->pluck('technician.name')->filter()->join(', ');
                $domainKeys = $ticket->domain_keys ?? [];
                $domainKeysAttr = implode(',', $domainKeys);
                $statusKey = $status;
                $isResolved = in_array($status, ['resuelto', 'cerrado'], true);
                $isStandby = in_array($status, ['standby', 'en_espera'], true);
                $isCompact = $isResolved || $isStandby;
                $statusLabel = $status === 'standby' ? 'en espera' : $status;
                $searchText = strtolower(trim(implode(' ', array_filter([
                    $ticket->display_id,
                    $ticket->usuario_mail,
                    $ticket->usuario,
                    $ticket->requester?->name,
                    $locacionLabel,
                    $ticket->descripcion,
                    $statusLabel,
                ]))));
            @endphp
            <div
                x-show="(showResolved || !['resuelto', 'cerrado'].includes($el.dataset.statusKey)) && (!query || ($el.dataset.search && $el.dataset.search.includes(query.toLowerCase())))"
                x-cloak
                class="group border rounded-lg cursor-pointer {{ $isCompact ? 'px-3 py-2 text-[11px]' : 'p-4' }} {{ $isResolved ? 'bg-gray-50 text-gray-500 border-gray-200' : ($isStandby ? 'bg-orange-50 text-orange-800 border-orange-200' : 'bg-gray-50 border-gray-200') }}"
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
                        @php
                            $requesterName = $ticket->usuario ?: ($ticket->requester?->name ?? 'Sin nombre');
                        @endphp
                        <div class="{{ $isResolved ? 'font-medium text-gray-500' : ($isStandby ? 'font-medium text-orange-800' : 'font-medium text-gray-800') }}">
                            #{{ $ticket->display_id }} · {{ $requesterName }}
                        </div>
                        @if($isCompact)
                            <div class="text-[11px] {{ $isStandby ? 'text-orange-600' : 'text-gray-500' }}">
                                {{ $ticket->usuario_mail }}
                            </div>
                        @else
                            <div class="text-sm text-gray-600">{{ $ticket->usuario_mail }}</div>
                            <div class="text-xs text-gray-400">Ubicación: {{ $locacionLabel }}</div>
                            <div class="mt-2 text-sm text-gray-700">{{ $ticket->descripcion }}</div>
                        @endif
                    </div>

                    <div class="text-xs text-gray-500 text-right">
                        <div>Estado: <span data-status-ticket="{{ $ticket->id }}" class="{{ $isResolved ? 'font-medium text-gray-500' : ($isStandby ? 'font-medium text-orange-700' : 'font-medium text-gray-700') }}">{{ $statusLabel }}</span></div>
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
            @empty
            <div class="text-center text-gray-500 py-6">No hay tickets registrados</div>
            @endforelse
        </div>
    </div>
</div>

<div id="admin-ticket-modal-root" class="fixed inset-0 z-50 hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" data-modal-backdrop></div>
    <div class="relative z-10 flex h-full w-full items-center justify-center p-4">
        <div id="admin-ticket-modal-body" class="w-full max-w-5xl"></div>
        <div id="admin-ticket-modal-loading" class="absolute flex items-center gap-2 rounded-full bg-white/90 px-4 py-2 text-xs text-gray-600 shadow-sm">
            <span class="inline-block h-3 w-3 animate-spin rounded-full border-2 border-gray-300 border-t-gray-500"></span>
            Cargando ticket...
        </div>
    </div>
</div>

<div id="admin-attachment-viewer" class="fixed inset-0 bg-black/90 z-50 hidden" aria-hidden="true">
    <div class="absolute inset-0 flex items-center justify-center">
        <div id="admin-attachment-stage" class="max-w-[90vw] max-h-[85vh]"></div>
    </div>
    <div class="absolute top-4 right-4 flex items-center gap-2">
        <div id="admin-attachment-caption" class="text-xs text-gray-200"></div>
        <button type="button" id="admin-attachment-close"
            class="text-white text-2xl leading-none hover:text-gray-200">✕</button>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const manager = document.getElementById('admin-ticket-manager');
            const modalRoot = document.getElementById('admin-ticket-modal-root');
            const modalBody = document.getElementById('admin-ticket-modal-body');
            const modalLoading = document.getElementById('admin-ticket-modal-loading');
            const modalBackdrop = modalRoot?.querySelector('[data-modal-backdrop]');
            const modalUrlTemplate = manager?.dataset.modalUrl;
            const modalCache = new Map();
            let activeTicketId = null;

            const viewer = document.getElementById('admin-attachment-viewer');
            const stage = document.getElementById('admin-attachment-stage');
            const caption = document.getElementById('admin-attachment-caption');
            const closeBtn = document.getElementById('admin-attachment-close');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const setModalLoading = (isLoading) => {
                if (!modalLoading) return;
                modalLoading.classList.toggle('hidden', !isLoading);
            };

            const openModalRoot = () => {
                if (!modalRoot) return;
                modalRoot.classList.remove('hidden');
                modalRoot.setAttribute('aria-hidden', 'false');
            };

            const closeModal = () => {
                if (!modalRoot || !modalBody) return;
                modalRoot.classList.add('hidden');
                modalRoot.setAttribute('aria-hidden', 'true');
                modalBody.innerHTML = '';
                activeTicketId = null;
            };

            const buildModalUrl = (ticketId) => {
                if (!modalUrlTemplate) return null;
                return modalUrlTemplate.replace('TICKET_ID', ticketId);
            };

            async function fetchModal(ticketId) {
                if (modalCache.has(ticketId)) {
                    return modalCache.get(ticketId);
                }
                const url = buildModalUrl(ticketId);
                if (!url) return null;
                const response = await fetch(url, { headers: { 'Accept': 'text/html' } });
                if (!response.ok) return null;
                const html = await response.text();
                modalCache.set(ticketId, html);
                return html;
            }

            async function openModal(ticketId, options = {}) {
                if (!modalRoot || !modalBody) return;
                activeTicketId = ticketId;
                openModalRoot();
                modalBody.innerHTML = '';
                setModalLoading(true);
                try {
                    const html = await fetchModal(ticketId);
                    if (!html) {
                        setModalLoading(false);
                        return;
                    }
                    modalBody.innerHTML = html;
                    if (window.Alpine && typeof window.Alpine.initTree === 'function') {
                        window.Alpine.initTree(modalBody);
                    }
                    if (options.tab) {
                        const alpineRoot = modalBody.querySelector('[x-data]');
                        if (alpineRoot?.__x?.$data) {
                            alpineRoot.__x.$data.tab = options.tab;
                        }
                        if (options.tab === 'chat') {
                            const container = modalBody.querySelector(`[data-chat-container][data-chat-ticket="${ticketId}"]`);
                            if (container) {
                                loadChat(container);
                            }
                        }
                    }
                } catch (_) {
                } finally {
                    setModalLoading(false);
                }
            }

            window.openAdminTicketModal = openModal;

            function openViewer({ url, name, mime }) {
                if (!viewer || !stage) return;
                const isImage = mime && mime.startsWith('image/');
                stage.innerHTML = '';
                if (isImage) {
                    const img = document.createElement('img');
                    img.src = url;
                    img.alt = name || 'Adjunto';
                    img.className = 'max-w-[90vw] max-h-[85vh] object-contain';
                    stage.appendChild(img);
                } else {
                    const frame = document.createElement('iframe');
                    frame.src = url;
                    frame.title = name || 'Adjunto';
                    frame.className = 'w-[90vw] h-[85vh] bg-white';
                    stage.appendChild(frame);
                }
                if (caption) {
                    caption.textContent = name || '';
                }
                viewer.classList.remove('hidden');
                viewer.setAttribute('aria-hidden', 'false');
            }

            function closeViewer() {
                if (!viewer) return;
                viewer.classList.add('hidden');
                viewer.setAttribute('aria-hidden', 'true');
                if (stage) {
                    stage.innerHTML = '';
                }
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', closeViewer);
            }
            if (viewer) {
                viewer.addEventListener('click', (event) => {
                    if (event.target === viewer) {
                        closeViewer();
                    }
                });
            }
            window.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeViewer();
                    closeModal();
                }
            });

            async function loadChat(container) {
                const url = container.dataset.chatFetchUrl;
                if (!url) return;
                try {
                    const response = await fetch(url, {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (!response.ok) return;
                    const payload = await response.json();
                    renderChatMessages(container, payload.messages || []);
                } catch (_) {}
            }

            function renderChatMessages(container, messages) {
                container.innerHTML = '';
                if (!messages.length) {
                    const empty = document.createElement('div');
                    empty.className = 'text-xs text-gray-400';
                    empty.textContent = 'Aún no hay mensajes.';
                    container.appendChild(empty);
                    return;
                }
                messages.forEach((msg) => {
                    const wrapper = document.createElement('div');
                    wrapper.className = msg.is_own ? 'flex justify-end' : 'flex justify-start';

                    const bubble = document.createElement('div');
                    bubble.className = msg.is_own
                        ? 'max-w-[80%] rounded-lg bg-[#F4F7EE] border border-[#6B8E23]/30 px-3 py-2'
                        : 'max-w-[80%] rounded-lg bg-gray-50 border border-gray-200 px-3 py-2';

                    const meta = document.createElement('div');
                    meta.className = 'text-[11px] text-gray-500 mb-1';
                    meta.textContent = `${msg.user_name} · ${msg.created_at}`;

                    const body = document.createElement('div');
                    body.className = 'whitespace-pre-wrap text-sm text-gray-700';
                    body.textContent = msg.body;

                    bubble.appendChild(meta);
                    bubble.appendChild(body);
                    wrapper.appendChild(bubble);
                    container.appendChild(wrapper);
                });
                container.scrollTop = container.scrollHeight;
            }

            document.addEventListener('click', (event) => {
                const card = event.target.closest('[data-ticket-card]');
                if (card) {
                    const ticketId = card.dataset.ticketId;
                    if (ticketId) {
                        openModal(ticketId);
                    }
                    return;
                }
                const closeTrigger = event.target.closest('[data-modal-close]');
                if (closeTrigger) {
                    closeModal();
                    return;
                }
                const thumb = event.target.closest('.admin-attachment-thumb');
                if (thumb) {
                    openViewer({
                        url: thumb.dataset.url,
                        name: thumb.dataset.name,
                        mime: thumb.dataset.mime,
                    });
                    return;
                }
                const refreshBtn = event.target.closest('[data-chat-refresh]');
                if (refreshBtn) {
                    const ticketId = refreshBtn.dataset.chatTicket;
                    const container = document.querySelector(`[data-chat-container][data-chat-ticket="${ticketId}"]`);
                    if (container) {
                        loadChat(container);
                    }
                    return;
                }
                const tabBtn = event.target.closest('[data-chat-tab]');
                if (tabBtn) {
                    const ticketId = tabBtn.dataset.chatTicket;
                    const container = document.querySelector(`[data-chat-container][data-chat-ticket="${ticketId}"]`);
                    if (container) {
                        loadChat(container);
                    }
                }
            });

            document.addEventListener('submit', async (event) => {
                const form = event.target.closest('[data-chat-form]');
                if (!form) return;
                event.preventDefault();
                const textarea = form.querySelector('textarea[name="message"]');
                const message = textarea?.value?.trim();
                if (!message) return;
                const url = form.dataset.chatSendUrl;
                if (!url) return;

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                        },
                        body: JSON.stringify({ message }),
                    });
                    if (!response.ok) return;
                    textarea.value = '';
                    const ticketId = form.dataset.chatTicket;
                    const container = document.querySelector(`[data-chat-container][data-chat-ticket="${ticketId}"]`);
                    if (container) {
                        loadChat(container);
                    }
                } catch (_) {}
            });

            document.addEventListener('keydown', (event) => {
                if (event.key !== 'Enter' && event.key !== ' ') return;
                const card = event.target.closest('[data-ticket-card]');
                if (!card) return;
                event.preventDefault();
                const ticketId = card.dataset.ticketId;
                if (ticketId) {
                    openModal(ticketId);
                }
            });

            function openChatFromUrl() {
                const params = new URLSearchParams(window.location.search);
                const ticketId = params.get('ticket');
                const tab = params.get('tab');
                if (!ticketId || tab !== 'chat') return;

                const card = document.querySelector(`[data-ticket-id="${ticketId}"]`);
                if (!card) return;
                openModal(ticketId, { tab: 'chat' });
            }

            document.addEventListener('alpine:initialized', () => {
                setTimeout(openChatFromUrl, 50);
            });
            setTimeout(openChatFromUrl, 300);

            if (modalRoot) {
                modalRoot.addEventListener('click', (event) => {
                    if (event.target === modalRoot || event.target === modalBackdrop) {
                        closeModal();
                    }
                });
            }
        });
    </script>
@endpush
