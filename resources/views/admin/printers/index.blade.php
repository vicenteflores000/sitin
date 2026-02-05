<x-layouts.clean>
    @php
    $apiBaseUrl = config('services.printers_api.base_url', 'http://127.0.0.1:8000/api/v1');
    @endphp

    <div class="w-full h-screen flex flex-col items-center px-4 bg-[#FAFAF7] overflow-hidden">
        <div x-data="printerPage('{{ $apiBaseUrl }}')" x-init="loadPrinters()"
            class="py-8 flex flex-col justify-center" style="height: calc(100vh - 2rem); width: 70%;">

            <div class="mb-6 text-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Tickets TI" class="mx-auto h-12"
                    style="width: 200px; height: auto;">
                <p class="text-gray-600">Administra impresoras y registro por IP.</p>
            </div>

            <div class="bg-white rounded-xl shadow-xl border border-gray-200 p-6 flex flex-col"
                style="height: 570px; min-height: 570px; max-height: 570px;">

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
                            @click="openModal()">
                            + Agregar impresora
                        </button>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto" style="max-height: calc(570px - 2rem);">
                    <div class="space-y-6 pr-2">
                        <template x-if="loading">
                            <div class="text-center text-gray-500 py-6">Cargando impresoras...</div>
                        </template>

                        <div x-show="!loading">
                            <template x-if="!loading && error">
                                <div class="text-center text-red-600 text-sm py-6" x-text="error"></div>
                            </template>

                            <template x-if="!loading && !error && printers.length === 0">
                                <div class="text-center text-gray-500 py-6">
                                    No hay impresoras registradas
                                </div>
                            </template>

                            <template x-for="printer in printers" :key="printer.id || printer.serial_number || printer.ip">
                            <div class="border rounded-lg p-4 bg-gray-50 mb-4">
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="min-w-0">
                                            <div class="font-medium text-gray-800 truncate">
                                                <span x-text="printerDisplayName(printer)"></span>
                                                <span class="text-xs text-gray-400 ml-2" x-text="printerIp(printer)"></span>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-3 flex-shrink-0">
                                            <button
                                                type="button"
                                                @click.prevent="openConsumables(printer)"
                                                x-show="isReachable(printer)"
                                                class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-50">
                                                Ver consumibles
                                            </button>

                                            <span
                                                class="w-3 h-3 rounded-full inline-block"
                                                :class="printerStatusClass(printer)"
                                                :title="printerStatusLabel(printer)">
                                            </span>
                                            <span class="text-xs text-gray-500" x-text="printerStatusLabel(printer)"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="showModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg w-96 p-6"
                    @click.away="closeModal()">
                    <h2 class="text-lg font-semibold mb-4">Agregar impresora</h2>

                    <form @submit.prevent="submitPrinter">
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    IP
                                </label>
                                <input
                                    type="text"
                                    required
                                    x-model.trim="form.ip"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Community
                                </label>
                                <input
                                    type="text"
                                    x-model.trim="form.community"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Versión SNMP
                                </label>
                                <select
                                    x-model="form.snmp_version"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="1">1</option>
                                    <option value="2c">2c</option>
                                    <option value="3">3</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Ubicación
                                </label>
                                <input
                                    type="text"
                                    x-model.trim="form.location"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Notas
                                </label>
                                <textarea
                                    rows="2"
                                    x-model.trim="form.notes"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-between">
                            <p class="text-xs text-red-600" x-text="formError"></p>
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    @click="closeModal()"
                                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    :disabled="submitting"
                                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700 transition disabled:opacity-60">
                                    <span x-text="submitting ? 'Guardando...' : 'Registrar'"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div x-show="showConsumablesModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg w-[30rem] p-6"
                    @click.away="closeConsumablesModal()">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold">Consumibles</h2>
                        <button
                            @click="closeConsumablesModal()"
                            class="text-gray-500 hover:text-gray-700">
                            ✕
                        </button>
                    </div>

                    <template x-if="consumablesLoading">
                        <div class="text-center text-gray-500 py-6">Cargando consumibles...</div>
                    </template>

                    <template x-if="!consumablesLoading && consumablesError">
                        <div class="text-center text-red-600 text-sm py-6" x-text="consumablesError"></div>
                    </template>

                    <template x-if="!consumablesLoading && !consumablesError && consumables.length === 0">
                        <div class="text-center text-gray-500 py-6">No hay consumibles disponibles</div>
                    </template>

                    <div x-show="!consumablesLoading && !consumablesError && normalizedConsumables().length">
                        <div class="grid gap-4" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
                            <template x-for="item in normalizedConsumables()" :key="item.__key">
                                <div class="rounded-lg border border-gray-200 px-3 py-3 text-sm">
                                    <div class="text-gray-700 font-medium truncate" x-text="consumableName(item)"></div>
                                    <div class="mt-2 h-2 rounded-full" :style="consumableBarStyle(item)"></div>
                                    <div class="mt-2 text-xs text-gray-500">
                                        <span x-text="consumableStatus(item)"></span>
                                        <span x-text="consumableLevel(item) !== '—' ? ` · ${consumableLevel(item)}` : ''"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function printerPage(baseUrl) {
            return {
                baseUrl,
                printers: [],
                loading: false,
                error: '',
                showModal: false,
                submitting: false,
                formError: '',
                showConsumablesModal: false,
                consumablesLoading: false,
                consumablesError: '',
                consumables: [],
                form: {
                    ip: '',
                    community: 'public',
                    snmp_version: '2c',
                    location: '',
                    notes: ''
                },
                async loadPrinters() {
                    this.loading = true;
                    this.error = '';

                    try {
                        const response = await fetch(`${this.baseUrl}/printers`);
                        if (!response.ok) {
                            throw new Error('No se pudo obtener la lista de impresoras.');
                        }
                        const data = await response.json();
                        this.printers = Array.isArray(data) ? data : (data.data || []);
                        await this.refreshReachableStatuses();
                    } catch (error) {
                        this.error = error.message || 'No se pudo obtener la lista de impresoras.';
                    } finally {
                        this.loading = false;
                    }
                },
                async refreshReachableStatuses() {
                    const updated = await Promise.all(this.printers.map(async (printer) => {
                        const ip = this.printerIp(printer);
                        if (!ip) {
                            return { ...printer, __reachable: false };
                        }

                        const community = printer?.community || 'public';
                        const version = printer?.snmp_version || '2c';
                        try {
                            const params = new URLSearchParams({
                                ip,
                                community,
                                version
                            });
                            const response = await fetch(`${this.baseUrl}/snmp/reachable?${params.toString()}`);
                            if (!response.ok) {
                                return { ...printer, __reachable: false };
                            }
                            const result = await response.json();
                            const reachable = result?.reachable === true || result === true;
                            return { ...printer, __reachable: reachable };
                        } catch (error) {
                            return { ...printer, __reachable: false };
                        }
                    }));

                    this.printers = updated;
                },
                openModal() {
                    this.formError = '';
                    this.submitting = false;
                    this.showModal = true;
                },
                closeModal() {
                    this.showModal = false;
                    this.resetForm();
                },
                resetForm() {
                    this.form = {
                        ip: '',
                        community: 'public',
                        snmp_version: '2c',
                        location: '',
                        notes: ''
                    };
                },
                async submitPrinter() {
                    if (!this.form.ip) {
                        this.formError = 'La IP es obligatoria.';
                        return;
                    }

                    this.formError = '';
                    this.submitting = true;

                    try {
                        const response = await fetch(`${this.baseUrl}/printers`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(this.form)
                        });

                        if (!response.ok) {
                            throw new Error('No se pudo registrar la impresora.');
                        }

                        await response.json();
                        this.closeModal();
                        await this.loadPrinters();
                    } catch (error) {
                        this.formError = error.message || 'No se pudo registrar la impresora.';
                    } finally {
                        this.submitting = false;
                    }
                },
                printerDisplayName(printer) {
                    const brand = printer?.brand || printer?.marca || '';
                    const rawModel = printer?.model || printer?.modelo || printer?.name || '';
                    const model = String(rawModel).replace(/string:/gi, '').trim();
                    const location = printer?.location || printer?.ubicacion || '';
                    const base = [brand, model].filter(Boolean).join(' - ') || 'Impresora';
                    return location ? `${base} · ${location}` : base;
                },
                printerIp(printer) {
                    return printer?.ip || printer?.ip_address || printer?.ipAddress || '';
                },
                printerStatusLabel(printer) {
                    return this.isReachable(printer) ? 'Activa' : 'Inalcanzable';
                },
                printerStatusClass(printer) {
                    return this.isReachable(printer) ? 'bg-[#6B8E23]' : 'bg-red-400';
                },
                isReachable(printer) {
                    if (printer?.__reachable === undefined || printer?.__reachable === null) {
                        return printer?.reachable !== false;
                    }
                    return printer.__reachable === true;
                },
                printerIdentifier(printer) {
                    return printer?.id || printer?.printer_id || printer?.uuid || printer?.serial_number || printer?.serial || printer?.ip;
                },
                async openConsumables(printer) {
                    if (!this.isReachable(printer)) {
                        return;
                    }
                    const identifier = this.printerIdentifier(printer);
                    if (!identifier) {
                        this.consumablesError = 'No se pudo identificar la impresora.';
                        this.showConsumablesModal = true;
                        return;
                    }

                    this.consumables = [];
                    this.consumablesError = '';
                    this.consumablesLoading = true;
                    this.showConsumablesModal = true;

                    try {
                        const response = await fetch(`${this.baseUrl}/printers/${encodeURIComponent(identifier)}/snmp/consumables`);
                        if (!response.ok) {
                            throw new Error('No se pudieron obtener los consumibles.');
                        }
                        const data = await response.json();
                        const list =
                            (Array.isArray(data?.data?.consumables) && data.data.consumables) ||
                            (Array.isArray(data?.consumables) && data.consumables) ||
                            (Array.isArray(data?.data?.items) && data.data.items) ||
                            (Array.isArray(data?.items) && data.items) ||
                            (Array.isArray(data) && data) ||
                            [];
                        this.consumables = list;
                    } catch (error) {
                        this.consumablesError = error.message || 'No se pudieron obtener los consumibles.';
                    } finally {
                        this.consumablesLoading = false;
                    }
                },
                closeConsumablesModal() {
                    this.showConsumablesModal = false;
                },
                normalizedConsumables() {
                    const source = this.consumables;
                    const list = Array.isArray(source) ? source : [];
                    return list
                        .filter(Boolean)
                        .map((item, index) => ({
                            ...item,
                            __key: item?.id || item?.index || item?.serial || `${item?.type || 'item'}-${index}`
                        }))
                        .filter((item) => this.consumableKind(item) !== 'waste');
                },
                consumableName(item) {
                    const raw = item?.raw_description || item?.description || item?.rawDescription || item?.name || item?.consumable || item?.type;
                    if (!raw) {
                        if (item?.color) return `Toner ${String(item.color).toUpperCase()}`;
                        return 'Consumible';
                    }
                    return String(raw).replace(/^"+|"+$/g, '');
                },
                consumableStatus(item) {
                    return item?.state || item?.status || item?.condition || 'Sin datos';
                },
                consumableLevel(item) {
                    if (item?.percent !== null && item?.percent !== undefined) return `${item.percent}%`;
                    if (item?.current !== undefined && item?.capacity) {
                        const max = Number(item.capacity);
                        const level = Number(item.current);
                        if (Number.isFinite(max) && max > 0 && Number.isFinite(level) && level >= 0) {
                            return `${Math.round((level / max) * 100)}%`;
                        }
                    }
                    if (item?.raw_level !== undefined && item?.raw_max) {
                        const max = Number(item.raw_max);
                        const level = Number(item.raw_level);
                        if (Number.isFinite(max) && max > 0 && Number.isFinite(level) && level >= 0) {
                            return `${Math.round((level / max) * 100)}%`;
                        }
                    }
                    if (item?.level !== undefined) return `${item.level}%`;
                    if (item?.remaining !== undefined) return `${item.remaining}%`;
                    if (item?.percent !== undefined) return `${item.percent}%`;
                    return 'N/D';
                },
                consumableKind(item) {
                    const label = `${item?.type || ''} ${item?.raw_description || ''} ${item?.description || ''} ${item?.name || ''} ${item?.consumable || ''} ${item?.color || ''}`.toLowerCase();
                    if (label.includes('waste') || label.includes('residuo')) return 'waste';
                    if (label.includes('drum') || label.includes('tambor')) return 'drum';
                    if (label.includes('unidad de imagen') || label.includes('image unit')) return 'image';
                    if (label.includes('toner') || label.includes('tóner')) return 'toner';
                    if (item?.color) return 'toner';
                    return 'other';
                },
                consumableTone(item) {
                    const label = `${item?.type || ''} ${item?.raw_description || ''} ${item?.description || ''} ${item?.name || ''} ${item?.consumable || ''} ${item?.color || ''}`.toLowerCase();
                    if (label.includes('black') || label.includes('negro')) return 'black';
                    if (label.includes('cyan') || label.includes('cian')) return 'cyan';
                    if (label.includes('magenta')) return 'magenta';
                    if (label.includes('yellow') || label.includes('amarillo')) return 'yellow';
                    return 'gray';
                },
                consumableBarStyle(item) {
                    const kind = this.consumableKind(item);
                    if (kind === 'image') {
                        return 'background: #6B8E23;';
                    }
                    if (kind === 'drum') {
                        return 'background: repeating-linear-gradient(45deg, #111 0 6px, #ffffff 6px 8px);';
                    }
                    if (kind === 'toner') {
                        const tone = this.consumableTone(item);
                        const colors = {
                            black: '#111827',
                            cyan: '#06b6d4',
                            magenta: '#db2777',
                            yellow: '#facc15',
                            gray: '#9ca3af'
                        };
                        return `background: ${colors[tone] || colors.gray};`;
                    }
                    return 'background: #9ca3af;';
                },
            };
        }
    </script>
</x-layouts.clean>
