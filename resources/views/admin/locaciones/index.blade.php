<x-layouts.clean>
    @php
        $establecimientosPayload = $establecimientos->map(function ($est) {
            return [
                'id' => $est->id,
                'nombre' => $est->nombre,
                'slug' => $est->slug,
                'updated_at' => optional($est->updated_at)->format('Y-m-d H:i'),
                'hijos' => $est->hijos->map(function ($hijo) {
                    return [
                        'id' => $hijo->id,
                        'nombre' => $hijo->nombre,
                        'slug' => $hijo->slug,
                    ];
                })->values(),
            ];
        })->values();
    @endphp

    <div class="w-full h-screen flex flex-col items-center px-4 bg-[#FAFAF7] overflow-hidden">
        <div x-data="locacionesDrawer()" class="w-full max-w-6xl py-8 flex flex-col" style="height: calc(100vh - 2rem);">

            <div class="mb-6 text-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Tickets TI" class="mx-auto h-12" style="width: 200px; height: auto;">
                <p class="text-gray-600">Organiza establecimientos y locaciones hijas de forma visual.</p>
            </div>

            <div class="bg-white rounded-xl shadow-xl border border-gray-200 p-6 flex flex-col flex-1 overflow-hidden">
                @if(session('success'))
                    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                        {{ session('success') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <form action="{{ route('dashboard') }}">
                        <button
                            class="rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                            type="submit">
                            &larr; Volver
                        </button>
                    </form>

                    <div class="flex flex-1 items-center gap-3">
                        <div class="flex-1">
                            <input type="text"
                                x-model="query"
                                placeholder="Buscar por nombre o slug..."
                                class="w-full rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700 focus:border-[#6B8E23] focus:ring-[#6B8E23]">
                        </div>
                        <button
                            type="button"
                            class="rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                            @click="showCreate = !showCreate">
                            + Nuevo establecimiento
                        </button>
                    </div>
                </div>

                <div x-show="showCreate" x-transition class="mb-4 rounded-xl border border-dashed border-gray-200 p-4 bg-gray-50">
                    <form method="POST" action="{{ route('admin.locaciones.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        @csrf
                        <div>
                            <label class="block text-xs uppercase tracking-wide text-gray-500 mb-1">Nombre</label>
                            <input type="text" name="nombre" x-model="createParentName" @input="createParentSlug = slugify(createParentName)"
                                required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700">
                        </div>
                        <div>
                            <label class="block text-xs uppercase tracking-wide text-gray-500 mb-1">Slug</label>
                            <input type="text" name="slug" x-model="createParentSlug"
                                required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700">
                        </div>
                        <div class="flex items-end">
                            <button type="submit"
                                class="w-full rounded-lg border border-[#6B8E23] px-4 py-2 text-sm text-[#6B8E23] hover:bg-[#F4F7EE]">
                                Crear
                            </button>
                        </div>
                    </form>
                </div>

                <div class="flex-1 overflow-y-auto pr-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        <template x-for="est in filteredEstablishments()" :key="est.id">
                            <div class="border border-gray-200 rounded-xl p-4 bg-gray-50 flex flex-col gap-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm uppercase tracking-wide text-gray-500">Establecimiento</p>
                                        <p class="text-lg font-semibold text-gray-800" x-text="est.nombre"></p>
                                        <p class="text-xs text-gray-500" x-text="est.slug"></p>
                                    </div>
                                    <span class="text-xs px-2 py-1 rounded-full bg-white border border-gray-200 text-gray-600"
                                        x-text="`${est.hijos.length} hijas`"></span>
                                </div>

                                <div class="text-xs text-gray-500">
                                    Última actualización: <span x-text="est.updated_at || '—'"></span>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <button
                                        type="button"
                                        class="rounded-lg border border-gray-300 px-3 py-2 text-xs text-gray-700 hover:bg-gray-50"
                                        @click="openDrawer(est)">
                                        Ver detalle
                                    </button>
                                    <button
                                        type="button"
                                        class="rounded-lg border border-[#6B8E23] px-3 py-2 text-xs text-[#6B8E23] hover:bg-[#F4F7EE]"
                                        @click="openDrawer(est, true)">
                                        + Agregar hija
                                    </button>
                                </div>

                                <div class="flex flex-wrap gap-2 text-xs text-gray-600">
                                    <template x-if="est.hijos.length === 0">
                                        <span class="px-2 py-1 rounded-full bg-white border border-dashed border-gray-300 text-gray-500">
                                            Sin locaciones hijas
                                        </span>
                                    </template>
                                    <template x-for="(hijo, idx) in est.hijos.slice(0, 3)" :key="hijo.id">
                                        <span class="px-2 py-1 rounded-full bg-white border border-gray-200 text-gray-600" x-text="hijo.nombre"></span>
                                    </template>
                                    <template x-if="est.hijos.length > 3">
                                        <span class="px-2 py-1 rounded-full bg-white border border-gray-200 text-gray-600">
                                            <span x-text="`+${est.hijos.length - 3}`"></span>
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <div x-show="filteredEstablishments().length === 0" class="col-span-full text-center text-gray-500 py-10">
                            No hay resultados con ese filtro.
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="drawerOpen" class="fixed inset-0 bg-black/30 backdrop-blur-sm flex justify-end z-50" x-transition>
                <div class="bg-white w-full max-w-md h-full shadow-xl p-6 flex flex-col" @click.away="drawerOpen = false">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Establecimiento</p>
                            <h3 class="text-lg font-semibold text-gray-800" x-text="selected?.nombre"></h3>
                            <p class="text-xs text-gray-500" x-text="selected?.slug"></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button"
                                class="rounded-lg border border-gray-300 px-2 py-1 text-xs text-gray-700 hover:bg-gray-50"
                                @click="toggleParentEdit()">
                                Editar
                            </button>
                            <form method="POST" :action="`/admin/locaciones/${selected?.id}`" @submit.prevent="confirmDeleteParent($event)">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg border border-red-200 px-2 py-1 text-xs text-red-600 hover:bg-red-50">
                                    Eliminar
                                </button>
                            </form>
                            <button type="button" class="text-gray-500 hover:text-gray-700" @click="drawerOpen = false">✕</button>
                        </div>
                    </div>

                    <div x-show="editingParent" x-transition class="mb-4 rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">Editar establecimiento</p>
                        <form method="POST" :action="`/admin/locaciones/${selected?.id}`" class="space-y-3">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="block text-xs uppercase tracking-wide text-gray-500 mb-1">Nombre</label>
                                <input type="text" name="nombre" x-model="parentEditName" @input="parentEditSlug = slugify(parentEditName)"
                                    required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700">
                            </div>
                            <div>
                                <label class="block text-xs uppercase tracking-wide text-gray-500 mb-1">Slug</label>
                                <input type="text" name="slug" x-model="parentEditSlug"
                                    required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700">
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="submit"
                                    class="flex-1 rounded-lg border border-[#6B8E23] px-4 py-2 text-sm text-[#6B8E23] hover:bg-[#F4F7EE]">
                                    Guardar cambios
                                </button>
                                <button type="button"
                                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                                    @click="editingParent = false">
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="mb-4 rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">Locaciones hijas</p>
                        <div class="space-y-2 max-h-52 overflow-y-auto pr-2">
                            <template x-if="selected && selected.hijos.length === 0">
                                <div class="text-sm text-gray-500">
                                    Aún no hay locaciones hijas. Crea la primera desde abajo.
                                </div>
                            </template>
                            <template x-for="hijo in (selected?.hijos || [])" :key="hijo.id">
                                <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm">
                                    <div>
                                        <p class="text-gray-700" x-text="hijo.nombre"></p>
                                        <p class="text-xs text-gray-500" x-text="hijo.slug"></p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button"
                                            class="text-xs text-[#6B8E23] hover:underline"
                                            @click="startEditChild(hijo)">
                                            Editar
                                        </button>
                                        <form method="POST" :action="`/admin/locaciones/${hijo.id}`" @submit.prevent="confirmDeleteChild($event)">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600 hover:underline">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div x-show="editingChildId" x-transition class="mb-4 rounded-xl border border-gray-200 p-4">
                        <p class="text-sm font-medium text-gray-700 mb-3">Editar locación hija</p>
                        <form method="POST" :action="`/admin/locaciones/${editingChildId}`" class="space-y-3">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="locacion_padre_id" :value="selected?.id">
                            <div>
                                <label class="block text-xs uppercase tracking-wide text-gray-500 mb-1">Nombre</label>
                                <input type="text" name="nombre" x-model="childEditName" @input="childEditSlug = slugify(childEditName)"
                                    required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700">
                            </div>
                            <div>
                                <label class="block text-xs uppercase tracking-wide text-gray-500 mb-1">Slug</label>
                                <input type="text" name="slug" x-model="childEditSlug"
                                    required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700">
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="submit"
                                    class="flex-1 rounded-lg border border-[#6B8E23] px-4 py-2 text-sm text-[#6B8E23] hover:bg-[#F4F7EE]">
                                    Guardar cambios
                                </button>
                                <button type="button"
                                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                                    @click="editingChildId = null">
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="mt-auto rounded-xl border border-gray-200 p-4">
                        <p class="text-sm font-medium text-gray-700 mb-3">Agregar locación hija</p>
                        <form method="POST" action="{{ route('admin.locaciones.store') }}" class="space-y-3">
                            @csrf
                            <input type="hidden" name="parent_id" :value="selected?.id">
                            <input type="hidden" name="locacion_padre_id" :value="selected?.id">
                            <div>
                                <label class="block text-xs uppercase tracking-wide text-gray-500 mb-1">Nombre</label>
                                <input type="text" name="nombre" x-model="childName" @input="childSlug = slugify(childName)"
                                    x-ref="childName"
                                    required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700">
                            </div>
                            <div>
                                <label class="block text-xs uppercase tracking-wide text-gray-500 mb-1">Slug</label>
                                <input type="text" name="slug" x-model="childSlug"
                                    required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700">
                            </div>
                            <button type="submit"
                                class="w-full rounded-lg border border-[#6B8E23] px-4 py-2 text-sm text-[#6B8E23] hover:bg-[#F4F7EE]">
                                Guardar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            function locacionesDrawer() {
                return {
                    query: '',
                    showCreate: false,
                    drawerOpen: false,
                    selected: null,
                    editingParent: false,
                    parentEditName: '',
                    parentEditSlug: '',
                    editingChildId: null,
                    childEditName: '',
                    childEditSlug: '',
                    childName: '',
                    childSlug: '',
                    createParentName: '',
                    createParentSlug: '',
                    establishments: @json($establecimientosPayload),
                    slugify(value) {
                        return (value || '')
                            .toLowerCase()
                            .normalize('NFD')
                            .replace(/[\u0300-\u036f]/g, '')
                            .replace(/ñ/g, 'n')
                            .replace(/[^a-z0-9\s-]/g, '')
                            .trim()
                            .replace(/\s+/g, '-');
                    },
                    filteredEstablishments() {
                        if (!this.query) {
                            return this.establishments;
                        }
                        const q = this.query.toLowerCase();
                        return this.establishments.filter((est) => {
                            return est.nombre.toLowerCase().includes(q) || (est.slug || '').toLowerCase().includes(q);
                        });
                    },
                    openDrawer(est, focusAdd = false) {
                        this.selected = est;
                        this.drawerOpen = true;
                        this.editingParent = false;
                        this.parentEditName = est.nombre || '';
                        this.parentEditSlug = est.slug || '';
                        this.editingChildId = null;
                        this.childEditName = '';
                        this.childEditSlug = '';
                        this.childName = '';
                        this.childSlug = '';
                        if (focusAdd) {
                            this.$nextTick(() => {
                                this.$refs.childName?.focus();
                            });
                        }
                    },
                    toggleParentEdit() {
                        this.editingParent = !this.editingParent;
                        if (this.selected) {
                            this.parentEditName = this.selected.nombre || '';
                            this.parentEditSlug = this.selected.slug || '';
                        }
                    },
                    startEditChild(hijo) {
                        this.editingChildId = hijo.id;
                        this.childEditName = hijo.nombre || '';
                        this.childEditSlug = hijo.slug || '';
                        this.$nextTick(() => {
                            this.$refs.childName?.focus();
                        });
                    },
                    confirmDeleteChild(event) {
                        if (confirm('¿Eliminar esta locación hija?')) {
                            event.target.submit();
                        }
                    },
                    confirmDeleteParent(event) {
                        if (confirm('¿Eliminar este establecimiento?')) {
                            event.target.submit();
                        }
                    },
                };
            }
        </script>
    @endpush
</x-layouts.clean>
