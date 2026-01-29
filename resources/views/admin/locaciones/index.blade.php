<x-layouts.clean>
    <div class="w-full h-screen flex flex-col items-center px-4 bg-[#FAFAF7] overflow-hidden">
        <div x-data="{ showModal: false, editingLocation: null }" class="w-full max-w-xl py-8 flex flex-col justify-center" style="height: calc(100vh - 2rem);">

            <div class="mb-6 text-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Tickets TI" class="mx-auto h-12" style="width: 200px; height: auto;">
                <p class="text-gray-600">Administra las locaciones.</p>
            </div>

            <div class="bg-white rounded-xl shadow-xl border border-gray-200 p-6 flex flex-col"
                style="height: 380px; min-height: 380px; max-height: 380px;">

                {{-- Opciones --}}
                <div class="flex items-center justify-between mb-4 gap-3">
                    <div class="flex w-full items-center gap-3">
                        <form action="{{ route('dashboard') }}">
                            <button
                                class="mb-3 rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                                type="submit">
                                &larr; Volver
                            </button>
                        </form>
                        <button
                            class="mb-3 rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                            @click="editingLocation = null; showModal = true">
                            + Crear Locación
                        </button>
                    </div>
                </div>

                @if(session('success'))
                <div
                    x-data="{ show: true }"
                    x-init="setTimeout(() => show = false, 5000)"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-2"
                    class="fixed top-4 right-4 z-50 max-w-sm w-full sm:w-auto">
                    <div class="flex items-start gap-3 p-4 bg-green-100 border border-green-200 text-green-800 rounded-lg shadow-lg">
                        {{-- Icono --}}
                        <svg class="w-5 h-5 mt-0.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>

                        {{-- Mensaje --}}
                        <p class="text-sm">
                            {{ session('success') }}
                        </p>

                        {{-- Cerrar manual --}}
                        <button
                            @click="show = false"
                            class="ml-auto text-green-700 hover:text-green-900">
                            ✕
                        </button>
                    </div>
                </div>
                @endif


                {{-- Tabla de usuarios --}}
                <div class="flex-1 overflow-y-auto" style="max-height: calc(380px - 2rem);">
                    <div class="space-y-3 pr-2">
                        @foreach($establecimientos as $est)
                        <div
                            x-data="{ open: false }"
                            class="border rounded-lg p-4 bg-gray-50">
                            {{-- Header --}}
                            <button
                                type="button"
                                @click="open = !open"
                                class="w-full flex items-center justify-between text-left">
                                <div>
                                    <span class="font-medium text-gray-800">
                                        {{ $est->nombre }}
                                    </span>
                                    <p class="text-sm text-gray-600">{{ $est->slug }}</p>
                                </div>

                                <div class="flex items-center gap-3">
                                    {{-- Cantidad de hijos --}}
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-gray-200 text-gray-700">
                                        {{ $est->hijos->count() }}
                                    </span>

                                    {{-- Flecha --}}
                                    <svg
                                        class="w-4 h-4 text-gray-500 transform transition-transform duration-200"
                                        :class="{ 'rotate-180': open }"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </button>

                            {{-- Contenido --}}
                            <div
                                x-show="open"
                                x-collapse
                                class="mt-3">
                                @if($est->hijos->count())
                                <ul class="ml-4 list-disc text-sm text-gray-700 space-y-1">
                                    @foreach($est->hijos as $hijo)
                                    <li>{{ $hijo->nombre }}</li>
                                    @endforeach
                                </ul>
                                @else
                                <p class="text-sm text-gray-500">
                                    Sin locaciones hijas
                                </p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <div x-show="showModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg w-96 p-6" @click.away="showModal=false; editingLocation=null" x-data="{
        nombre: '',
        slug: '',
        generarSlug() {
            this.slug = this.nombre
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '') // quita tildes
                .replace(/ñ/g, 'n')
                .replace(/[^a-z0-9\s-]/g, '')
                .trim()
                .replace(/\s+/g, '-');
        }
    }">
                    <h2 class="text-lg font-semibold mb-4" x-text="editingLocation ? 'Editar locación' : 'Crear locación'"></h2>

                    <form :action="editingLocation ? `/admin/locaciones/${editingLocation.id}` : '{{ route('admin.locaciones.store') }}'"
                        method="POST">
                        @csrf

                        {{-- Nombre --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nombre
                            </label>
                            <input
                                type="text"
                                name="nombre"
                                x-model="nombre"
                                @input="generarSlug"
                                required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        {{-- Slug --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Slug
                            </label>
                            <input
                                type="text"
                                name="slug"
                                x-model="slug"
                                readonly
                                required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-gray-100 text-gray-700 focus:outline-none">
                            <p class="text-xs text-gray-500 mt-1">
                                Se genera automáticamente desde el nombre
                            </p>
                        </div>


                        {{-- Padre --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Depende de
                            </label>
                            <select
                                name="parent_id"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">
                                    — Establecimiento (raíz) —
                                </option>
                                @foreach($establecimientos as $est)
                                <option value="{{ $est->id }}">
                                    {{ $est->nombre }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Botón --}}
                        <div class="flex justify-end pt-2">
                            <button
                                type="submit"
                                class="rounded-lg bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700 transition">
                                Crear locación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.clean>