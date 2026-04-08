<x-layouts.clean>
    <div class="w-full h-screen flex flex-col items-center px-4 bg-[#FAFAF7] overflow-hidden">
        <div class="w-full max-w-7xl py-8 flex flex-col" style="height: calc(100vh - 2rem);">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Tickets TI" class="theme-logo-light h-8" style="width: 130px; height: auto;">
                    <img src="{{ asset('images/logo-white.png') }}" alt="Logo Tickets TI" class="theme-logo-dark h-8" style="width: 130px; height: auto;">
                </div>
                <a href="{{ route('admin.dashboard') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-full border border-gray-300 text-sm text-gray-700 bg-white hover:bg-gray-50 transition">
                    &larr; Volver
                </a>
            </div>

            <div class="mb-4">
                <div class="text-sm text-gray-500">Detalle por técnico</div>
                <div class="text-lg font-semibold text-gray-800">{{ $technician->name }}</div>
                <div class="text-sm text-gray-500">{{ $technician->email }}</div>
            </div>

            <div class="flex-1 overflow-hidden grid grid-cols-1 lg:grid-cols-2 gap-6 min-h-0">
                <div class="grid grid-rows-2 gap-6 h-full min-h-0">
                    <div class="bg-white rounded-xl shadow-xl border border-gray-200 p-6 flex flex-col gap-4 h-full min-h-0">
                        <div class="flex items-center justify-between gap-3">
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-600">Resumen</div>
                            <form method="GET" action="{{ route('admin.dashboard.tech', $technician->id) }}">
                                <select name="days" onchange="this.form.submit()" class="rounded-lg border border-gray-300 px-2 py-1 text-xs text-gray-700">
                                    @foreach([7,14,30,60,90] as $option)
                                        <option value="{{ $option }}" @selected($days === $option)>
                                            Últimos {{ $option }} días
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm text-gray-700">
                            <div>
                                <div class="text-xs text-gray-400">Resueltos / cerrados</div>
                                <div class="text-lg font-semibold">{{ $stats['resolved_period'] }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400">Velocidad promedio</div>
                                <div class="text-lg font-semibold">{{ $stats['speed_per_day'] }} / día</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400">Tiempo promedio</div>
                                <div class="text-lg font-semibold">{{ $stats['avg_resolution'] }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400">Mediana</div>
                                <div class="text-lg font-semibold">{{ $stats['median_resolution'] }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-xl border border-gray-200 p-6 flex flex-col gap-4 h-full min-h-0 overflow-hidden">
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-600">Resueltos y velocidad ({{ $days }} días)</div>
                        <div class="flex items-center gap-4 text-[10px] text-gray-500">
                            <div class="flex items-center gap-1">
                                <span class="inline-block h-2 w-2 rounded-sm bg-[#6B8E23]"></span> Resueltos
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="inline-block h-2 w-2 rounded-sm bg-[#2F7FA3]"></span> Tiempo promedio
                            </div>
                        </div>
                        <div class="flex-1 overflow-x-auto">
                            <div class="flex items-end gap-2 h-full min-h-[160px] pr-2">
                                @foreach($chartData as $point)
                                    @php
                                        $resolvedHeight = $chartMaxResolved > 0 ? ($point['resolved'] / $chartMaxResolved) * 100 : 0;
                                        $avgHeight = $chartMaxAvgMinutes > 0 ? ($point['avg_minutes'] / $chartMaxAvgMinutes) * 100 : 0;
                                    @endphp
                                    <div class="flex flex-col items-center gap-2 w-6">
                                        <div class="flex items-end gap-1 h-28 w-full">
                                            <div class="flex-1 bg-[#6B8E23]/70 rounded-sm" style="height: {{ $resolvedHeight }}%"></div>
                                            <div class="flex-1 bg-[#2F7FA3]/70 rounded-sm" style="height: {{ $avgHeight }}%"></div>
                                        </div>
                                        <div class="text-[9px] text-gray-500">{{ $point['label'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-xl border border-gray-200 p-6 flex flex-col gap-4 h-full min-h-0 overflow-hidden">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-600">Tickets involucrados</div>
                    <div class="flex-1 overflow-y-auto pr-2 space-y-4">
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wide mb-2">Asignados activos</div>
                            <div class="space-y-3">
                                @forelse($assignedTickets as $ticket)
                                    @php
                                        $locacionLabel = \App\Support\TicketView::locationLabel($ticket);
                                        $status = \App\Support\TicketView::statusLabel($ticket->latestStatusEvent?->to_status ?? 'nuevo', 'code');
                                    @endphp
                                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 text-sm text-gray-700">
                                        <div class="flex items-center justify-between">
                                            <div class="font-medium">#{{ $ticket->display_id }}</div>
                                            <div class="text-xs text-gray-500">{{ $status }}</div>
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $ticket->usuario_mail }}</div>
                                        <div class="text-xs text-gray-400">Ubicación: {{ $locacionLabel }}</div>
                                        <div class="mt-1 text-sm text-gray-700">{{ \Illuminate\Support\Str::limit($ticket->descripcion, 120) }}</div>
                                    </div>
                                @empty
                                    <div class="text-sm text-gray-500">No hay tickets activos asignados.</div>
                                @endforelse
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wide mb-2">Resueltos</div>
                            <div class="space-y-3">
                                @forelse($resolvedTickets as $ticket)
                                    @php
                                        $resolvedAt = $ticket->resolved_at ? $ticket->resolved_at->format('d-m-Y H:i') : '—';
                                        $locacionLabel = \App\Support\TicketView::locationLabel($ticket);
                                    @endphp
                                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 text-sm text-gray-700">
                                        <div class="flex items-center justify-between">
                                            <div class="font-medium">#{{ $ticket->display_id }}</div>
                                            <div class="text-xs text-gray-500">{{ $resolvedAt }}</div>
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $ticket->usuario_mail }}</div>
                                        <div class="text-xs text-gray-400">Ubicación: {{ $locacionLabel }}</div>
                                        <div class="mt-1 text-sm text-gray-700">{{ \Illuminate\Support\Str::limit($ticket->descripcion, 120) }}</div>
                                    </div>
                                @empty
                                    <div class="text-sm text-gray-500">Sin tickets resueltos aún.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.clean>
