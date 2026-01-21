<x-layouts.clean>
    <div class="w-full h-screen flex flex-col items-center px-4 bg-[#FAFAF7] overflow-hidden">

        {{-- Contenedor principal centrado verticalmente --}}
        <div class="w-full max-w-xl py-8 flex flex-col justify-center" style="height: calc(100vh - 2rem);">

            {{-- Header sobrio --}}
            <div class="mb-10 text-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Tickets TI" class="mx-auto h-12" style="width: 200px">
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

                {{-- Estado GLPI --}}
                @php
                $glpiChip = match($glpiStatus ?? 'offline') {
                'online' => [
                'text' => 'GLPI conectado',
                'classes' => 'border-[#6B8E23] text-[#6B8E23] bg-[#F4F7EE]',
                ],
                'error' => [
                'text' => 'Error GLPI',
                'classes' => 'border-red-300 text-red-700 bg-red-50',
                ],
                default => [
                'text' => 'Modo offline',
                'classes' => 'border-gray-300 text-gray-600 bg-gray-100',
                ],
                };
                @endphp

                <div class="flex items-center gap-2 px-3 py-1.5 rounded-full border text-sm {{ $glpiChip['classes'] }}">
                    <span class="w-2 h-2 rounded-full
                        {{ $glpiStatus === 'online'
                            ? 'bg-[#6B8E23]'
                            : ($glpiStatus === 'error' ? 'bg-red-500' : 'bg-gray-400')
                        }}">
                    </span>

                    {{ $glpiChip['text'] }}
                </div>

            </div>

            {{-- Card principal --}}
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 p-6 flex flex-col" 
                 style="height: 380px; min-height: 380px; max-height: 380px;">

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
                <div class="flex-1 overflow-y-auto" style="max-height: calc(380px - 5rem);">

                    <div class="space-y-3 pr-2">
                        @forelse($ultimos as $ticket)
                        @php
                        $esPendiente = in_array($ticket->estado_glpi, [
                        'recibido',
                        'en_proceso',
                        'en_espera',
                        ]);

                        $idHibrido = $ticket->glpi_ticket_id
                        ? "{$ticket->id}-{$ticket->glpi_ticket_id}"
                        : "W{$ticket->id}-XXXX";
                        @endphp

                        <div
                            class="border rounded-xl mb-2 p-4 flex justify-between items-center
                                {{ $esPendiente ? 'border-[#6B8E23] bg-[#F4F7EE]' : 'border-gray-200 bg-white' }}">
                            <div>
                                <div class="font-medium">
                                    {{ $ticket->categoria }}
                                </div>

                                <div class="text-xs text-gray-400 mt-1">
                                    ID {{ $idHibrido }}
                                </div>

                                <div class="text-sm text-gray-500">
                                    {{ $ticket->created_at->format('d-m-Y H:i') }}
                                </div>
                            </div>

                            <div class="text-sm font-medium {{ $esPendiente ? 'text-[#6B8E23]' : 'text-gray-500' }}">
                                {{ ucfirst(str_replace('_', ' ', $ticket->estado_glpi)) }}
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

            {{-- Acciones administrativas --}}
            <div class="mt-6 flex flex-col gap-3 pb-4 flex-shrink-0">

                <form method="POST" action="{{ route('dashboard.sync-estados') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        Actualizar estados desde GLPI
                    </button>
                </form>

                @if(\App\Models\Ticket::whereIn('estado_envio_glpi', ['pendiente','error'])->exists())
                <form method="POST" action="{{ route('dashboard.reenviar-pendientes') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full rounded-xl border border-[#6B8E23] px-4 py-2 text-sm text-[#6B8E23] hover:bg-[#F4F7EE]">
                        Reenviar tickets pendientes a GLPI
                    </button>
                </form>
                @endif

            </div>

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