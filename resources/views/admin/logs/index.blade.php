<x-layouts.clean>
    <div class="w-full h-screen flex flex-col items-center px-4 bg-[#FAFAF7] overflow-hidden">
        <div class="w-full max-w-3xl py-8 flex flex-col justify-center" style="height: calc(100vh - 2rem);">

            <div class="mb-6 text-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Tickets TI" class="mx-auto h-12" style="width: 200px; height: auto;">
                <p class="text-gray-600">Log general del sistema (errores + cambios).</p>
            </div>

            <div class="bg-white rounded-xl shadow-xl border border-gray-200 p-6 flex flex-col"
                style="height: 560px; min-height: 560px; max-height: 560px;">

                <div class="flex items-center justify-between mb-4 gap-3">
                    <form action="{{ route('dashboard') }}">
                        <button
                            class="rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                            type="submit">
                            &larr; Volver
                        </button>
                    </form>

                    <form method="GET" action="{{ route('admin.logs.index') }}" class="flex items-center gap-2">
                        <input type="date" name="date" value="{{ $date }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700">
                        <input type="time" name="from" value="{{ $from }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700">
                        <input type="time" name="to" value="{{ $to }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700">
                        <button
                            type="submit"
                            class="rounded-lg border border-[#6B8E23] px-4 py-2 text-sm text-[#6B8E23] hover:bg-[#F4F7EE]">
                            Filtrar
                        </button>
                    </form>
                </div>

                <div class="text-xs text-gray-500 mb-3">
                    Mostrando hasta 200 registros. Fecha: {{ $date ?? 'sin filtro' }}
                </div>

                <div class="flex-1 overflow-y-auto space-y-3 pr-2">
                    @forelse($entries as $entry)
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 text-sm text-gray-700">
                            <div class="flex items-center justify-between gap-3">
                                <div class="text-xs text-gray-500">
                                    {{ $entry['date'] }} {{ $entry['time'] }}
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-semibold uppercase px-2 py-0.5 rounded-full
                                        {{ ($entry['tag'] ?? '') === 'ERROR' ? 'bg-red-100 text-red-700' : '' }}
                                        {{ ($entry['tag'] ?? '') === 'ALERTA' ? 'bg-orange-100 text-orange-700' : '' }}
                                        {{ ($entry['tag'] ?? '') === 'OUTPUT' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                        {{ ($entry['tag'] ?? '') === 'INPUT' ? 'bg-blue-100 text-blue-700' : '' }}
                                    ">
                                        {{ $entry['tag'] ?? '—' }}
                                    </span>
                                    <span class="text-xs font-semibold text-gray-600 uppercase">
                                        {{ $entry['level'] }}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-2 text-sm text-gray-700">
                                {{ $entry['message'] }}
                            </div>
                            @if(!empty($entry['user']))
                                <div class="mt-1 text-xs text-gray-500">
                                    Por: {{ $entry['user'] }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-6">
                            No hay registros para el rango seleccionado.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.clean>
