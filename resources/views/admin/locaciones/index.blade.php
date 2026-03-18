<x-layouts.clean>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @php
        $establecimientosPayload = $establecimientos->map(function ($est) {
            return [
                'id' => $est->id,
                'nombre' => $est->nombre,
                'slug' => $est->slug,
                'updated_at' => optional($est->updated_at)->format('Y-m-d H:i'),
                'allowed_domain_ids' => $est->allowedDomains->pluck('id')->values(),
                'hijos' => $est->hijos->map(function ($hijo) {
                    return [
                        'id' => $hijo->id,
                        'nombre' => $hijo->nombre,
                        'slug' => $hijo->slug,
                        'funcionarios' => $hijo->funcionarios->map(function ($funcionario) {
                            return [
                                'id' => $funcionario->id,
                                'name' => $funcionario->name,
                                'email' => $funcionario->email,
                            ];
                        })->values(),
                    ];
                })->values(),
            ];
        })->values();

        $funcionariosPayload = $funcionarios->map(function ($funcionario) {
            return [
                'id' => $funcionario->id,
                'name' => $funcionario->name,
                'email' => $funcionario->email,
                'locacion_ids' => $funcionario->locaciones->pluck('id')->values(),
            ];
        })->values();

        $domainsPayload = $domains->map(function ($domain) {
            return [
                'id' => $domain->id,
                'domain' => $domain->domain,
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
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <form action="{{ route('admin.dashboard') }}">
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
                                        class="rounded-lg border border-gray-300 px-3 py-2 text-xs text-gray-700 hover:bg-gray-50"
                                        @click="openDomainModal(est)">
                                        Dominios
                                    </button>
                                    <button
                                        type="button"
                                        class="rounded-lg border border-[#6B8E23] px-3 py-2 text-xs text-[#6B8E23] hover:bg-[#F4F7EE]"
                                        @click="openDrawer(est, true)">
                                        + Agregar hija
                                    </button>
                                </div>

                                <div class="text-xs text-gray-500">
                                    <template x-if="est.hijos.length === 0">
                                        <span>Sin locaciones hijas</span>
                                    </template>
                                    <template x-if="est.hijos.length > 0">
                                        <span x-text="`Locaciones hijas: ${est.hijos.length}`"></span>
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

                    <div class="flex-1 flex flex-col min-h-0 mb-4 rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">Locaciones hijas</p>
                        <div class="space-y-2 overflow-y-auto pr-2 flex-1">
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
                                        <span class="text-[10px] px-2 py-0.5 rounded-full border border-gray-200 text-gray-500"
                                            x-text="`${hijo.funcionarios?.length || 0} usuarios`"></span>
                                        <button type="button"
                                            class="text-xs text-blue-600 hover:underline"
                                            @click="openStaffManager(hijo)">
                                            Gestionar
                                        </button>
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

                    <div x-show="staffTarget" x-transition class="mb-4 rounded-xl border border-gray-200 p-4">
                        <p class="text-sm font-medium text-gray-700 mb-3">
                            Usuarios en <span class="font-semibold" x-text="staffTarget?.nombre"></span>
                        </p>
                        <div class="space-y-2 mb-3 max-h-32 overflow-y-auto pr-2">
                            <template x-if="staffTarget && (staffTarget.funcionarios?.length || 0) === 0">
                                <div class="text-sm text-gray-500">Sin usuarios asignados.</div>
                            </template>
                            <template x-for="funcionario in (staffTarget?.funcionarios || [])" :key="funcionario.id">
                                <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm">
                                    <div>
                                        <p class="text-gray-700" x-text="funcionario.name"></p>
                                        <p class="text-xs text-gray-500" x-text="funcionario.email"></p>
                                    </div>
                                    <form method="POST" :action="`/admin/locaciones/${staffTarget?.id}/funcionarios/${funcionario.id}`" @submit.prevent="removeStaff($event, funcionario)">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-600 hover:underline">
                                            Quitar
                                        </button>
                                    </form>
                                </div>
                            </template>
                        </div>

                        <form method="POST" :action="`/admin/locaciones/${staffTarget?.id}/funcionarios`" class="space-y-3" @submit.prevent="assignStaff($event)">
                            @csrf
                            <select name="user_id" data-enhanced-select x-ref="staffSelect" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700"
                                x-model="selectedStaffId" required>
                                <option value="">Selecciona usuario</option>
                                <template x-for="funcionario in availableStaff()" :key="funcionario.id">
                                    <option :value="funcionario.id" x-text="`${funcionario.name} (${funcionario.email})`"></option>
                                </template>
                            </select>
                            <div class="flex items-center gap-2">
                                <button type="submit"
                                    class="rounded-lg border border-[#6B8E23] px-3 py-2 text-sm text-[#6B8E23] hover:bg-[#F4F7EE]"
                                    :disabled="assigningStaff">
                                    <span x-text="assigningStaff ? 'Asignando...' : 'Asignar'"></span>
                                </button>
                                <button type="button"
                                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50"
                                    @click="staffTarget = null; selectedStaffId = ''">
                                    Cancelar
                                </button>
                            </div>
                            <div x-show="staffMessage" class="text-xs" :class="staffMessageType === 'error' ? 'text-red-600' : 'text-emerald-600'">
                                <span x-text="staffMessage"></span>
                            </div>
                        </form>
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

            <div x-show="domainModalOpen" x-cloak class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50" x-transition>
                <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6" @click.away="closeDomainModal()">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Dominios permitidos</p>
                            <p class="text-lg font-semibold text-gray-800" x-text="domainTarget?.nombre"></p>
                        </div>
                        <button type="button" class="text-gray-500 hover:text-gray-700" @click="closeDomainModal()">✕</button>
                    </div>

                    <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                        <template x-if="domains.length === 0">
                            <div class="text-sm text-gray-500">No hay dominios registrados.</div>
                        </template>
                        <template x-for="domain in domains" :key="domain.id">
                            <label class="flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700 cursor-pointer">
                                <span x-text="domain.domain"></span>
                                <input type="checkbox"
                                    class="h-4 w-4 accent-[#6B8E23]"
                                    :value="domain.id"
                                    x-model="selectedDomainIds">
                            </label>
                        </template>
                    </div>

                    <div class="mt-4 flex items-center gap-2">
                        <button type="button"
                            class="rounded-lg border border-[#6B8E23] px-4 py-2 text-sm text-[#6B8E23] hover:bg-[#F4F7EE]"
                            :disabled="domainSaving"
                            @click="saveDomains()">
                            <span x-text="domainSaving ? 'Guardando...' : 'Guardar'"></span>
                        </button>
                        <button type="button"
                            class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                            @click="closeDomainModal()">
                            Cancelar
                        </button>
                        <div x-show="domainMessage" class="text-xs" :class="domainMessageType === 'error' ? 'text-red-600' : 'text-emerald-600'">
                            <span x-text="domainMessage"></span>
                        </div>
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
                    staffTarget: null,
                    selectedStaffId: '',
                    assigningStaff: false,
                    staffMessage: '',
                    staffMessageType: 'success',
                    domainModalOpen: false,
                    domainTarget: null,
                    selectedDomainIds: [],
                    domainSaving: false,
                    domainMessage: '',
                    domainMessageType: 'success',
                    childName: '',
                    childSlug: '',
                    createParentName: '',
                    createParentSlug: '',
                    establishments: @json($establecimientosPayload),
                    funcionarios: @json($funcionariosPayload),
                    domains: @json($domainsPayload),
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
                        this.staffTarget = null;
                        this.selectedStaffId = '';
                        this.childName = '';
                        this.childSlug = '';
                        if (focusAdd) {
                            this.$nextTick(() => {
                                this.$refs.childName?.focus();
                            });
                        }
                    },
                    openDomainModal(est) {
                        this.domainTarget = est;
                        this.selectedDomainIds = (est.allowed_domain_ids || []).map((id) => Number(id));
                        this.domainMessage = '';
                        this.domainModalOpen = true;
                    },
                    closeDomainModal() {
                        this.domainModalOpen = false;
                        this.domainTarget = null;
                        this.selectedDomainIds = [];
                        this.domainMessage = '';
                    },
                    async saveDomains() {
                        if (!this.domainTarget || this.domainSaving) return;
                        this.domainSaving = true;
                        this.domainMessage = '';
                        const url = `/admin/locaciones/${this.domainTarget.id}/domains`;

                        try {
                            const response = await fetch(url, {
                                method: 'PUT',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({
                                    domain_ids: this.selectedDomainIds,
                                }),
                            });

                            if (!response.ok) {
                                const errorData = await response.json().catch(() => null);
                                this.domainMessageType = 'error';
                                this.domainMessage = errorData?.message || 'No se pudo guardar la configuración.';
                                return;
                            }

                            const data = await response.json();
                            if (this.domainTarget) {
                                this.domainTarget.allowed_domain_ids = data.domain_ids || [];
                            }
                            this.domainMessageType = 'success';
                            this.domainMessage = data.message || 'Dominios actualizados.';
                        } finally {
                            this.domainSaving = false;
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
                    openStaffManager(hijo) {
                        this.staffTarget = hijo;
                        this.selectedStaffId = '';
                        this.staffMessage = '';
                        this.$nextTick(() => {
                            this.refreshStaffSelect();
                        });
                    },
                    availableStaff() {
                        if (!this.staffTarget) return [];
                        return this.funcionarios.filter((f) => {
                            const ids = f.locacion_ids || [];
                            return !ids.includes(this.staffTarget.id);
                        });
                    },
                    refreshStaffSelect() {
                        const el = this.$refs.staffSelect;
                        if (!el || !window.$) return;
                        const $el = window.$(el);
                        if ($el.data('select2')) {
                            $el.select2('destroy');
                        }
                        $el.select2({
                            width: '100%',
                            placeholder: 'Seleccione...',
                            allowClear: true,
                        });
                        $el.off('change.sitin').on('change.sitin', () => {
                            this.selectedStaffId = el.value;
                        });
                    },
                    async assignStaff() {
                        if (!this.staffTarget || !this.selectedStaffId || this.assigningStaff) return;
                        this.assigningStaff = true;
                        this.staffMessage = '';
                        const url = `/admin/locaciones/${this.staffTarget.id}/funcionarios`;
                        const body = new URLSearchParams();
                        body.append('user_id', this.selectedStaffId);

                        try {
                            const response = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: body.toString(),
                            });

                            if (!response.ok) {
                                const errorData = await response.json().catch(() => null);
                                this.staffMessageType = 'error';
                                this.staffMessage = errorData?.message || 'No se pudo asignar el funcionario.';
                                return;
                            }

                            const data = await response.json();
                            const assigned = data.user;
                            if (assigned && this.staffTarget) {
                                const func = this.funcionarios.find((f) => f.id === assigned.id);
                                if (func) {
                                    func.locacion_ids = func.locacion_ids || [];
                                    if (!func.locacion_ids.includes(this.staffTarget.id)) {
                                        func.locacion_ids.push(this.staffTarget.id);
                                    }
                                }
                                this.staffTarget.funcionarios = this.staffTarget.funcionarios || [];
                                const exists = this.staffTarget.funcionarios.find((f) => f.id === assigned.id);
                                if (!exists) {
                                    this.staffTarget.funcionarios.push({
                                        id: assigned.id,
                                        name: assigned.name,
                                        email: assigned.email,
                                    });
                                }
                                this.selectedStaffId = '';
                                if (this.$refs.staffSelect && window.$) {
                                    window.$(this.$refs.staffSelect).val('').trigger('change');
                                    this.refreshStaffSelect();
                                }
                                this.staffMessageType = 'success';
                                this.staffMessage = data.message || 'Usuario asignado.';
                            }
                        } finally {
                            this.assigningStaff = false;
                        }
                    },
                    async removeStaff(event, funcionario) {
                        if (!this.staffTarget || !funcionario) return;
                        if (!confirm('¿Quitar usuario de esta locación?')) return;

                        const url = `/admin/locaciones/${this.staffTarget.id}/funcionarios/${funcionario.id}`;
                        const response = await fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                        });

                        if (!response.ok) {
                            const errorData = await response.json().catch(() => null);
                            this.staffMessageType = 'error';
                            this.staffMessage = errorData?.message || 'No se pudo quitar el funcionario.';
                            return;
                        }

                        if (this.staffTarget) {
                            this.staffTarget.funcionarios = (this.staffTarget.funcionarios || []).filter((f) => f.id !== funcionario.id);
                        }
                        const func = this.funcionarios.find((f) => f.id === funcionario.id);
                        if (func) {
                            func.locacion_ids = (func.locacion_ids || []).filter((id) => id !== this.staffTarget.id);
                        }
                        this.staffMessageType = 'success';
                        this.staffMessage = 'Usuario quitado.';
                        this.refreshStaffSelect();
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
