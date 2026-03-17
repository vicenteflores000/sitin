<x-layouts.clean>
    @push('head')
        <style>
            @keyframes dotPulse {
                0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
                40% { transform: scale(1); opacity: 1; }
            }
            .auth-dot {
                width: 10px;
                height: 10px;
                border-radius: 9999px;
                display: inline-block;
                animation: dotPulse 1.2s infinite ease-in-out;
            }
        </style>
    @endpush
    <style>
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
    </style>
    <div class="w-full h-screen flex flex-col items-center px-4 bg-[#FAFAF7] overflow-hidden">
        <div class="w-full max-w-xl py-8 flex flex-col justify-center" style="height: calc(100vh - 2rem);">

            <div class="mb-6 text-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Tickets TI" class="mx-auto h-12" style="width: 200px; height: auto;">
                <p class="text-gray-600">Solicitud de soporte TI</p>
            </div>

            <div class="bg-white rounded-xl shadow-xl border border-gray-200 p-6 flex flex-col">

                <form id="ticket-form" method="POST" action="/ticket">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 pr-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción breve del problema</label>
                            <textarea name="descripcion" maxlength="300" rows="4" required
                                placeholder="Describa el problema de forma breve y clara"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('descripcion') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Establecimiento</label>
                            <select name="locacion_id" id="locacion_select" required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccione...</option>
                                @foreach ($locaciones as $locacion)
                                    <option value="{{ $locacion->id }}" @selected(old('locacion_id') == $locacion->id)>
                                        {{ $locacion->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ubicación (detalle)</label>
                            <input type="text" name="locacion_hija_texto" value="{{ old('locacion_hija_texto') }}" required
                                placeholder="Ej: Sala 3, Oficina dirección"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de solicitud</label>
                            <select name="tipo" required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccione...</option>
                                <option value="Algo no funciona..." @selected(old('tipo') === 'Algo no funciona...')>Algo no funciona...</option>
                                <option value="Necesito ayuda para algo..." @selected(old('tipo') === 'Necesito ayuda para algo...')>Necesito ayuda para algo...</option>
                                <option value="No puedo acceder / entrar ..." @selected(old('tipo') === 'No puedo acceder / entrar ...')>No puedo acceder / entrar ...</option>
                                <option value="Necesito una mejora / cambio en algo..." @selected(old('tipo') === 'Necesito una mejora / cambio en algo...')>Necesito una mejora / cambio en algo...</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Impacto laboral (opcional)</label>
                            <input type="hidden" name="impacto" id="impacto_input" value="{{ old('impacto') }}">
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" data-impacto="No impide trabajar"
                                    class="impacto-btn rounded-lg border border-gray-300 px-3 py-2 text-xs text-gray-700 hover:bg-gray-50">
                                    No impide trabajar
                                </button>
                                <button type="button" data-impacto="Dificulta trabajar"
                                    class="impacto-btn rounded-lg border border-gray-300 px-3 py-2 text-xs text-gray-700 hover:bg-gray-50">
                                    Dificulta trabajar
                                </button>
                                <button type="button" data-impacto="Impide trabajar"
                                    class="impacto-btn rounded-lg border border-gray-300 px-3 py-2 text-xs text-gray-700 hover:bg-gray-50">
                                    Impide trabajar
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" id="ticket-submit"
                            class="w-full rounded-xl border border-[#6B8E23] px-4 py-2 text-sm font-medium text-[#6B8E23] bg-[#F4F7EE] hover:bg-[#E9F0DF] transition">
                            <span id="ticket-submit-text">Enviar solicitud</span>
                        </button>
                    </div>

                </form>
            </div>

            <div class="mt-4 flex items-center gap-3">
                <div
                    id="auth-menu"
                    x-data="{ open: false, authenticated: {{ auth()->check() ? 'true' : 'false' }} }"
                    class="relative"
                    data-authenticated="{{ auth()->check() ? 'true' : 'false' }}">

                    <button
                        @click="authenticated ? open = !open : (window.openAuthModal && window.openAuthModal('login'))"
                        @click.outside="if (authenticated) open = false"
                        class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5.121 17.804A9 9 0 1118.88 17.8M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>

                        <span id="auth-menu-text">
                            @auth
                                {{ auth()->user()->email }}
                            @else
                                Iniciar Sesión
                            @endauth
                        </span>

                        <svg x-show="authenticated" class="w-3 h-3 text-gray-400" id="auth-menu-caret" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    {{-- Dropdown --}}
                    <div
                        x-show="open && authenticated"
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
                <a
                    href="{{ route('dashboard') }}"
                    data-auth-required="{{ auth()->check() ? 'false' : 'true' }}"
                    id="dashboard-link"
                    class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 10h7V3H3v7zM14 21h7v-7h-7v7zM14 3h7v7h-7V3zM3 21h7v-7H3v7z" />
                    </svg>
                    Dashboard
                </a>
            </div>
        </div>
    </div>

    <div id="auth-modal" class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50 hidden" aria-hidden="true">
        <div class="bg-white rounded-xl shadow-lg w-[22rem] p-6" role="dialog" aria-modal="true" aria-labelledby="auth-title">
            <h2 id="auth-title" class="text-lg font-semibold mb-2">Antes de continuar</h2>
            <p class="text-sm text-gray-500 mb-4">Solo puedes acceder con tu correo institucional.</p>

            <div class="mt-4">
                <a href="{{ route('auth.microsoft.redirect') }}" id="auth-outlook"
                    class="w-full inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="#F25022" d="M1 1h10v10H1z" />
                        <path fill="#7FBA00" d="M13 1h10v10H13z" />
                        <path fill="#00A4EF" d="M1 13h10v10H1z" />
                        <path fill="#FFB900" d="M13 13h10v10H13z" />
                    </svg>
                    Iniciar con Outlook
                </a>
            </div>

            <div class="mt-4">
                <button type="button" id="auth-cancel"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
            </div>
        </div>
    </div>

    @php
        $locacionesGrouped = $locaciones->groupBy(fn($loc) => $loc->padre?->id ?? 'sin');
        $suggestedLocaciones = collect();
        if (auth()->check()) {
            $suggestedLocaciones = auth()->user()->locaciones()->with('padre')->get();
        }
    @endphp
    <div id="locacion-modal" class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50 hidden" aria-hidden="true">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl p-6" role="dialog" aria-modal="true" aria-labelledby="locacion-title">
            <div class="flex items-center justify-between mb-4">
                <h2 id="locacion-title" class="text-lg font-semibold">Selecciona una locación</h2>
                <button type="button" id="locacion-close" class="text-gray-500 hover:text-gray-700">✕</button>
            </div>

            <div class="mb-4">
                <input type="text" id="locacion-search" placeholder="Buscar locación o establecimiento..."
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            @if ($suggestedLocaciones->count() > 0)
                <div id="locacion-suggested" class="mb-4 rounded-lg border border-[#DCE7C5] bg-[#F4F7EE] p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500 mb-2">Sugeridas</p>
                    <div class="space-y-2">
                        @foreach ($suggestedLocaciones as $locacion)
                            @php
                                $label = ($locacion->padre?->nombre ? $locacion->padre->nombre . ' - ' : '') . $locacion->nombre;
                            @endphp
                            <button type="button"
                                class="w-full text-left text-sm text-gray-700 hover:text-[#6B8E23] locacion-child locacion-suggested"
                                data-id="{{ $locacion->id }}"
                                data-label="{{ $label }}"
                                data-suggest-name="{{ strtolower($label) }}"
                                data-child-name="{{ strtolower($locacion->nombre) }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="max-h-[50vh] overflow-y-auto pr-2 space-y-3" id="locacion-list">
                @foreach ($locacionesGrouped as $group)
                    @php
                        $padre = $group->first()?->padre;
                        $parentId = $padre?->id ?? 'sin';
                        $parentName = $padre?->nombre ?? 'Sin establecimiento';
                    @endphp
                    <div class="border border-gray-200 rounded-lg bg-gray-50 locacion-group" data-parent-name="{{ strtolower($parentName) }}">
                        <button type="button"
                            class="w-full flex items-center justify-between px-4 py-3 text-left text-sm font-medium text-gray-700 locacion-parent-toggle"
                            data-target="locacion-group-{{ $parentId }}">
                            <span>{{ $parentName }}</span>
                            <span class="text-xs text-gray-500">{{ $group->count() }} locaciones</span>
                        </button>
                        <div id="locacion-group-{{ $parentId }}" class="px-4 pb-3 space-y-2 hidden locacion-children">
                            @foreach ($group as $locacion)
                                @php
                                    $label = ($locacion->padre?->nombre ? $locacion->padre->nombre . ' - ' : '') . $locacion->nombre;
                                @endphp
                                <button type="button"
                                    class="w-full text-left text-sm text-gray-700 hover:text-[#6B8E23] locacion-child"
                                    data-id="{{ $locacion->id }}"
                                    data-label="{{ $label }}"
                                    data-child-name="{{ strtolower($locacion->nombre) }}">
                                    {{ $locacion->nombre }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div id="auth-loading" class="fixed inset-0 hidden items-center justify-center bg-black/80 backdrop-blur-sm z-50">
        <div class="text-center text-white">
            <div class="flex items-center justify-center gap-2 mb-4">
                <span class="auth-dot bg-[#6B8E23]" style="animation-delay: 0s;"></span>
                <span class="auth-dot bg-[#6B8E23]" style="animation-delay: 0.2s;"></span>
                <span class="auth-dot bg-[#6B8E23]" style="animation-delay: 0.4s;"></span>
            </div>
            <div class="text-sm tracking-wide">Iniciando su sesión, espere un momento</div>
        </div>
    </div>

    <script>
        (function () {
            const ticketForm = document.getElementById('ticket-form');
            const modal = document.getElementById('auth-modal');
            const cancelButton = document.getElementById('auth-cancel');
            const outlookButton = document.getElementById('auth-outlook');
            const authLoading = document.getElementById('auth-loading');
            const ticketSubmit = document.getElementById('ticket-submit');
            const ticketSubmitText = document.getElementById('ticket-submit-text');
            const locacionSelect = document.getElementById('locacion_select');
            const locacionPicker = document.getElementById('locacion-picker');
            const locacionLabel = document.getElementById('locacion-label');
            const locacionModal = document.getElementById('locacion-modal');
            const locacionClose = document.getElementById('locacion-close');
            const locacionSearch = document.getElementById('locacion-search');
            const locacionList = document.getElementById('locacion-list');
            const locacionGroups = document.querySelectorAll('.locacion-group');
            const locacionChildren = document.querySelectorAll('.locacion-child');
            const locacionSuggested = document.querySelectorAll('.locacion-suggested');
            const locacionSuggestedBox = document.getElementById('locacion-suggested');
            const locacionToggles = document.querySelectorAll('.locacion-parent-toggle');
            let requiresAuth = {{ auth()->check() ? 'false' : 'true' }};
            let readyToSubmit = false;
            let authContext = 'ticket';
            let authRedirect = null;
            let sessionActive = !requiresAuth;
            let isSubmitting = false;
            let submittingInterval = null;
            const submittingFrames = ['Enviando', 'Enviando .', 'Enviando ..', 'Enviando ...', 'Enviando ..', 'Enviando .'];
            let submittingIndex = 0;
            const draftKey = 'sitin_ticket_draft';
            const submitAfterLoginKey = 'sitin_ticket_submit_after_login';

            function startSubmittingAnimation() {
                if (!ticketSubmitText) {
                    return;
                }
                submittingIndex = 0;
                ticketSubmitText.textContent = submittingFrames[submittingIndex];
                submittingInterval = setInterval(() => {
                    submittingIndex = (submittingIndex + 1) % submittingFrames.length;
                    ticketSubmitText.textContent = submittingFrames[submittingIndex];
                }, 350);
            }

            function stopSubmittingAnimation() {
                if (submittingInterval) {
                    clearInterval(submittingInterval);
                    submittingInterval = null;
                }
                if (ticketSubmitText) {
                    ticketSubmitText.textContent = 'Enviar solicitud';
                }
            }

            function setSubmitting(value) {
                isSubmitting = value;
                if (value) {
                    if (ticketSubmit) {
                        ticketSubmit.disabled = true;
                        ticketSubmit.classList.add('opacity-60', 'cursor-not-allowed');
                    }
                    startSubmittingAnimation();
                } else {
                    if (ticketSubmit) {
                        ticketSubmit.disabled = false;
                        ticketSubmit.classList.remove('opacity-60', 'cursor-not-allowed');
                    }
                    stopSubmittingAnimation();
                }
            }

            function openLocacionModal() {
                if (!locacionModal) return;
                locacionModal.classList.remove('hidden');
                locacionModal.setAttribute('aria-hidden', 'false');
                if (locacionSearch) {
                    locacionSearch.value = '';
                    filterLocaciones('');
                    locacionSearch.focus();
                }
            }

            function closeLocacionModal() {
                if (!locacionModal) return;
                locacionModal.classList.add('hidden');
                locacionModal.setAttribute('aria-hidden', 'true');
            }

            function filterLocaciones(query) {
                const q = (query || '').toLowerCase().trim();

                if (locacionSuggestedBox) {
                    let anySuggested = false;
                    locacionSuggested.forEach((item) => {
                        const label = item.dataset.suggestName || '';
                        const visible = !q || label.includes(q);
                        item.classList.toggle('hidden', !visible);
                        if (visible) {
                            anySuggested = true;
                        }
                    });
                    locacionSuggestedBox.classList.toggle('hidden', q ? !anySuggested : false);
                }

                locacionGroups.forEach((group) => {
                    const parentName = group.dataset.parentName || '';
                    const children = group.querySelectorAll('.locacion-child');
                    const body = group.querySelector('.locacion-children');
                    let anyVisible = false;

                    children.forEach((child) => {
                        const childName = child.dataset.childName || '';
                        if (q && parentName.includes(q)) {
                            child.classList.remove('hidden');
                            anyVisible = true;
                            return;
                        }
                        const matches = !q || childName.includes(q);
                        child.classList.toggle('hidden', !matches);
                        if (matches) {
                            anyVisible = true;
                        }
                    });

                    if (q) {
                        body.classList.remove('hidden');
                    } else {
                        body.classList.add('hidden');
                    }

                    group.classList.toggle('hidden', q ? !anyVisible : false);
                });
            }

            if (locacionPicker) {
                locacionPicker.addEventListener('click', openLocacionModal);
            }
            if (locacionClose) {
                locacionClose.addEventListener('click', closeLocacionModal);
            }
            if (locacionModal) {
                locacionModal.addEventListener('click', (event) => {
                    if (event.target === locacionModal) {
                        closeLocacionModal();
                    }
                });
            }
            if (locacionSearch) {
                locacionSearch.addEventListener('input', (event) => {
                    filterLocaciones(event.target.value);
                });
            }
            locacionChildren.forEach((child) => {
                child.addEventListener('click', () => {
                    const id = child.dataset.id;
                    const label = child.dataset.label;
                    if (locacionSelect) {
                        locacionSelect.value = id;
                    }
                    if (locacionLabel) {
                        locacionLabel.textContent = label || 'Seleccione una locación';
                    }
                    closeLocacionModal();
                });
            });
            locacionToggles.forEach((toggle) => {
                toggle.addEventListener('click', () => {
                    if (locacionSearch && locacionSearch.value.trim() !== '') {
                        return;
                    }
                    const targetId = toggle.dataset.target;
                    const body = targetId ? document.getElementById(targetId) : null;
                    if (!body) return;
                    body.classList.toggle('hidden');
                });
            });

            function saveDraft() {
                if (!ticketForm) return;
                const data = {};
                const formData = new FormData(ticketForm);
                formData.forEach((value, key) => {
                    if (key === '_token' || key.startsWith('auth_')) return;
                    data[key] = value;
                });
                localStorage.setItem(draftKey, JSON.stringify(data));
            }

            function restoreDraft() {
                const raw = localStorage.getItem(draftKey);
                if (!raw || !ticketForm) return;
                let data = {};
                try {
                    data = JSON.parse(raw);
                } catch {
                    return;
                }
                Object.entries(data).forEach(([key, value]) => {
                    const field = ticketForm.querySelector(`[name="${key}"]`);
                    if (!field) return;
                    field.value = value;
                    field.dispatchEvent(new Event('change', { bubbles: true }));
                    if (key === 'locacion_id' && locacionLabel) {
                        const match = document.querySelector(`.locacion-child[data-id="${value}"]`);
                        locacionLabel.textContent = match?.dataset.label || 'Seleccione una locación';
                    }
                });
                if (data.impacto) {
                    setImpacto(data.impacto);
                }
            }

            function clearDraft() {
                localStorage.removeItem(draftKey);
                localStorage.removeItem(submitAfterLoginKey);
            }

            function openModal(context = 'ticket', redirectUrl = null) {
                if (sessionActive) {
                    if (context === 'dashboard') {
                        window.location.href = redirectUrl || '/dashboard';
                        return;
                    }
                    if (context === 'login') {
                        showToast('Sesión ya iniciada', 'success');
                        return;
                    }
                }
                authContext = context;
                authRedirect = redirectUrl;
                modal.classList.remove('hidden');
                modal.setAttribute('aria-hidden', 'false');
            }

            window.openAuthModal = openModal;

            const dashboardLink = document.querySelector('[data-auth-required]');
            if (dashboardLink) {
                dashboardLink.addEventListener('click', (event) => {
                    if (sessionActive) {
                        return;
                    }
                    event.preventDefault();
                    openModal('dashboard', dashboardLink.getAttribute('href'));
                });
            }

            const authMenu = document.getElementById('auth-menu');
            const authMenuText = document.getElementById('auth-menu-text');
            const authMenuCaret = document.getElementById('auth-menu-caret');

            const impactoInput = document.getElementById('impacto_input');
            const impactoButtons = document.querySelectorAll('.impacto-btn');

            function setImpacto(value) {
                impactoInput.value = value || '';
                impactoButtons.forEach((button) => {
                    if (button.dataset.impacto === value) {
                        button.classList.add('bg-[#F4F7EE]', 'border-[#6B8E23]', 'text-[#6B8E23]');
                    } else {
                        button.classList.remove('bg-[#F4F7EE]', 'border-[#6B8E23]', 'text-[#6B8E23]');
                    }
                });
            }

            impactoButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    if (impactoInput.value === button.dataset.impacto) {
                        setImpacto('');
                    } else {
                        setImpacto(button.dataset.impacto);
                    }
                });
            });

            if (impactoInput.value) {
                setImpacto(impactoInput.value);
            }


            function closeModal() {
                modal.classList.add('hidden');
                modal.setAttribute('aria-hidden', 'true');
                readyToSubmit = false;
            }

            function showAuthLoading() {
                if (!authLoading) return;
                authLoading.classList.remove('hidden');
                authLoading.classList.add('flex');
            }

            function hideAuthLoading() {
                if (!authLoading) return;
                authLoading.classList.add('hidden');
                authLoading.classList.remove('flex');
            }

            ticketForm.addEventListener('submit', (event) => {
                if (isSubmitting) {
                    event.preventDefault();
                    return;
                }
                if (locacionSelect && !locacionSelect.value) {
                    event.preventDefault();
                    showToast('Selecciona una locación.');
                    openLocacionModal();
                    return;
                }
                if (!requiresAuth || readyToSubmit) {
                    event.preventDefault();
                    submitTicketAjax();
                    return;
                }
                event.preventDefault();
                saveDraft();
                localStorage.setItem(submitAfterLoginKey, '1');
                openModal('ticket');
            });

            function showToast(message, type = 'error') {
                if (window.showToast) {
                    window.showToast(type, message);
                }
            }

            async function submitTicketAjax() {
                if (isSubmitting) {
                    return;
                }
                setSubmitting(true);

                const formData = new FormData(ticketForm);
                try {
                    const response = await fetch(ticketForm.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json'
                        },
                        body: formData,
                        credentials: 'same-origin',
                    });

                    if (response.status === 423) {
                        const payload = await response.json().catch(() => ({}));
                        showToast(payload.message || 'Debes cambiar tu clave provisoria.');
                        setSubmitting(false);
                        hideAuthLoading();
                        if (payload.redirect) {
                            window.location.href = payload.redirect;
                        }
                        return;
                    }

                    if (!response.ok) {
                        const payload = await response.json().catch(() => ({}));
                        showToast(payload.message || 'No se pudo enviar el ticket.');
                        setSubmitting(false);
                        hideAuthLoading();
                        return;
                    }

                    const payload = await response.json();
                    clearDraft();
                    ticketForm.reset();
                    setImpacto('');
                    if (locacionSelect && locacionLabel) {
                        locacionSelect.value = '';
                        locacionLabel.textContent = 'Seleccione una locación';
                    }
                    closeModal();
                    sessionActive = true;
                    requiresAuth = false;
                    if (authMenu) {
                        authMenu.dataset.authenticated = 'true';
                        if (authMenu.__x) {
                            authMenu.__x.$data.authenticated = true;
                            authMenu.__x.$data.open = false;
                        }
                    }
                    showToast(`Ticket enviado correctamente con el ID: ${payload.ticket_id}`, 'success');
                    setSubmitting(false);
                    hideAuthLoading();
                } catch (error) {
                    showToast('No se pudo enviar el ticket.');
                    setSubmitting(false);
                    hideAuthLoading();
                }
            }

            if (outlookButton) {
                outlookButton.addEventListener('click', () => {
                    if (authContext === 'ticket') {
                        saveDraft();
                        localStorage.setItem(submitAfterLoginKey, '1');
                    }
                    showAuthLoading();
                });
            }

            cancelButton.addEventListener('click', closeModal);

            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeModal();
                }
            });

            window.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeModal();
                }
            });

            restoreDraft();
            if (sessionActive && localStorage.getItem(submitAfterLoginKey) === '1') {
                localStorage.removeItem(submitAfterLoginKey);
                submitTicketAjax();
            }

        })();
    </script>
</x-layouts.clean>
