<x-layouts.clean>
    <div id="admin-dashboard">
    <style>
        html.dark-mode #admin-dashboard .admin-page {
            background: radial-gradient(circle at top, #111a2b 0%, #0b1220 55%, #0b1220 100%);
        }
        html.dark-mode #admin-dashboard .admin-loader {
            background-color: #0b1220;
        }
        html.dark-mode #admin-dashboard .bg-white {
            background-color: #0f172a !important;
        }
        html.dark-mode #admin-dashboard .bg-gray-50 {
            background-color: #0b1220 !important;
        }
        html.dark-mode #admin-dashboard .border-gray-200,
        html.dark-mode #admin-dashboard .border-gray-300 {
            border-color: #1f2a44 !important;
        }
        html.dark-mode #admin-dashboard .text-gray-700 {
            color: #e5e7eb !important;
        }
        html.dark-mode #admin-dashboard .text-gray-800,
        html.dark-mode #admin-dashboard .text-gray-900 {
            color: #f8fafc !important;
        }
        html.dark-mode #admin-dashboard h1,
        html.dark-mode #admin-dashboard h2,
        html.dark-mode #admin-dashboard h3,
        html.dark-mode #admin-dashboard h4 {
            color: #f8fafc;
        }
        html.dark-mode #admin-dashboard .text-gray-600 {
            color: #cbd5f5 !important;
        }
        html.dark-mode #admin-dashboard .text-gray-500 {
            color: #9aa6bf !important;
        }
        html.dark-mode #admin-dashboard .text-gray-400 {
            color: #7b879d !important;
        }
        html.dark-mode #admin-dashboard .shadow-xl,
        html.dark-mode #admin-dashboard .shadow-lg,
        html.dark-mode #admin-dashboard .shadow-sm {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.45);
        }
        html.dark-mode #admin-dashboard input,
        html.dark-mode #admin-dashboard select,
        html.dark-mode #admin-dashboard textarea {
            background-color: #0b1220 !important;
            color: #e5e7eb !important;
            border-color: #1f2a44 !important;
        }
        html.dark-mode #admin-dashboard .hover\:bg-gray-50:hover {
            background-color: #1f2a44 !important;
        }
        html.dark-mode #admin-dashboard .admin-toggle {
            background-color: #0b1220;
            border-color: #1f2a44;
            color: #e5e7eb;
        }
        html.dark-mode #admin-dashboard .admin-toggle:hover {
            background-color: #1f2a44;
        }
        html.dark-mode #admin-dashboard .admin-cta {
            background-color: #1b2a10;
            border-color: #7aa23a;
            color: #d6f5a3;
        }
        html.dark-mode #admin-dashboard .admin-cta:hover {
            background-color: #223614;
        }
        html.dark-mode #admin-dashboard .admin-modal {
            color: #e5e7eb;
            border-color: #1f2a44;
            background-color: #0f172a;
        }
        html.dark-mode #admin-dashboard .admin-modal-title {
            color: #f8fafc;
        }
        html.dark-mode #admin-dashboard .admin-tech-pill {
            background-color: #111827 !important;
            color: #e5e7eb !important;
            border: 1px solid #1f2a44;
        }
        html.dark-mode #admin-dashboard .admin-edit-tech {
            color: #cbd5f5 !important;
        }
        html.dark-mode #admin-dashboard .admin-edit-tech:hover {
            color: #f8fafc !important;
        }
        html.dark-mode #admin-dashboard .admin-warning {
            background-color: #2a1f0f !important;
            border-color: #6b4b1e !important;
            color: #f5d39a !important;
        }
        html.dark-mode #admin-dashboard .admin-search-pill {
            background-color: #0b1220 !important;
            border-color: #7aa23a !important;
            color: #d6f5a3 !important;
        }
        html.dark-mode #admin-dashboard .admin-search-input {
            color: #e5e7eb !important;
        }
        html.dark-mode #admin-dashboard .bg-orange-50 {
            background-color: #3a2a14 !important;
        }
        html.dark-mode #admin-dashboard .border-orange-200,
        html.dark-mode #admin-dashboard .border-orange-400 {
            border-color: #8a5b23 !important;
        }
        html.dark-mode #admin-dashboard .text-orange-700,
        html.dark-mode #admin-dashboard .text-orange-800,
        html.dark-mode #admin-dashboard .text-orange-600 {
            color: #f5b868 !important;
        }
        html.dark-mode #admin-dashboard .fc {
            color: #dbe4f3;
        }
        html.dark-mode #admin-dashboard .fc .fc-toolbar-title {
            color: #dbe4f3;
        }
        html.dark-mode #admin-dashboard .fc .fc-button {
            background: #0b1220;
            border: 1px solid #1f2a44;
            color: #d6f5a3;
        }
        html.dark-mode #admin-dashboard .fc .fc-button:hover {
            background: #1f2a44;
            border-color: #2b3b5e;
            color: #e5f5c5;
        }
        html.dark-mode #admin-dashboard .fc .fc-button-primary:not(:disabled).fc-button-active {
            background: #7aa23a;
            border-color: #7aa23a;
            color: #0b1220;
        }
        html.dark-mode #admin-dashboard .fc .fc-daygrid-day-number,
        html.dark-mode #admin-dashboard .fc .fc-timegrid-slot-label,
        html.dark-mode #admin-dashboard .fc .fc-col-header-cell-cushion {
            color: #a9b8d4;
        }
        html.dark-mode #admin-dashboard .fc .fc-scrollgrid,
        html.dark-mode #admin-dashboard .fc .fc-scrollgrid-section > td,
        html.dark-mode #admin-dashboard .fc .fc-timegrid-slot,
        html.dark-mode #admin-dashboard .fc .fc-timegrid-axis,
        html.dark-mode #admin-dashboard .fc .fc-daygrid-day {
            border-color: #1f2a44;
        }
        html.dark-mode #admin-dashboard .fc .fc-timegrid-now-indicator-line,
        html.dark-mode #admin-dashboard .fc .fc-timegrid-now-indicator-arrow {
            border-color: #f97316;
        }
    </style>
    <div id="page-loader" class="admin-loader fixed inset-0 z-50 flex items-center justify-center bg-[#FAFAF7] transition-opacity duration-200">
        <div class="h-10 w-10 rounded-full border-4 border-gray-200 border-t-gray-500 animate-spin"></div>
    </div>
    <div class="admin-page w-full h-screen flex flex-col items-center px-4 bg-[#FAFAF7] overflow-hidden">
        <div class="w-full max-w-7xl py-8 flex flex-col" style="height: calc(100vh - 2rem);">
            <div class="mb-3 text-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Tickets TI" class="theme-logo-light admin-logo mx-auto h-8" style="width: 130px; height: auto;">
                <img src="{{ asset('images/logo-white.png') }}" alt="Logo Tickets TI" class="theme-logo-dark admin-logo mx-auto h-8" style="width: 130px; height: auto;">
            </div>

            <div class="mb-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div x-data="{ open: false }" class="relative">
                        <button
                            type="button"
                            @click="open = !open"
                            @click.outside="open = false"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700 bg-white hover:bg-gray-50 transition">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5h18M6 12h12M10 19h4" />
                            </svg>
                            Filtros
                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div
                            x-show="open"
                            x-transition
                            class="absolute left-0 mt-2 w-max max-w-[90vw] bg-white border border-gray-200 rounded-xl shadow-sm z-50 p-4 space-y-3">
                            <div>
                                <div class="text-xs uppercase tracking-wide text-gray-500 mb-2">Dominio</div>
                                <div class="flex flex-nowrap gap-2 overflow-x-auto">
                                    @foreach($domainCards as $key => $card)
                                        <button
                                            type="button"
                                            data-domain-filter="{{ $key }}"
                                            class="domain-chip inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ $card['text'] }}"
                                            style="{{ $card['style'] }}">
                                            {{ $card['label'] }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <div class="text-xs uppercase tracking-wide text-gray-500 mb-2">Técnico</div>
                                <div class="flex flex-nowrap gap-2 overflow-x-auto">
                                    <button
                                        type="button"
                                        data-tech-filter="unassigned"
                                        class="tech-chip inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 transition">
                                        Sin asignar
                                    </button>
                                    @foreach($admins as $adminUser)
                                        <button
                                            type="button"
                                            data-tech-filter="{{ $adminUser->id }}"
                                            class="tech-chip inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 transition">
                                            {{ $adminUser->name }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <div class="text-xs uppercase tracking-wide text-gray-500 mb-2">Estado</div>
                                <div class="flex flex-nowrap gap-2 overflow-x-auto">
                                    @foreach(['nuevo', 'asignado', 'agendado', 'standby', 'resuelto'] as $state)
                                        <button
                                            type="button"
                                            data-status-filter="{{ $state }}"
                                            class="status-chip inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 transition">
                                            {{ strtoupper(str_replace('_', ' ', $state === 'standby' ? 'en espera' : $state)) }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3 ml-auto">
                    <div x-data="{ open: false }" class="relative">
                        <button
                            type="button"
                            @click="open = !open"
                            @click.outside="open = false"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700 bg-white hover:bg-gray-50 transition">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 4h18M3 12h18M3 20h18" />
                            </svg>
                            Menú
                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div
                            x-show="open"
                            x-transition
                            class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-sm z-50">
                            <a href="{{ route('admin.tickets.index') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-xl">
                                Gestor de tickets
                            </a>
                            <a href="{{ route('admin.calendar.index') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-xl">
                                Calendario
                            </a>
                            <a href="{{ route('admin.locaciones.index') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-xl">
                                Locaciones
                            </a>
                            <a href="{{ route('admin.profiles.index') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-xl">
                                Perfiles
                            </a>
                            <a href="{{ route('admin.printers.index') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-xl">
                                Impresoras
                            </a>
                            <a href="{{ route('admin.logs.index') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-xl">
                                Logs
                            </a>
                        </div>
                    </div>
                    <a
                        href="{{ route('ticket.create') }}"
                        class="admin-cta inline-flex items-center gap-1.5 px-3 py-2 rounded-full border border-[#6B8E23] text-sm font-medium text-[#6B8E23] bg-[#F4F7EE] hover:bg-[#E9F0DF] transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Nuevo ticket
                    </a>
                    <a
                        href="{{ route('admin.tickets.assisted.create') }}"
                        class="admin-cta inline-flex items-center gap-1.5 px-3 py-2 rounded-full border border-[#D97706] text-sm font-medium text-[#D97706] bg-[#FFF7ED] hover:bg-[#FFEDD5] transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Ticket asistido
                    </a>
                </div>
            </div>

            <div class="flex-1 overflow-hidden grid grid-cols-1 lg:grid-cols-2 gap-6 min-h-0">
                <div class="grid grid-rows-2 gap-6 h-full min-h-0">
                    <div class="min-h-0 h-full overflow-hidden">
                        @include('admin.calendar.embedded', ['tickets' => $calendarTickets])
                    </div>
                    <div class="bg-white rounded-xl shadow-xl border border-gray-200 p-6 flex flex-col gap-4 h-full min-h-0 overflow-hidden" x-data="{ mode: 'tech' }">
                        <div class="flex items-center justify-between gap-3">
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-600 text-left">ID números</div>
                            <div class="flex items-center gap-2 text-xs font-semibold">
                                <button type="button"
                                    @click="mode = 'domain'"
                                    :class="mode === 'domain' ? 'bg-gray-900 text-white' : 'bg-white text-gray-600 border border-gray-300'"
                                    class="rounded-full px-3 py-1 transition">
                                    Por dominio
                                </button>
                                <button type="button"
                                    @click="mode = 'tech'"
                                    :class="mode === 'tech' ? 'bg-gray-900 text-white' : 'bg-white text-gray-600 border border-gray-300'"
                                    class="rounded-full px-3 py-1 transition">
                                    Por técnico
                                </button>
                            </div>
                        </div>

                        <div class="flex-1 min-h-0 overflow-hidden">
                        <div class="flex flex-col gap-3 w-full max-w-full overflow-y-auto pr-2 h-full" x-show="mode === 'domain'">
                            <div class="grid grid-cols-[minmax(0,1.6fr)_minmax(0,0.2fr)_minmax(0,0.7fr)_minmax(0,0.7fr)_minmax(0,0.9fr)_minmax(0,0.9fr)] gap-x-4 text-[10px] md:text-xs font-semibold text-gray-500 uppercase tracking-wide px-4 w-full leading-tight">
                                <div class="min-w-0 break-words">DOMINIO</div>
                                <div></div>
                                <div class="text-right break-words">TOTAL</div>
                                <div class="text-right break-words">NUEVOS</div>
                                <div class="text-right break-words">ASIGNADOS</div>
                                <div class="text-right break-words">RESUELTOS</div>
                            </div>
                            @foreach($domainCards as $key => $card)
                                @php
                                    $stats = $domainStats[$key] ?? ['total' => 0, 'nuevo' => 0, 'asignado' => 0, 'resuelto' => 0];
                                @endphp
                                <div class="rounded-xl px-5 py-4 {{ $card['text'] }} w-full max-w-full" style="{{ $card['style'] }}">
                                    <div class="grid grid-cols-[minmax(0,1.6fr)_minmax(0,0.2fr)_minmax(0,0.7fr)_minmax(0,0.7fr)_minmax(0,0.9fr)_minmax(0,0.9fr)] gap-x-4 items-center text-sm md:text-base font-semibold w-full leading-snug">
                                        <div class="min-w-0 break-words">{{ $card['label'] }}</div>
                                        <div></div>
                                        <div class="text-right">{{ $stats['total'] }}</div>
                                        <div class="text-right">{{ $stats['nuevo'] }}</div>
                                        <div class="text-right">{{ $stats['asignado'] }}</div>
                                        <div class="text-right">{{ $stats['resuelto'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="rounded-xl px-5 py-4 text-white w-full max-w-full" style="background-color: #4B5563;">
                                <div class="grid grid-cols-[minmax(0,1.6fr)_minmax(0,0.2fr)_minmax(0,0.7fr)_minmax(0,0.7fr)_minmax(0,0.9fr)_minmax(0,0.9fr)] gap-x-4 items-center text-sm md:text-base font-semibold w-full leading-snug">
                                    <div class="min-w-0 break-words">Total</div>
                                    <div></div>
                                    <div class="text-right">{{ $totalStats['total'] ?? 0 }}</div>
                                    <div class="text-right">{{ $totalStats['nuevo'] ?? 0 }}</div>
                                    <div class="text-right">{{ $totalStats['asignado'] ?? 0 }}</div>
                                    <div class="text-right">{{ $totalStats['resuelto'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 w-full max-w-full overflow-y-auto pr-2 h-full" x-show="mode === 'tech'">
                            <div class="grid grid-cols-[minmax(0,1.6fr)_minmax(0,0.2fr)_minmax(0,1fr)_minmax(0,1fr)] gap-x-4 text-[10px] md:text-xs font-semibold text-gray-500 uppercase tracking-wide px-4 w-full leading-tight">
                                <div class="min-w-0 break-words">TÉCNICO</div>
                                <div></div>
                                <div class="text-right break-words">ASIGNADOS</div>
                                <div class="text-right break-words">RESUELTOS</div>
                            </div>
                            @foreach($techCards as $tech)
                                <a href="{{ route('admin.dashboard.tech', $tech['id']) }}" class="group relative rounded-xl px-4 py-3 text-white w-full max-w-full block transition hover:opacity-95 overflow-hidden" style="background-color: #1F2937;">
                                    <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition"></div>
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                        <div class="text-[10px] sm:text-xs text-white/90 bg-black/40 px-2 py-1 rounded-full">
                                            clic para ver detalle por técnico
                                        </div>
                                    </div>
                                    <div class="relative grid grid-cols-[minmax(0,1.6fr)_minmax(0,0.2fr)_minmax(0,1fr)_minmax(0,1fr)] gap-x-4 items-center text-xs md:text-sm font-semibold w-full leading-tight">
                                        <div class="min-w-0 break-words">{{ $tech['name'] }}</div>
                                        <div></div>
                                        <div class="text-right">{{ $tech['assigned'] }}</div>
                                        <div class="text-right">{{ $tech['resolved'] }}</div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        </div>
                    </div>
                </div>

                <div class="min-h-0">
                    @include('admin.tickets.embedded', ['tickets' => $adminTickets, 'admins' => $admins])
                </div>
            </div>
        </div>
    </div>

    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const domainChips = Array.from(document.querySelectorAll('[data-domain-filter]'));
                const techChips = Array.from(document.querySelectorAll('[data-tech-filter]'));
                const statusChips = Array.from(document.querySelectorAll('[data-status-filter]'));
                const ticketCards = Array.from(document.querySelectorAll('[data-domain-keys]'));
                let activeDomain = null;
                let activeTech = null;
                let activeStatus = null;

                const parseKeys = (value) => {
                    if (!value) {
                        return [];
                    }
                    return value.split(',').map((item) => item.trim()).filter(Boolean);
                };

                const applyTicketFilter = (domainKey, techId, statusKey) => {
                    ticketCards.forEach((card) => {
                        const keys = parseKeys(card.dataset.domainKeys || '');
                        const techIds = parseKeys(card.dataset.technicianIds || '');
                        const status = card.dataset.statusKey || '';
                        const showDomain = !domainKey || keys.includes(domainKey);
                        const showTech = !techId
                            || (techId === 'unassigned' ? techIds.length === 0 : techIds.includes(String(techId)));
                        const showStatus = !statusKey || status === statusKey;
                        const show = showDomain && showTech && showStatus;
                        card.classList.toggle('hidden', !show);
                    });
                };

                const setActiveDomainChip = (domainKey) => {
                    domainChips.forEach((chip) => {
                        const isActive = chip.dataset.domainFilter === domainKey;
                        chip.classList.toggle('ring-2', isActive);
                        chip.classList.toggle('ring-black/20', isActive);
                        chip.classList.toggle('opacity-70', domainKey && !isActive);
                        chip.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                    });
                };

                const setActiveTechChip = (techId) => {
                    techChips.forEach((chip) => {
                        const isActive = String(chip.dataset.techFilter) === String(techId);
                        chip.classList.toggle('ring-2', isActive);
                        chip.classList.toggle('ring-black/20', isActive);
                        chip.classList.toggle('opacity-70', techId && !isActive);
                        chip.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                    });
                };

                const setActiveStatusChip = (statusKey) => {
                    statusChips.forEach((chip) => {
                        const isActive = chip.dataset.statusFilter === statusKey;
                        chip.classList.toggle('ring-2', isActive);
                        chip.classList.toggle('ring-black/20', isActive);
                        chip.classList.toggle('opacity-70', statusKey && !isActive);
                        chip.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                    });
                };

                const applyFilter = (domainKey, techId, statusKey) => {
                    activeDomain = domainKey || null;
                    activeTech = techId || null;
                    activeStatus = statusKey || null;
                    setActiveDomainChip(activeDomain);
                    setActiveTechChip(activeTech);
                    setActiveStatusChip(activeStatus);
                    applyTicketFilter(activeDomain, activeTech, activeStatus);
                    if (window.adminDomainFilter && typeof window.adminDomainFilter.apply === 'function') {
                        window.adminDomainFilter.apply(activeDomain, activeTech, activeStatus);
                    }
                };

                domainChips.forEach((chip) => {
                    chip.addEventListener('click', () => {
                        const key = chip.dataset.domainFilter;
                        if (activeDomain === key) {
                            applyFilter(null, activeTech, activeStatus);
                            return;
                        }
                        applyFilter(key, activeTech, activeStatus);
                    });
                });

                techChips.forEach((chip) => {
                    chip.addEventListener('click', () => {
                        const key = chip.dataset.techFilter;
                        if (String(activeTech) === String(key)) {
                            applyFilter(activeDomain, null, activeStatus);
                            return;
                        }
                        applyFilter(activeDomain, key, activeStatus);
                    });
                });

                statusChips.forEach((chip) => {
                    chip.addEventListener('click', () => {
                        const key = chip.dataset.statusFilter;
                        if (activeStatus === key) {
                            applyFilter(activeDomain, activeTech, null);
                            return;
                        }
                        applyFilter(activeDomain, activeTech, key);
                    });
                });

                applyFilter(null, null, null);

                // Se vuelve al flujo con recarga completa por cada acción.
            });
        </script>
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
    @endpush
</x-layouts.clean>
