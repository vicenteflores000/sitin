@php
    $embedded = $embedded ?? false;
@endphp
@if(!$embedded)
<x-layouts.clean>
@endif
    @push('head')
        <style>
            .fc {
                font-family: inherit;
                color: #374151;
            }

            .fc .fc-toolbar-title {
                font-size: 1rem;
                font-weight: 600;
                color: #374151;
            }

            .fc .fc-button {
                background: #F4F7EE;
                border: 1px solid #DCE7C5;
                color: #6B8E23;
                border-radius: 0.75rem;
                padding: 0.35rem 0.65rem;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: none;
                box-shadow: none;
            }

            .fc .fc-button:hover {
                background: #E9F0DF;
                border-color: #CFE0B0;
                color: #5A7A1E;
            }

            .fc .fc-button:focus {
                box-shadow: none;
            }

            .fc .fc-button-primary:not(:disabled).fc-button-active {
                background: #6B8E23;
                border-color: #6B8E23;
                color: #ffffff;
            }

            .fc .fc-daygrid-day-number,
            .fc .fc-timegrid-slot-label,
            .fc .fc-col-header-cell-cushion {
                color: #6B7280;
                font-size: 0.75rem;
            }

            .fc .fc-scrollgrid,
            .fc .fc-scrollgrid-section > td,
            .fc .fc-timegrid-slot,
            .fc .fc-timegrid-axis,
            .fc .fc-daygrid-day {
                border-color: #E5E7EB;
            }

            .fc .fc-event {
                color: #ffffff;
                border-radius: 0.5rem;
                padding: 0.15rem 0.35rem;
                font-size: 0.75rem;
            }

            .fc .fc-event.event-synced {
                background: #6B8E23;
                border-color: #6B8E23;
            }

            .fc .fc-event.event-error {
                background: #DC2626;
                border-color: #DC2626;
            }

            .fc .fc-event .fc-event-time {
                font-weight: 600;
            }

            .fc .fc-timegrid-now-indicator-line {
                border-color: #DC2626;
            }

            .fc .fc-timegrid-now-indicator-arrow {
                border-color: #DC2626;
            }

            .fc {
                font-size: 0.75rem;
                --fc-small-font-size: 0.65rem;
            }

            .fc .fc-toolbar-title {
                font-size: 0.95rem;
            }

            .fc .fc-button {
                padding: 0.25rem 0.5rem;
                font-size: 0.7rem;
            }

            .fc .fc-daygrid-day-number,
            .fc .fc-timegrid-slot-label,
            .fc .fc-col-header-cell-cushion {
                font-size: 0.7rem;
            }

            .fc .fc-timegrid-slot {
                height: 1.2rem;
            }

            .fc .fc-timegrid-axis-cushion,
            .fc .fc-timegrid-slot-label-cushion {
                padding: 0 2px;
            }

            .fc .fc-event {
                font-size: 0.7rem;
                padding: 0.1rem 0.25rem;
            }

            .fc .fc-daygrid-event {
                line-height: 1.1;
            }

            .fc .fc-daygrid-event-harness {
                margin-top: 1px;
            }

            .select2-container .select2-selection--single {
                height: 42px;
                border: 1px solid #d1d5db;
                border-radius: 0.5rem;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 40px;
                padding-left: 12px;
                font-size: 0.875rem;
                color: #374151;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 40px;
                right: 8px;
            }

            .select2-container--default .select2-dropdown {
                border-color: #e5e7eb;
                border-radius: 0.75rem;
            }

            .ticket-option-desc {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            html.dark-mode .select2-container .select2-selection--single {
                background-color: #0b1220;
                border-color: #1f2a44;
            }

            html.dark-mode .select2-container--default .select2-selection--single .select2-selection__rendered {
                color: #e5e7eb;
            }

            html.dark-mode .select2-container--default .select2-dropdown {
                background-color: #0f172a;
                border-color: #1f2a44;
            }

            html.dark-mode .select2-container--default .select2-results__option {
                color: #e5e7eb;
            }

            html.dark-mode .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
                background-color: #1f2a44;
            }
        </style>
    @endpush

    <div class="w-full h-screen flex flex-col items-center px-4 bg-[#FAFAF7] overflow-hidden">
        <div class="w-full max-w-6xl py-8 flex flex-col justify-center" style="height: calc(100vh - 2rem);">

            <div class="mb-6 text-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Tickets TI" class="theme-logo-light mx-auto h-12" style="width: 200px; height: auto;">
                <img src="{{ asset('images/logo-white.png') }}" alt="Logo Tickets TI" class="theme-logo-dark mx-auto h-12" style="width: 200px; height: auto;">
                <p class="text-gray-600">Calendario personal de tickets</p>
            </div>

            <div class="bg-white rounded-xl shadow-xl border border-gray-200 p-6 flex flex-col"
                style="height: 620px; min-height: 620px; max-height: 620px;">

                <div class="flex items-center justify-between mb-4 gap-3">
                    <form action="{{ route('admin.dashboard') }}">
                        <button
                            class="mb-3 rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                            type="submit">
                            &larr; Volver
                        </button>
                    </form>
                    <div class="text-xs text-gray-500">Arrastra y ajusta bloques para organizar tu jornada.</div>
                </div>

                <div class="flex-1 overflow-hidden">
                    <div id="calendar-wrapper" class="h-full">
                        <div id="calendar-error" class="hidden text-center text-sm text-red-600 py-8 px-4">
                            No se pudieron cargar los archivos del calendario. Ejecuta <span class="font-semibold">npm run build</span> en el servidor y recarga.
                        </div>
                        <div id="calendar" class="h-full"></div>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-4 text-xs text-gray-500">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-[#6B8E23]"></span>
                        Sincronizado
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-[#DC2626]"></span>
                        Error de sincronización
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="schedule-modal" class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6" role="dialog" aria-modal="true">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Agendar ticket</h3>
                <button type="button" id="schedule-close" class="text-gray-500 hover:text-gray-700">✕</button>
            </div>

            <form id="schedule-form" class="space-y-4">
                <input type="hidden" id="schedule-id" name="schedule_id">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ticket asignado</label>
                    <select id="schedule-ticket" name="ticket_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700" data-enhanced-select required>
                        <option value="">Selecciona un ticket</option>
                        @foreach($tickets as $ticket)
                            @php
                                $loc = $ticket->locacion?->padre?->nombre
                                    ? $ticket->locacion->padre->nombre . ' - ' . $ticket->locacion->nombre
                                    : ($ticket->locacion?->nombre ?? 'Sin ubicación');
                                $label = '#'.$ticket->display_id.' · '.$loc;
                            @endphp
                            <option
                                value="{{ $ticket->id }}"
                                data-label="{{ $label }}"
                                data-description="{{ \Illuminate\Support\Str::limit($ticket->descripcion ?? '', 140) }}">
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Modalidad</label>
                    <select id="schedule-modality" name="modality" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700" required>
                        <option value="remota">Remota</option>
                        <option value="terreno">Visita en terreno</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Inicio</label>
                        <input type="datetime-local" id="schedule-start" name="start_at"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fin</label>
                        <input type="datetime-local" id="schedule-end" name="end_at"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700" required>
                    </div>
                </div>

                <div class="flex justify-between items-center gap-2">
                    <button type="button" id="schedule-delete"
                        class="rounded-lg border border-red-200 px-3 py-2 text-xs text-red-600 hover:bg-red-50 hidden">
                        Eliminar
                    </button>
                    <div class="flex gap-2">
                        <button type="button" id="schedule-cancel"
                            class="rounded-lg border border-gray-300 px-3 py-2 text-xs text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit" id="schedule-submit"
                            class="rounded-lg border border-[#6B8E23] px-3 py-2 text-xs text-[#6B8E23] hover:bg-[#F4F7EE]">
                            Guardar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('vendor/fullcalendar/core.min.js') }}"></script>
        <script src="{{ asset('vendor/fullcalendar/daygrid.min.js') }}"></script>
        <script src="{{ asset('vendor/fullcalendar/timegrid.min.js') }}"></script>
        <script src="{{ asset('vendor/fullcalendar/interaction.min.js') }}"></script>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const calendarEl = document.getElementById('calendar');
                const calendarWrapper = document.getElementById('calendar-wrapper');
                const calendarError = document.getElementById('calendar-error');
                const showCalendarError = (message) => {
                    if (calendarError) {
                        calendarError.innerHTML = message;
                        calendarError.classList.remove('hidden');
                    }
                    if (calendarEl) {
                        calendarEl.classList.add('hidden');
                    }
                    if (calendarWrapper) {
                        calendarWrapper.classList.add('flex', 'items-center', 'justify-center');
                    }
                };

                if (!calendarEl) {
                    return;
                }

                if (!window.FullCalendar || !window.FullCalendar.Calendar) {
                    showCalendarError('No se pudieron cargar los archivos del calendario. Ejecuta <span class="font-semibold">npm run build</span> en el servidor y recarga.');
                    return;
                }

                if (!window.FullCalendar.DayGrid?.default || !window.FullCalendar.TimeGrid?.default || !window.FullCalendar.Interaction?.default) {
                    showCalendarError('Faltan módulos del calendario. Revisa que <span class="font-semibold">core.min.js</span>, <span class="font-semibold">daygrid.min.js</span>, <span class="font-semibold">timegrid.min.js</span> e <span class="font-semibold">interaction.min.js</span> existan en <span class="font-semibold">public/vendor/fullcalendar</span>.');
                    return;
                }
                const modal = document.getElementById('schedule-modal');
                const closeBtn = document.getElementById('schedule-close');
                const cancelBtn = document.getElementById('schedule-cancel');
                const form = document.getElementById('schedule-form');
                const ticketSelect = document.getElementById('schedule-ticket');
                const modalitySelect = document.getElementById('schedule-modality');
                const startInput = document.getElementById('schedule-start');
                const endInput = document.getElementById('schedule-end');
                const scheduleIdInput = document.getElementById('schedule-id');
                const deleteButton = document.getElementById('schedule-delete');
                const submitButton = document.getElementById('schedule-submit');
                const csrfToken = '{{ csrf_token() }}';
                let isScheduling = false;
                let schedulingInterval = null;
                const schedulingFrames = ['Agendando', 'Agendando .', 'Agendando ..', 'Agendando ...', 'Agendando ..', 'Agendando .'];
                let schedulingIndex = 0;
                let select2Tries = 0;
                initTicketSelect();

                function toLocalInput(date) {
                    const d = new Date(date);
                    const pad = (n) => String(n).padStart(2, '0');
                    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
                }

                function formatTicketOption(option) {
                    if (!option.id) {
                        return option.text;
                    }
                    const el = option.element;
                    const label = el?.dataset?.label || option.text || '';
                    const description = el?.dataset?.description || '';
                    const container = document.createElement('div');
                    const title = document.createElement('div');
                    title.className = 'text-sm font-medium';
                    title.textContent = label;
                    container.appendChild(title);
                    if (description) {
                        const desc = document.createElement('div');
                        desc.className = 'ticket-option-desc text-xs text-gray-500';
                        desc.textContent = description;
                        container.appendChild(desc);
                    }
                    return container;
                }

                function formatTicketSelection(option) {
                    if (!option.id) {
                        return option.text;
                    }
                    const el = option.element;
                    return el?.dataset?.label || option.text;
                }

                function initTicketSelect() {
                    if (!ticketSelect) {
                        return;
                    }
                    if (!window.$ || !window.$.fn || !window.$.fn.select2) {
                        if (select2Tries < 10) {
                            select2Tries += 1;
                            setTimeout(initTicketSelect, 200);
                        }
                        return;
                    }
                    const $select = window.$(ticketSelect);
                    if ($select.hasClass('select2-hidden-accessible')) {
                        $select.select2('destroy');
                    }
                    $select.select2({
                        width: '100%',
                        placeholder: 'Seleccione...',
                        allowClear: true,
                        dropdownParent: window.$('#schedule-modal'),
                        templateResult: formatTicketOption,
                        templateSelection: formatTicketSelection,
                        escapeMarkup: (markup) => markup,
                    });
                }

                function startSchedulingAnimation(baseText = 'Agendando') {
                    if (!submitButton) {
                        return;
                    }
                    schedulingIndex = 0;
                    const frames = schedulingFrames.map((frame) => frame.replace('Agendando', baseText));
                    submitButton.textContent = frames[schedulingIndex];
                    schedulingInterval = setInterval(() => {
                        schedulingIndex = (schedulingIndex + 1) % frames.length;
                        submitButton.textContent = frames[schedulingIndex];
                    }, 350);
                }

                function stopSchedulingAnimation() {
                    if (schedulingInterval) {
                        clearInterval(schedulingInterval);
                        schedulingInterval = null;
                    }
                    if (submitButton) {
                        submitButton.textContent = submitButton.dataset.defaultText || 'Guardar';
                    }
                }

                function setScheduling(value, baseText = 'Agendando') {
                    isScheduling = value;
                    if (!submitButton) {
                        return;
                    }
                    if (value) {
                        submitButton.disabled = true;
                        submitButton.classList.add('opacity-60', 'cursor-not-allowed');
                        if (cancelBtn) {
                            cancelBtn.disabled = true;
                            cancelBtn.classList.add('opacity-60', 'cursor-not-allowed');
                        }
                        if (closeBtn) {
                            closeBtn.disabled = true;
                            closeBtn.classList.add('opacity-60', 'cursor-not-allowed');
                        }
                        if (deleteButton) {
                            deleteButton.disabled = true;
                            deleteButton.classList.add('opacity-60', 'cursor-not-allowed');
                        }
                        startSchedulingAnimation(baseText);
                    } else {
                        submitButton.disabled = false;
                        submitButton.classList.remove('opacity-60', 'cursor-not-allowed');
                        if (cancelBtn) {
                            cancelBtn.disabled = false;
                            cancelBtn.classList.remove('opacity-60', 'cursor-not-allowed');
                        }
                        if (closeBtn) {
                            closeBtn.disabled = false;
                            closeBtn.classList.remove('opacity-60', 'cursor-not-allowed');
                        }
                        if (deleteButton) {
                            deleteButton.disabled = false;
                            deleteButton.classList.remove('opacity-60', 'cursor-not-allowed');
                        }
                        stopSchedulingAnimation();
                    }
                }

                function openModal(start, end) {
                    startInput.value = start;
                    endInput.value = end;
                    ticketSelect.disabled = false;
                    modalitySelect.value = 'remota';
                    scheduleIdInput.value = '';
                    deleteButton.classList.add('hidden');
                    submitButton.dataset.defaultText = 'Guardar';
                    submitButton.textContent = submitButton.dataset.defaultText;
                    modal.classList.remove('hidden');
                }

                function openEditModal(event) {
                    const start = toLocalInput(event.start);
                    const end = toLocalInput(event.end || event.start);
                    startInput.value = start;
                    endInput.value = end;
                    ticketSelect.disabled = true;
                    modalitySelect.value = event.extendedProps?.modality || 'remota';
                    scheduleIdInput.value = event.id;
                    ticketSelect.value = event.extendedProps?.ticket_id || '';
                    deleteButton.classList.remove('hidden');
                    submitButton.dataset.defaultText = 'Guardar cambios';
                    submitButton.textContent = submitButton.dataset.defaultText;
                    modal.classList.remove('hidden');
                }

                function closeModal() {
                    modal.classList.add('hidden');
                    form.reset();
                    ticketSelect.disabled = false;
                    modalitySelect.value = 'remota';
                    if (!isScheduling) {
                        stopSchedulingAnimation();
                    }
                }

                closeBtn.addEventListener('click', () => {
                    if (!isScheduling) {
                        closeModal();
                    }
                });
                cancelBtn.addEventListener('click', () => {
                    if (!isScheduling) {
                        closeModal();
                    }
                });
                modal.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        closeModal();
                    }
                });

                const calendar = new window.FullCalendar.Calendar(calendarEl, {
                    initialView: 'timeGridWeek',
                    locale: window.FullCalendarLocaleEs || 'es',
                    plugins: [
                        window.FullCalendar.DayGrid.default,
                        window.FullCalendar.TimeGrid.default,
                        window.FullCalendar.Interaction.default,
                    ],
                    height: '100%',
                    nowIndicator: true,
                    editable: true,
                    selectable: true,
                    buttonText: {
                        today: 'Hoy',
                        month: 'Mes',
                        week: 'Semana',
                        day: 'Día',
                    },
                    allDayText: 'Todo el día',
                    eventTimeFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false,
                    },
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay',
                    },
                    events: "{{ route('admin.calendar.events') }}",
                    select: (info) => {
                        const start = info.startStr.slice(0, 16);
                        const end = info.endStr.slice(0, 16);
                        openModal(start, end);
                    },
                    eventClick: (info) => {
                        info.jsEvent.preventDefault();
                        openEditModal(info.event);
                    },
                    eventDrop: async (info) => {
                        const ok = await updateEvent(info.event);
                        if (!ok) {
                            info.revert();
                        }
                    },
                    eventResize: async (info) => {
                        const ok = await updateEvent(info.event);
                        if (!ok) {
                            info.revert();
                        }
                    },
                    eventDidMount: (info) => {
                        const { location, user } = info.event.extendedProps || {};
                        if (location || user) {
                            info.el.title = `${location || ''}${location && user ? ' · ' : ''}${user || ''}`;
                        }
                    },
                });

                calendar.render();

                async function updateEvent(event) {
                    const payload = {
                        start_at: toLocalInput(event.start),
                        end_at: toLocalInput(event.end || event.start),
                        modality: event.extendedProps?.modality || 'remota',
                    };

                    const response = await fetch(`{{ url('/admin/calendario/events') }}/${event.id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(payload),
                        credentials: 'same-origin',
                    });

                    if (response.ok) {
                        const updatedEvent = await response.json().catch(() => null);
                        if (updatedEvent) {
                            event.setStart(updatedEvent.start);
                            event.setEnd(updatedEvent.end);
                            event.setProp('classNames', updatedEvent.classNames || []);
                            if (updatedEvent.extendedProps) {
                                Object.entries(updatedEvent.extendedProps).forEach(([key, value]) => {
                                    event.setExtendedProp(key, value);
                                });
                            }
                        }
                    }

                    return response.ok;
                }

                form.addEventListener('submit', async (event) => {
                    event.preventDefault();
                    if (!ticketSelect.value) {
                        return;
                    }
                    if (isScheduling) {
                        return;
                    }

                    const scheduleId = scheduleIdInput.value;
                    if (scheduleId) {
                        setScheduling(true, 'Guardando');
                        const payload = {
                            start_at: startInput.value,
                            end_at: endInput.value,
                            modality: modalitySelect.value || 'remota',
                        };
                        const response = await fetch(`{{ url('/admin/calendario/events') }}/${scheduleId}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(payload),
                            credentials: 'same-origin',
                        });

                        if (response.ok) {
                            const updatedEvent = await response.json().catch(() => null);
                            const eventToUpdate = calendar.getEventById(scheduleId);
                            if (eventToUpdate && updatedEvent) {
                                eventToUpdate.setStart(updatedEvent.start);
                                eventToUpdate.setEnd(updatedEvent.end);
                                eventToUpdate.setProp('classNames', updatedEvent.classNames || []);
                                if (updatedEvent.extendedProps) {
                                    Object.entries(updatedEvent.extendedProps).forEach(([key, value]) => {
                                        eventToUpdate.setExtendedProp(key, value);
                                    });
                                }
                            } else {
                                calendar.refetchEvents();
                            }
                        }

                        setScheduling(false);
                        closeModal();
                        return;
                    }

                    setScheduling(true, 'Agendando');
                    const payload = {
                        ticket_id: ticketSelect.value,
                        start_at: startInput.value,
                        end_at: endInput.value,
                        modality: modalitySelect.value || 'remota',
                    };

                    const response = await fetch("{{ route('admin.calendar.store') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(payload),
                        credentials: 'same-origin',
                    });

                    if (!response.ok) {
                        setScheduling(false);
                        closeModal();
                        return;
                    }

                    const createdEvent = await response.json().catch(() => null);
                    if (createdEvent && createdEvent.id) {
                        calendar.addEvent(createdEvent);
                    } else {
                        calendar.refetchEvents();
                    }
                    setScheduling(false);
                    closeModal();
                });

                deleteButton.addEventListener('click', async () => {
                    const scheduleId = scheduleIdInput.value;
                    if (!scheduleId) {
                        return;
                    }

                    const response = await fetch(`{{ url('/admin/calendario/events') }}/${scheduleId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                    });

                    if (response.ok) {
                        const eventToRemove = calendar.getEventById(scheduleId);
                        if (eventToRemove) {
                            eventToRemove.remove();
                        } else {
                            calendar.refetchEvents();
                        }
                    }

                    closeModal();
                });
            });
        </script>
    @endpush
@if(!$embedded)
</x-layouts.clean>
@endif
