<x-layouts.clean>
    <div class="w-full h-screen flex flex-col items-center px-4 bg-[#FAFAF7] overflow-hidden">
        <div class="w-full max-w-7xl py-8 flex flex-col" style="height: calc(100vh - 2rem);">
            <div class="mb-6 text-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Tickets TI" class="mx-auto h-12" style="width: 200px; height: auto;">
                <p class="text-gray-600">Dashboard de Administrador</p>
            </div>

            <div class="mb-4 flex items-center justify-between gap-3">
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
                                @foreach(['nuevo', 'asignado', 'resuelto'] as $state)
                                    <button
                                        type="button"
                                        data-status-filter="{{ $state }}"
                                        class="status-chip inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 transition">
                                        {{ strtoupper(str_replace('_', ' ', $state)) }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
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
            </div>

            <div class="flex-1 overflow-hidden grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="grid grid-rows-2 gap-6 h-full">
                    <div class="min-h-0">
                        @include('admin.calendar.embedded', ['tickets' => $calendarTickets])
                    </div>
                    <div class="bg-white rounded-xl shadow-xl border border-gray-200 p-6 flex flex-col gap-3">
                        <div class="flex flex-col gap-3 w-full max-w-full overflow-hidden">
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
                    </div>
                </div>

                <div class="min-h-0">
                    @include('admin.tickets.embedded', ['tickets' => $adminTickets, 'admins' => $admins])
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
                        const tech = card.dataset.technicianId || '';
                        const status = card.dataset.statusKey || '';
                        const showDomain = !domainKey || keys.includes(domainKey);
                        const showTech = !techId
                            || (techId === 'unassigned' ? tech === '' : tech === String(techId));
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
            });
        </script>
    @endpush
</x-layouts.clean>
