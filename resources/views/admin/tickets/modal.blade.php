@php
    $status = $ticket->latestStatusEvent?->to_status ?? 'nuevo';
    $locacionPadre = $ticket->locacion?->nombre ?? 'Sin ubicación';
    $locacionLabel = $ticket->locacion_hija_texto
        ? $locacionPadre . ' - ' . $ticket->locacion_hija_texto
        : $locacionPadre;
    $assignedTechs = $ticket->currentAssignments ?? collect();
    $assignedIds = $assignedTechs->pluck('technician_id')->all();
    $assignedNames = $assignedTechs->pluck('technician.name')->filter()->join(', ');
    $hasAssignment = $assignedTechs->isNotEmpty();
    $canManage = $assignedTechs->contains('technician_id', auth()->id());
    $classificationComplete = ($ticket->categoria_interna && $ticket->problem_type && $ticket->root_cause);
    $actionsCount = $ticket->actions->count();
    $isResolved = in_array($status, ['resuelto', 'cerrado'], true);
    $isStandby = in_array($status, ['standby', 'en_espera'], true);
    $statusLabel = $status === 'standby' ? 'en espera' : $status;
    $requesterName = $ticket->usuario ?: ($ticket->requester?->name ?? 'Sin nombre');
@endphp

<div
    class="admin-modal bg-white rounded-xl shadow-lg w-full max-w-5xl p-0 overflow-hidden"
    x-data="{
        tab: 'antecedentes',
        actionsCount: {{ $actionsCount }},
        classificationComplete: {{ $classificationComplete ? 'true' : 'false' }},
        canManage: {{ $canManage ? 'true' : 'false' }},
        hasAssignment: {{ $hasAssignment ? 'true' : 'false' }},
        isResolved: {{ $isResolved ? 'true' : 'false' }},
        isStandby: {{ $isStandby ? 'true' : 'false' }},
        canResolve() { return this.canManage && this.actionsCount > 0 && this.classificationComplete; }
    }"
    data-ticket-id="{{ $ticket->id }}">
    <div class="flex flex-col gap-3 px-6 pt-6 pb-4 border-b border-gray-100">
        <div class="flex items-center justify-between">
            <h3 class="admin-modal-title text-lg font-semibold">Gestionar ticket #{{ $ticket->display_id }}</h3>
            <button type="button" data-modal-close class="text-gray-500 hover:text-gray-700">✕</button>
        </div>
        <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
            <div class="flex flex-wrap items-center gap-2">
                <div class="admin-tech-pill rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-700">
                    Técnicos: <span class="font-medium" data-assigned-ticket="{{ $ticket->id }}">{{ $assignedNames ?: '—' }}</span>
                </div>
                <div x-data="{ openAssign: false }" class="relative">
                    <button type="button"
                        @click.stop="openAssign = !openAssign"
                        class="admin-edit-tech text-xs text-gray-500 hover:text-gray-700 underline">
                        Editar técnicos
                    </button>
                    <div
                        x-show="openAssign"
                        x-transition
                        @click.outside="openAssign = false"
                        class="absolute left-0 mt-2 w-72 max-w-[80vw] rounded-xl border border-gray-200 bg-white p-3 shadow-sm z-50">
                        <form method="POST" action="{{ route('admin.tickets.assign-multiple', $ticket) }}" class="space-y-3" @click.stop data-ajax="true" data-ajax-type="assignment" data-ticket-id="{{ $ticket->id }}">
                            @csrf
                            <div class="max-h-52 overflow-y-auto pr-1 space-y-2">
                                @foreach($admins as $adminUser)
                                    <label class="flex items-center gap-2 text-xs text-gray-600">
                                        <input type="checkbox" name="technician_ids[]"
                                            value="{{ $adminUser->id }}"
                                            @checked(in_array($adminUser->id, $assignedIds))
                                            class="rounded border-gray-300 text-[#6B8E23]">
                                        <span>{{ $adminUser->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <div class="flex justify-end">
                                <button type="submit"
                                    class="rounded-lg border border-[#6B8E23] px-3 py-2 text-xs text-[#6B8E23] hover:bg-[#F4F7EE]">
                                    Guardar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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
                    @click="tab = 'chat'"
                    :class="tab === 'chat' ? 'bg-white border-gray-200 shadow-sm' : 'bg-transparent border-transparent'"
                    class="w-full text-left rounded-lg border px-3 py-2 transition"
                    data-chat-tab
                    data-chat-ticket="{{ $ticket->id }}">
                    Chat
                </button>
                <button type="button"
                    @click="tab = 'agenda'"
                    :class="tab === 'agenda' ? 'bg-white border-gray-200 shadow-sm' : 'bg-transparent border-transparent'"
                    class="w-full text-left rounded-lg border px-3 py-2 transition">
                    Agenda
                </button>
                <button type="button"
                    :disabled="!canManage"
                    @click="canManage ? (tab = 'resolucion') : null"
                    :class="tab === 'resolucion' ? 'bg-white border-gray-200 shadow-sm' : 'bg-transparent border-transparent'"
                    class="w-full text-left rounded-lg border px-3 py-2 transition disabled:opacity-40 disabled:cursor-not-allowed">
                    Resolución
                </button>
                <button type="button"
                    :disabled="!canManage"
                    @click="canManage ? (tab = 'cierre_rapido') : null"
                    :class="tab === 'cierre_rapido' ? 'ring-2 ring-[#6B8E23]/20' : ''"
                    class="admin-cta w-full inline-flex items-center justify-center gap-1 rounded-full border border-[#6B8E23] px-2.5 py-1.5 text-xs font-medium text-[#6B8E23] bg-[#F4F7EE] hover:bg-[#E9F0DF] transition disabled:opacity-50 disabled:cursor-not-allowed">
                    Cierre Rapido
                </button>
                <form method="POST" action="{{ route('admin.tickets.status', $ticket) }}" class="w-full" @click.stop>
                    @csrf
                    <input type="hidden" name="to_status" :value="isStandby ? 'standby' : (hasAssignment ? 'asignado' : 'nuevo')">
                    <input type="hidden" name="reason" :value="isStandby ? 'Ticket en espera' : ''">
                    <label
                        class="w-full inline-flex items-center justify-between gap-2 rounded-full border border-orange-400 px-2.5 py-1.5 text-xs font-medium text-orange-700 bg-orange-50 hover:bg-orange-100 transition"
                        :class="(!canManage || isResolved) ? 'opacity-50 cursor-not-allowed' : ''">
                        <span>Ticket en Espera</span>
                        <span class="relative inline-flex h-4 w-8 items-center rounded-full bg-orange-200 transition">
                            <span
                                class="inline-block h-3 w-3 transform rounded-full bg-white shadow transition"
                                :class="isStandby ? 'translate-x-4' : 'translate-x-1'"></span>
                        </span>
                        <input type="checkbox"
                            class="sr-only"
                            x-model="isStandby"
                            @change="$nextTick(() => $el.form.submit())"
                            :disabled="!canManage || isResolved">
                    </label>
                </form>
                <div x-show="!canResolve()" class="text-[11px] text-gray-400">
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
                                        $attachmentUrl = route('tickets.attachments.show', $attachment);
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
                        <div data-status-ticket="{{ $ticket->id }}">{{ $statusLabel }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400">Asignado</div>
                        <div data-assigned-ticket="{{ $ticket->id }}">{{ $assignedNames ?: '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400">Creado</div>
                        <div>{{ $ticket->created_at->format('d-m-Y H:i') }}</div>
                    </div>
                </div>
            </div>

            <div x-show="tab === 'chat'" class="space-y-3">
                <div class="flex items-center justify-between">
                    <div class="text-sm font-medium text-gray-700">Conversación</div>
                    <button type="button"
                        class="text-xs text-gray-500 hover:text-gray-700"
                        data-chat-refresh
                        data-chat-ticket="{{ $ticket->id }}">
                        Actualizar
                    </button>
                </div>
                <div
                    class="h-56 overflow-y-auto rounded-lg border border-gray-200 bg-white p-3 space-y-3 text-sm"
                    data-chat-container
                    data-chat-ticket="{{ $ticket->id }}"
                    data-chat-fetch-url="{{ route('tickets.messages.index', $ticket) }}">
                </div>
                <form
                    class="space-y-2"
                    data-chat-form
                    data-chat-ticket="{{ $ticket->id }}"
                    data-chat-send-url="{{ route('tickets.messages.store', $ticket) }}">
                    @csrf
                    <textarea name="message" rows="2" placeholder="Escribe un mensaje"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700"></textarea>
                    <div class="flex justify-end">
                        <button type="submit"
                            class="rounded-lg border border-[#6B8E23] px-3 py-2 text-xs text-[#6B8E23] hover:bg-[#F4F7EE]">
                            Enviar
                        </button>
                    </div>
                </form>
            </div>

            <div x-show="tab === 'agenda'">
                <div class="space-y-3">
                    @forelse($ticket->schedules as $schedule)
                        @php
                            $techName = $schedule->technician?->name ?? 'Sin técnico';
                            $start = $schedule->start_at?->format('d-m-Y H:i') ?? '';
                            $end = $schedule->end_at?->format('d-m-Y H:i') ?? '';
                            $modality = $schedule->modality === 'terreno' ? 'Visita en terreno' : ($schedule->modality === 'remota' ? 'Atención remota' : null);
                        @endphp
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700">
                            <div class="flex items-center justify-between gap-3">
                                <div class="font-medium">{{ $start }} @if($end) — {{ $end }} @endif</div>
                                @if($modality)
                                    <div class="text-xs text-gray-500">{{ $modality }}</div>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500 mt-1">Técnico: {{ $techName }}</div>
                        </div>
                    @empty
                        <div class="rounded-lg border border-dashed border-gray-300 p-4 text-sm text-gray-500 bg-gray-50">
                            Aún no hay agendamientos registrados.
                        </div>
                    @endforelse
                </div>
            </div>

            <div x-show="tab === 'acciones'">
                <div x-show="!hasAssignment" class="admin-warning mb-4 rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-xs text-yellow-800">
                    Asigna el ticket a un técnico para poder registrar acciones.
                </div>
                <div class="flex items-center justify-between mb-3">
                    <div class="text-sm font-medium text-gray-700">Acciones registradas</div>
                    <div x-show="!canManage" class="text-xs text-gray-400">Solo el técnico asignado puede editar.</div>
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
                    <form method="POST" action="{{ route('admin.tickets.actions', $ticket) }}" class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3" data-ajax="true" data-ajax-type="action" data-ticket-id="{{ $ticket->id }}">
                        @csrf
                        <select name="action_type" required class="rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700" :disabled="!canManage">
                            <option value="repuesto">Repuesto</option>
                            <option value="instalacion">Instalación</option>
                            <option value="compra">Compra</option>
                            <option value="otro">Otro</option>
                        </select>
                        <select name="status" required class="rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700" :disabled="!canManage">
                            <option value="pendiente">Pendiente</option>
                            <option value="en_progreso">En progreso</option>
                            <option value="completado">Completado</option>
                        </select>
                        <textarea name="description" rows="2" placeholder="Describe la acción o tarea" required
                            class="md:col-span-2 rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700" :disabled="!canManage"></textarea>
                        <div class="md:col-span-2 flex justify-end">
                            <button type="submit"
                                class="rounded-lg border border-[#6B8E23] px-3 py-2 text-xs text-[#6B8E23] hover:bg-[#F4F7EE] disabled:opacity-50"
                                :disabled="!canManage">
                                Guardar acción
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div x-show="tab === 'clasificacion'">
                <div x-show="!hasAssignment" class="admin-warning mb-4 rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-xs text-yellow-800">
                    Asigna el ticket a un técnico para poder clasificar.
                </div>
                <div class="flex items-center justify-between mb-3">
                    <div class="text-sm font-medium text-gray-700">Clasificación técnica</div>
                    <div x-show="!canManage" class="text-xs text-gray-400">Solo el técnico asignado puede editar.</div>
                </div>

                <form method="POST" action="{{ route('admin.tickets.classification', $ticket) }}" class="space-y-3" data-ajax="true" data-ajax-type="classification" data-ticket-id="{{ $ticket->id }}">
                    @csrf
                    <input type="text" name="categoria_interna" placeholder="Categoría interna" required
                        value="{{ old('categoria_interna', $ticket->categoria_interna) }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700" :disabled="!canManage">
                    <input type="text" name="problem_type" placeholder="Tipo de problema" required
                        value="{{ old('problem_type', $ticket->problem_type) }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700" :disabled="!canManage">
                    <input type="text" name="root_cause" placeholder="Causa raíz" required
                        value="{{ old('root_cause', $ticket->root_cause) }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700" :disabled="!canManage">
                    <div class="flex justify-end">
                        <button type="submit"
                            class="rounded-lg border border-gray-300 px-3 py-2 text-xs text-gray-700 hover:bg-gray-50 disabled:opacity-50"
                            :disabled="!canManage">
                            Guardar clasificación
                        </button>
                    </div>
                </form>
            </div>

            <div x-show="tab === 'resolucion'">
                <div class="text-sm font-medium text-gray-700 mb-3">Resolución</div>
                <div x-show="!hasAssignment" class="admin-warning mb-4 rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-xs text-yellow-800">
                    Asigna el ticket a un técnico para completar la resolución.
                </div>
                <div x-show="actionsCount === 0" class="mb-4 rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-xs text-yellow-800">
                    Registra al menos una acción antes de completar la resolución.
                </div>
                <div x-show="!classificationComplete" class="mb-4 rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-xs text-yellow-800">
                    Completa la clasificación técnica antes de cerrar el ticket.
                </div>
                <form method="POST" action="{{ route('admin.tickets.resolve', $ticket) }}" class="space-y-3" data-ajax="true" data-ajax-type="resolution" data-ticket-id="{{ $ticket->id }}">
                    @csrf
                    <textarea name="resolution_text" rows="4" placeholder="Resumen de resolución" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700" :disabled="!canManage">{{ $ticket->resolution?->resolution_text }}</textarea>
                    <div class="flex justify-end">
                        <button type="submit"
                            class="rounded-lg border border-[#6B8E23] px-3 py-2 text-xs text-[#6B8E23] hover:bg-[#F4F7EE] disabled:opacity-50"
                            :disabled="!canManage">
                            Completar ticket
                        </button>
                    </div>
                </form>
            </div>

            <div x-show="tab === 'cierre_rapido'">
                <div class="text-sm font-medium text-gray-700 mb-3">Cierre rápido</div>
                <div class="text-xs text-gray-500 mb-3">
                    Registra una acción, clasificación y resolución en un solo paso.
                </div>
                <form method="POST" action="{{ route('admin.tickets.quick-close', $ticket) }}" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <select name="action_type" required class="rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700" :disabled="!canManage">
                            <option value="">Tipo de acción</option>
                            <option value="repuesto">Repuesto</option>
                            <option value="instalacion">Instalación</option>
                            <option value="compra">Compra</option>
                            <option value="otro">Otro</option>
                        </select>
                        <select name="action_status" required class="rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700" :disabled="!canManage">
                            <option value="">Estado de acción</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="en_progreso">En progreso</option>
                            <option value="completado">Completado</option>
                        </select>
                        <textarea name="action_description" rows="2" placeholder="Descripción de la acción" required
                            class="md:col-span-2 rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700" :disabled="!canManage"></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <input type="text" name="categoria_interna" placeholder="Categoría interna" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700" :disabled="!canManage">
                        <input type="text" name="problem_type" placeholder="Tipo de problema" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700" :disabled="!canManage">
                        <input type="text" name="root_cause" placeholder="Causa raíz" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700" :disabled="!canManage">
                    </div>
                    <textarea name="resolution_text" rows="3" placeholder="Resolución final" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700" :disabled="!canManage"></textarea>
                    <div class="flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-full border border-[#6B8E23] text-sm font-medium text-[#6B8E23] bg-[#F4F7EE] hover:bg-[#E9F0DF] transition disabled:opacity-50"
                            :disabled="!canManage">
                            Cerrar Ticket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
