<x-layouts.clean>
    <div class="w-full h-screen flex flex-col items-center px-4 bg-[#FAFAF7] overflow-hidden">
        <div class="w-full max-w-5xl py-8 flex flex-col justify-center" style="height: calc(100vh - 2rem);">

            <div class="mb-6 text-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Tickets TI" class="mx-auto h-12" style="width: 200px; height: auto;">
                <p class="text-gray-600">Gestión técnica de tickets</p>
            </div>

            <div class="bg-white rounded-xl shadow-xl border border-gray-200 p-6 flex flex-col"
                style="height: 620px; min-height: 620px; max-height: 620px;">

                <div class="flex items-center justify-between mb-4 gap-3">
                    <form action="{{ route('dashboard') }}">
                        <button
                            class="mb-3 rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                            type="submit">
                            &larr; Volver
                        </button>
                    </form>
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
                            $hasAssignment = (bool) $ticket->currentAssignment;
                            $canManage = $hasAssignment && $ticket->currentAssignment->technician_id === auth()->id();
                            $classificationComplete = ($ticket->categoria_interna && $ticket->problem_type && $ticket->root_cause);
                            $actionsCount = $ticket->actions->count();
                            $isResolved = in_array($status, ['resuelto', 'cerrado'], true);
                        @endphp
                        <div x-data="{ open: false, tab: 'antecedentes', canResolve: {{ ($classificationComplete && $actionsCount > 0 && $canManage) ? 'true' : 'false' }}, showReassign: false }" class="group border rounded-lg bg-gray-50 cursor-pointer {{ $isResolved ? 'px-3 py-2 text-[11px] text-gray-500' : 'p-4' }}" @click="open = true" role="button" tabindex="0">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="{{ $isResolved ? 'font-medium text-gray-500' : 'font-medium text-gray-800' }}">#{{ $ticket->id }} · {{ $ticket->categoria }}</div>
                                    @if($isResolved)
                                        <div class="text-[11px] text-gray-500">{{ $ticket->usuario_mail }}</div>
                                    @else
                                        <div class="text-sm text-gray-600">{{ $ticket->usuario_mail }}</div>
                                        <div class="text-xs text-gray-400">Ubicación: {{ $locacionLabel }}</div>
                                        <div class="mt-2 text-sm text-gray-700">{{ $ticket->descripcion }}</div>
                                    @endif
                                </div>

                                <div class="text-xs text-gray-500 text-right">
                                    <div>Estado: <span class="{{ $isResolved ? 'font-medium text-gray-500' : 'font-medium text-gray-700' }}">{{ $status }}</span></div>
                                    @if(!$isResolved)
                                        <div>Asignado: {{ $ticket->currentAssignment?->technician?->name ?? '—' }}</div>
                                        <div>{{ $ticket->created_at->format('d-m-Y H:i') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="{{ $isResolved ? 'mt-1' : 'mt-3' }} text-[10px] text-gray-400 opacity-0 group-hover:opacity-100 transition">
                                Clic para ver las acciones
                            </div>

                            <div x-show="open" class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
                                <div class="bg-white rounded-xl shadow-lg w-full max-w-5xl p-0 overflow-hidden" @click.outside="open = false">
                                    <div class="flex flex-col gap-3 px-6 pt-6 pb-4 border-b border-gray-100">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-lg font-semibold">Gestionar ticket #{{ $ticket->id }}</h3>
                                            <button type="button" @click="open = false" class="text-gray-500 hover:text-gray-700">✕</button>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
                                            @if($ticket->currentAssignment)
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <div class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-700">
                                                        Asignado a: <span class="font-medium">{{ $ticket->currentAssignment?->technician?->name ?? '—' }}</span>
                                                    </div>
                                                    <button type="button"
                                                        @click="showReassign = !showReassign"
                                                        class="text-xs text-gray-500 hover:text-gray-700 underline">
                                                        Reasignar
                                                    </button>
                                                </div>
                                                <form x-show="showReassign" method="POST" action="{{ route('admin.tickets.assign-user', $ticket) }}" class="flex flex-wrap items-center gap-2">
                                                    @csrf
                                                    <span class="text-xs text-gray-500">Asignar a:</span>
                                                    <select name="technician_id" required class="rounded-lg border border-gray-300 px-3 py-2 text-xs text-gray-700">
                                                        <option value="">Selecciona técnico</option>
                                                        @foreach($admins as $adminUser)
                                                            <option value="{{ $adminUser->id }}">{{ $adminUser->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <button type="submit"
                                                        class="rounded-lg border border-[#6B8E23] px-3 py-2 text-xs text-[#6B8E23] hover:bg-[#F4F7EE]">
                                                        Asignar
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.tickets.assign-user', $ticket) }}" class="flex flex-wrap items-center gap-2">
                                                    @csrf
                                                    <span class="text-xs text-gray-500">Asignar a:</span>
                                                    <select name="technician_id" required class="rounded-lg border border-gray-300 px-3 py-2 text-xs text-gray-700">
                                                        <option value="">Selecciona técnico</option>
                                                        @foreach($admins as $adminUser)
                                                            <option value="{{ $adminUser->id }}">{{ $adminUser->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <button type="submit"
                                                        class="rounded-lg border border-[#6B8E23] px-3 py-2 text-xs text-[#6B8E23] hover:bg-[#F4F7EE]">
                                                        Asignar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-[220px_1fr] gap-0">
                                        <div class="bg-gray-50 border-r border-gray-200 p-4">
                                            <div class="text-[11px] uppercase tracking-wide text-gray-400 mb-3">Secciones</div>
                                            <div class="space-y-2 text-sm text-gray-700">
                                                <button type="button"
                                                    @click="tab = 'antecedentes'"
                                                    :class="tab === 'antecedentes' ? 'bg-white border-gray-200 shadow-sm' : 'bg-transparent border-transparent'"
                                                    class="w-full text-left rounded-lg border px-3 py-2 transition">
                                                    Antecedentes
                                                </button>
                                                <button type="button"
                                                    @click="tab = 'acciones'"
                                                    :class="tab === 'acciones' ? 'bg-white border-gray-200 shadow-sm' : 'bg-transparent border-transparent'"
                                                    class="w-full text-left rounded-lg border px-3 py-2 transition">
                                                    Acciones
                                                </button>
                                                <button type="button"
                                                    @click="tab = 'clasificacion'"
                                                    :class="tab === 'clasificacion' ? 'bg-white border-gray-200 shadow-sm' : 'bg-transparent border-transparent'"
                                                    class="w-full text-left rounded-lg border px-3 py-2 transition">
                                                    Clasificación
                                                </button>
                                                <button type="button"
                                                    :disabled="!canResolve"
                                                    @click="canResolve ? (tab = 'resolucion') : null"
                                                    :class="tab === 'resolucion' ? 'bg-white border-gray-200 shadow-sm' : 'bg-transparent border-transparent'"
                                                    class="w-full text-left rounded-lg border px-3 py-2 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                                    Resolución
                                                </button>
                                                <div x-show="!canResolve" class="text-[11px] text-gray-400">
                                                    Completa acciones y clasificación para habilitar resolución.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="p-6">
                                            <div x-show="tab === 'antecedentes'">
                                                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                                    <div class="text-[11px] uppercase tracking-wide text-gray-400">Antecedentes entregados por usuario</div>

                                                    <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                                                        <div>
                                                            <div class="text-xs text-gray-400">Usuario</div>
                                                            <div>{{ $ticket->usuario_mail }}</div>
                                                        </div>
                                                        <div>
                                                            <div class="text-xs text-gray-400">Ubicación</div>
                                                            <div>{{ $locacionLabel }}</div>
                                                        </div>
                                                        <div>
                                                            <div class="text-xs text-gray-400">Categoría</div>
                                                            <div>{{ $ticket->categoria }}</div>
                                                        </div>
                                                        <div>
                                                            <div class="text-xs text-gray-400">Tipo</div>
                                                            <div>{{ $ticket->tipo }}</div>
                                                        </div>
                                                        <div>
                                                            <div class="text-xs text-gray-400">Impacto laboral</div>
                                                            <div>{{ $ticket->impacto }}</div>
                                                        </div>
                                                    </div>

                                                    <div class="mt-4">
                                                        <div class="text-xs text-gray-400">Descripción</div>
                                                        <div class="mt-1 text-sm text-gray-700">{{ $ticket->descripcion }}</div>
                                                    </div>

                                                    @if ($ticket->attachments->count() > 0)
                                                        <div class="mt-4">
                                                            <div class="text-xs text-gray-400">Adjuntos</div>
                                                            <div class="mt-2 flex flex-wrap gap-2">
                                                                @foreach ($ticket->attachments as $attachment)
                                                                    @php
                                                                        $attachmentUrl = Storage::disk('public')->url($attachment->path);
                                                                        $isImage = str_starts_with($attachment->mime_type ?? '', 'image/');
                                                                    @endphp
                                                                    <button type="button"
                                                                        class="admin-attachment-thumb h-[10px] w-[10px] rounded-sm border border-gray-300 bg-white overflow-hidden"
                                                                        data-url="{{ $attachmentUrl }}"
                                                                        data-name="{{ $attachment->original_name }}"
                                                                        data-mime="{{ $attachment->mime_type }}">
                                                                        @if ($isImage)
                                                                            <img src="{{ $attachmentUrl }}" alt="{{ $attachment->original_name }}" class="h-full w-full object-cover">
                                                                        @else
                                                                            <span class="block text-[6px] leading-[10px] text-gray-500 text-center">PDF</span>
                                                                        @endif
                                                                    </button>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-700">
                                                    <div>
                                                        <div class="text-xs text-gray-400">Estado</div>
                                                        <div>{{ $status }}</div>
                                                    </div>
                                                    <div>
                                                        <div class="text-xs text-gray-400">Asignado</div>
                                                        <div>{{ $ticket->currentAssignment?->technician?->name ?? '—' }}</div>
                                                    </div>
                                                    <div>
                                                        <div class="text-xs text-gray-400">Creado</div>
                                                        <div>{{ $ticket->created_at->format('d-m-Y H:i') }}</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div x-show="tab === 'acciones'">
                                                @if(! $hasAssignment)
                                                    <div class="mb-4 rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-xs text-yellow-800">
                                                        Asigna el ticket a un técnico para poder registrar acciones.
                                                    </div>
                                                @endif
                                                <div class="flex items-center justify-between mb-3">
                                                    <div class="text-sm font-medium text-gray-700">Acciones registradas</div>
                                                    @unless($canManage)
                                                        <div class="text-xs text-gray-400">Solo el técnico asignado puede editar.</div>
                                                    @endunless
                                                </div>
                                                <div class="mb-3 text-xs text-gray-500">
                                                    Registra lo que necesitas hacer: repuestos, instalaciones, compras u otras tareas.
                                                </div>

                                                <div class="space-y-3">
                                                    @forelse($ticket->actions as $action)
                                                        <div class="rounded-lg border border-gray-200 bg-white p-3 text-sm text-gray-700">
                                                            <div class="flex items-center justify-between">
                                                                <div class="font-medium capitalize">
                                                                    {{ str_replace('_', ' ', $action->action_type) }}
                                                                </div>
                                                                <div class="text-xs text-gray-400">
                                                                    {{ $action->created_at->format('d-m-Y H:i') }}
                                                                </div>
                                                            </div>
                                                            <div class="mt-1 text-sm text-gray-600">{{ $action->description }}</div>
                                                            <div class="mt-2 text-xs text-gray-500">
                                                                Estado: <span class="font-medium text-gray-700">{{ str_replace('_', ' ', $action->status) }}</span>
                                                                @if($action->creator)
                                                                    · {{ $action->creator->name }}
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <div class="rounded-lg border border-dashed border-gray-300 p-4 text-sm text-gray-500 bg-gray-50">
                                                            Aún no hay acciones registradas.
                                                        </div>
                                                    @endforelse
                                                </div>

                                                <div class="mt-4 rounded-lg border border-gray-200 bg-gray-50 p-4">
                                                    <div class="text-xs uppercase tracking-wide text-gray-400">Agregar acción</div>
                                                    <form method="POST" action="{{ route('admin.tickets.actions', $ticket) }}" class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                                                        @csrf
                                                        <select name="action_type" required class="rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700" @disabled(!$canManage)>
                                                            <option value="repuesto">Repuesto</option>
                                                            <option value="instalacion">Instalación</option>
                                                            <option value="compra">Compra</option>
                                                            <option value="otro">Otro</option>
                                                        </select>
                                                        <select name="status" required class="rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700" @disabled(!$canManage)>
                                                            <option value="pendiente">Pendiente</option>
                                                            <option value="en_progreso">En progreso</option>
                                                            <option value="completado">Completado</option>
                                                        </select>
                                                        <textarea name="description" rows="2" placeholder="Describe la acción o tarea" required
                                                            class="md:col-span-2 rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700" @disabled(!$canManage)></textarea>
                                                        <div class="md:col-span-2 flex justify-end">
                                                            <button type="submit"
                                                                class="rounded-lg border border-[#6B8E23] px-3 py-2 text-xs text-[#6B8E23] hover:bg-[#F4F7EE] disabled:opacity-50"
                                                                @disabled(!$canManage)>
                                                                Guardar acción
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>

                                            <div x-show="tab === 'clasificacion'">
                                                @if(! $hasAssignment)
                                                    <div class="mb-4 rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-xs text-yellow-800">
                                                        Asigna el ticket a un técnico para poder clasificar.
                                                    </div>
                                                @endif
                                                <div class="flex items-center justify-between mb-3">
                                                    <div class="text-sm font-medium text-gray-700">Clasificación técnica</div>
                                                    @unless($canManage)
                                                        <div class="text-xs text-gray-400">Solo el técnico asignado puede editar.</div>
                                                    @endunless
                                                </div>

                                                <form method="POST" action="{{ route('admin.tickets.classification', $ticket) }}" class="space-y-3">
                                                    @csrf
                                                    <input type="text" name="categoria_interna" placeholder="Categoría interna" required
                                                        value="{{ old('categoria_interna', $ticket->categoria_interna) }}"
                                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700" @disabled(!$canManage)>
                                                    <input type="text" name="problem_type" placeholder="Tipo de problema" required
                                                        value="{{ old('problem_type', $ticket->problem_type) }}"
                                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700" @disabled(!$canManage)>
                                                    <input type="text" name="root_cause" placeholder="Causa raíz" required
                                                        value="{{ old('root_cause', $ticket->root_cause) }}"
                                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700" @disabled(!$canManage)>
                                                    <div class="flex justify-end">
                                                        <button type="submit"
                                                            class="rounded-lg border border-gray-300 px-3 py-2 text-xs text-gray-700 hover:bg-gray-50 disabled:opacity-50"
                                                            @disabled(!$canManage)>
                                                            Guardar clasificación
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>

                                            <div x-show="tab === 'resolucion'">
                                                <div class="text-sm font-medium text-gray-700 mb-3">Resolución</div>
                                                @if(! $hasAssignment)
                                                    <div class="mb-4 rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-xs text-yellow-800">
                                                        Asigna el ticket a un técnico para completar la resolución.
                                                    </div>
                                                @endif
                                                @if($actionsCount === 0)
                                                    <div class="mb-4 rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-xs text-yellow-800">
                                                        Registra al menos una acción antes de completar la resolución.
                                                    </div>
                                                @endif
                                                @if(! $classificationComplete)
                                                    <div class="mb-4 rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-xs text-yellow-800">
                                                        Completa la clasificación técnica antes de cerrar el ticket.
                                                    </div>
                                                @endif
                                                <form method="POST" action="{{ route('admin.tickets.resolve', $ticket) }}" class="space-y-3">
                                                    @csrf
                                                    <textarea name="resolution_text" rows="4" placeholder="Resumen de resolución" required
                                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700" @disabled(!$canManage)>{{ $ticket->resolution?->resolution_text }}</textarea>
                                                    <div class="flex justify-end">
                                                        <button type="submit"
                                                            class="rounded-lg border border-[#6B8E23] px-3 py-2 text-xs text-[#6B8E23] hover:bg-[#F4F7EE] disabled:opacity-50"
                                                            @disabled(!$canManage)>
                                                            Completar ticket
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-gray-500 py-6">No hay tickets registrados</div>
                        @endforelse
                    </div>
                </div>
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
                const viewer = document.getElementById('admin-attachment-viewer');
                const stage = document.getElementById('admin-attachment-stage');
                const caption = document.getElementById('admin-attachment-caption');
                const closeBtn = document.getElementById('admin-attachment-close');

                function openViewer({ url, name, mime }) {
                    if (!viewer || !stage) return;
                    const isImage = mime && mime.startsWith('image/');
                    if (isImage) {
                        stage.innerHTML = `<img src="${url}" alt="${name || 'Adjunto'}" class="max-w-[90vw] max-h-[85vh] object-contain">`;
                    } else {
                        stage.innerHTML = `<iframe src="${url}" class="w-[90vw] h-[85vh] bg-white" title="${name || 'Adjunto'}"></iframe>`;
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

                document.querySelectorAll('.admin-attachment-thumb').forEach((thumb) => {
                    thumb.addEventListener('click', () => {
                        openViewer({
                            url: thumb.dataset.url,
                            name: thumb.dataset.name,
                            mime: thumb.dataset.mime,
                        });
                    });
                });

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
                    }
                });
            });
        </script>
    @endpush
</x-layouts.clean>
