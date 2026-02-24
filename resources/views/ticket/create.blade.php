<x-layouts.clean>
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

            @if (session('success'))
            <div
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 4500)"
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                class="fixed top-4 right-4 z-50 max-w-sm w-full sm:w-auto">
                <div class="flex items-start gap-3 p-4 bg-green-100 border border-green-200 text-green-800 rounded-lg shadow-lg">
                    <svg class="w-5 h-5 mt-0.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 13l4 4L19 7" />
                    </svg>
                    <p class="text-sm">{{ session('success') }}</p>
                    <button @click="show = false" class="ml-auto text-green-700 hover:text-green-900">✕</button>
                </div>
            </div>
            @endif

            <div id="toast-message" class="fixed top-4 right-4 z-[60] max-w-sm w-full sm:w-auto hidden">
                <div id="toast-body" class="flex items-start gap-3 p-4 rounded-lg shadow-lg border">
                    <svg id="toast-icon" class="w-5 h-5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p id="toast-text" class="text-sm"></p>
                <button id="toast-close" class="ml-auto">✕</button>
            </div>
            </div>

            <div class="bg-white rounded-xl shadow-xl border border-gray-200 p-6 flex flex-col">

                @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form id="ticket-form" method="POST" action="/ticket">
                    @csrf
                    <input type="hidden" name="auth_email" id="auth_email_hidden" value="{{ old('auth_email') }}">
                    <input type="hidden" name="auth_password" id="auth_password_hidden">

                    <div class="grid grid-cols-1 gap-4 pr-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción breve del problema</label>
                            <textarea name="descripcion" maxlength="300" rows="4" required
                                placeholder="Describa el problema de forma breve y clara"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('descripcion') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                            <select name="categoria" required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccione...</option>
                                <option value="Computador" @selected(old('categoria') === 'Computador')>Computador</option>
                                <option value="Impresora" @selected(old('categoria') === 'Impresora')>Impresora</option>
                                <option value="Internet" @selected(old('categoria') === 'Internet')>Internet</option>
                                <option value="Sistema" @selected(old('categoria') === 'Sistema')>Sistema</option>
                                <option value="Correo" @selected(old('categoria') === 'Correo')>Correo</option>
                                <option value="Telefonia" @selected(old('categoria') === 'Telefonia')>Telefonía</option>
                                <option value="Otro" @selected(old('categoria') === 'Otro')>Otro</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ubicación</label>
                            <select name="locacion_id" id="locacion_select" data-enhanced-select required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccione...</option>
                                @foreach ($locaciones as $locacion)
                                <option value="{{ $locacion->id }}"
                                    data-label="{{ $locacion->padre ? $locacion->padre->nombre . ' - ' : '' }}{{ $locacion->nombre }}"
                                    @selected(old('locacion_id') == $locacion->id)>
                                    {{ $locacion->padre ? $locacion->padre->nombre . ' - ' : '' }}{{ $locacion->nombre }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de solicitud</label>
                            <select name="tipo" required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccione...</option>
                                <option value="Soporte" @selected(old('tipo') === 'Soporte')>Soporte</option>
                                <option value="Administrativo" @selected(old('tipo') === 'Administrativo')>Administrativo</option>
                                <option value="Mejora" @selected(old('tipo') === 'Mejora')>Mejora</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Impacto (opcional)</label>
                            <input type="hidden" name="impacto" id="impacto_input" value="{{ old('impacto') }}">
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" data-impacto="No impide trabajar"
                                    class="impacto-btn rounded-lg border border-gray-300 px-3 py-2 text-xs text-gray-700 hover:bg-gray-50">
                                    No impide
                                </button>
                                <button type="button" data-impacto="Dificulta el trabajo"
                                    class="impacto-btn rounded-lg border border-gray-300 px-3 py-2 text-xs text-gray-700 hover:bg-gray-50">
                                    Dificulta
                                </button>
                                <button type="button" data-impacto="Impide atender usuarios"
                                    class="impacto-btn rounded-lg border border-gray-300 px-3 py-2 text-xs text-gray-700 hover:bg-gray-50">
                                    Impide
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
            <p class="text-sm text-gray-500 mb-4">Necesitamos que inicie sesión para poder continuar.</p>

            <label for="auth_email_input" class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
            <input type="email" id="auth_email_input" autocomplete="email" value="{{ old('auth_email') }}" required
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

            <label for="auth_password_input" class="block text-sm font-medium text-gray-700 mb-1 mt-3">Clave</label>
            <input type="password" id="auth_password_input" autocomplete="current-password" required
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

            <div class="mt-2 text-right">
                <a href="{{ route('password.request') }}"
                    class="text-xs text-gray-500 hover:text-gray-700">
                    Olvidé mi contraseña
                </a>
            </div>

            <div class="mt-4 flex items-center gap-2">
                <button type="button" id="auth-cancel"
                    class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="button" id="auth-submit"
                    class="flex-1 rounded-lg bg-blue-600 px-3 py-2 text-sm text-white hover:bg-blue-700 transition">
                    Enviar
                </button>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const ticketForm = document.getElementById('ticket-form');
            const modal = document.getElementById('auth-modal');
            const authEmailInput = document.getElementById('auth_email_input');
            const authPasswordInput = document.getElementById('auth_password_input');
            const authEmailHidden = document.getElementById('auth_email_hidden');
            const authPasswordHidden = document.getElementById('auth_password_hidden');
            const cancelButton = document.getElementById('auth-cancel');
            const submitButton = document.getElementById('auth-submit');
            const ticketSubmit = document.getElementById('ticket-submit');
            const ticketSubmitText = document.getElementById('ticket-submit-text');
            let requiresAuth = {{ auth()->check() ? 'false' : 'true' }};
            let readyToSubmit = false;
            let authContext = 'ticket';
            let authRedirect = null;
            let sessionActive = !requiresAuth;
            let isSubmitting = false;
            let submittingInterval = null;
            const submittingFrames = ['Enviando', 'Enviando .', 'Enviando ..', 'Enviando ...', 'Enviando ..', 'Enviando .'];
            let submittingIndex = 0;

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
                authEmailInput.focus();
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
                authPasswordInput.value = '';
                readyToSubmit = false;
            }

            ticketForm.addEventListener('submit', (event) => {
                if (isSubmitting) {
                    event.preventDefault();
                    return;
                }
                if (!requiresAuth || readyToSubmit) {
                    event.preventDefault();
                    submitTicketAjax();
                    return;
                }
                event.preventDefault();
                openModal('ticket');
            });

            function showToast(message, type = 'error') {
                const toast = document.getElementById('toast-message');
                const body = document.getElementById('toast-body');
                const text = document.getElementById('toast-text');
                const icon = document.getElementById('toast-icon');
                const close = document.getElementById('toast-close');

                text.textContent = message;
                body.className = 'flex items-start gap-3 p-4 rounded-lg shadow-lg border';
                icon.className = 'w-5 h-5 mt-0.5';

                if (type === 'success') {
                    body.classList.add('bg-green-100', 'border-green-200', 'text-green-800');
                    icon.classList.add('text-green-600');
                    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />';
                } else {
                    body.classList.add('bg-red-100', 'border-red-200', 'text-red-800');
                    icon.classList.add('text-red-600');
                    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
                }

                toast.classList.remove('hidden');
                close.onclick = () => toast.classList.add('hidden');

                setTimeout(() => {
                    toast.classList.add('hidden');
                }, 3000);
            }

            async function authenticateOnly() {
                const tokenInput = ticketForm.querySelector('input[name="_token"]');
                const formData = new FormData();
                formData.append('_token', tokenInput ? tokenInput.value : '');
                formData.append('email', authEmailInput.value.trim());
                formData.append('password', authPasswordInput.value);

                const response = await fetch('/login', {
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
                    if (payload.redirect) {
                        window.location.href = payload.redirect;
                    }
                    return false;
                }

                if (response.status === 422) {
                    const payload = await response.json().catch(() => ({}));
                    showToast(payload.message || 'Correo o clave inválidos.');
                    return false;
                }

                if (!response.ok) {
                    showToast('No se pudo iniciar sesión.');
                    return false;
                }

                return true;
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
                        if (payload.redirect) {
                            window.location.href = payload.redirect;
                        }
                        return;
                    }

                    if (!response.ok) {
                        const payload = await response.json().catch(() => ({}));
                        showToast(payload.message || 'No se pudo enviar el ticket.');
                        setSubmitting(false);
                        return;
                    }

                    const payload = await response.json();
                    ticketForm.reset();
                    authPasswordHidden.value = '';
                    setImpacto('');
                    const locacionSelect = document.getElementById('locacion_select');
                    if (window.$ && window.$.fn && window.$.fn.select2 && locacionSelect) {
                        window.$(locacionSelect).val(null).trigger('change');
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
                    if (authMenuText) {
                        authMenuText.textContent = authEmailInput.value.trim() || authMenuText.textContent;
                    }
                    showToast(`Ticket enviado correctamente con el ID: ${payload.ticket_id}`, 'success');
                    setSubmitting(false);
                } catch (error) {
                    showToast('No se pudo enviar el ticket.');
                    setSubmitting(false);
                }
            }

            submitButton.addEventListener('click', async () => {
                if (!authEmailInput.value.trim() || !authPasswordInput.value.trim()) {
                    return;
                }

                authEmailHidden.value = authEmailInput.value.trim();
                authPasswordHidden.value = authPasswordInput.value;

                if (authContext !== 'ticket') {
                    if (isSubmitting) {
                        return;
                    }
                    isSubmitting = true;
                    submitButton.disabled = true;
                    const ok = await authenticateOnly();
                    if (!ok) {
                        isSubmitting = false;
                        submitButton.disabled = false;
                        return;
                    }

                    sessionActive = true;
                    requiresAuth = false;

                    closeModal();
                    if (authContext === 'dashboard') {
                        window.location.href = authRedirect || '/dashboard';
                        return;
                    }

                    if (authMenu) {
                        authMenu.dataset.authenticated = 'true';
                        if (authMenu.__x) {
                            authMenu.__x.$data.authenticated = true;
                            authMenu.__x.$data.open = false;
                        }
                    }
                    if (authMenuText) {
                        authMenuText.textContent = authEmailInput.value.trim();
                    }

                    showToast('Sesión iniciada correctamente', 'success');
                    isSubmitting = false;
                    submitButton.disabled = false;
                    return;
                }

                if (!requiresAuth) {
                    readyToSubmit = true;
                    submitTicketAjax();
                    return;
                }

                submitTicketAjax();
            });

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

            @if ($errors->has('auth_email') || $errors->has('auth_password'))
                openModal();
            @endif
        })();
    </script>
</x-layouts.clean>
