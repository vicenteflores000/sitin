<x-layouts.clean>
    @push('head')
        <style>
            @keyframes dotPulse {
                0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
                40% { transform: scale(1); opacity: 1; }
            }
            .login-dot {
                width: 10px;
                height: 10px;
                border-radius: 9999px;
                display: inline-block;
                animation: dotPulse 1.2s infinite ease-in-out;
            }
        </style>
    @endpush
    <div class="w-full h-screen flex flex-col items-center px-4 bg-[#FAFAF7] overflow-hidden">
        <div class="w-full max-w-md py-8 flex flex-col justify-center" style="height: calc(100vh - 2rem);">

            <div class="mb-6 text-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Tickets TI" class="mx-auto h-12" style="width: 200px; height: auto;">
                <p class="text-gray-600">Acceso para funcionarios</p>
            </div>

            <div class="bg-white rounded-xl shadow-xl border border-gray-200 p-6">
                @if (session('status'))
                    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="space-y-4" id="login-form">
                    <a href="{{ route('auth.microsoft.redirect') }}" id="login-outlook"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="#F25022" d="M1 1h10v10H1z" />
                            <path fill="#7FBA00" d="M13 1h10v10H13z" />
                            <path fill="#00A4EF" d="M1 13h10v10H1z" />
                            <path fill="#FFB900" d="M13 13h10v10H13z" />
                        </svg>
                        Iniciar con Outlook
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div id="login-loading" class="fixed inset-0 hidden items-center justify-center bg-black/80 backdrop-blur-sm z-50">
        <div class="text-center text-white">
            <div class="flex items-center justify-center gap-2 mb-4">
                <span class="login-dot bg-[#6B8E23]" style="animation-delay: 0s;"></span>
                <span class="login-dot bg-[#6B8E23]" style="animation-delay: 0.2s;"></span>
                <span class="login-dot bg-[#6B8E23]" style="animation-delay: 0.4s;"></span>
            </div>
            <div class="text-sm tracking-wide">Iniciando su sesión, espere un momento</div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const outlook = document.getElementById('login-outlook');
                const overlay = document.getElementById('login-loading');

                const showOverlay = () => {
                    if (!overlay) return;
                    overlay.classList.remove('hidden');
                    overlay.classList.add('flex');
                };

                if (outlook) {
                    outlook.addEventListener('click', showOverlay);
                }
            });
        </script>
    @endpush
</x-layouts.clean>
