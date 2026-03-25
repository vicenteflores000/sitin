<x-layouts.clean>
    <div class="w-full h-screen flex flex-col items-center px-4 bg-[#FAFAF7] overflow-hidden">
        <div class="w-full max-w-md py-8 flex flex-col justify-center" style="height: calc(100vh - 2rem);">

            <div class="mb-6 text-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Tickets TI" class="theme-logo-light mx-auto h-12" style="width: 200px; height: auto;">
                <img src="{{ asset('images/logo-white.png') }}" alt="Logo Tickets TI" class="theme-logo-dark mx-auto h-12" style="width: 200px; height: auto;">
                <p class="text-gray-600">Restablecer clave</p>
            </div>

            <div class="bg-white rounded-xl shadow-xl border border-gray-200 p-6">
                @if (session('status'))
                    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                        {{ session('status') }}
                    </div>
                @endif

                <p class="mb-4 text-sm text-gray-600">
                    Ingresa tu correo y te enviaremos un enlace para restablecer tu clave.
                </p>

                <form method="POST" action="{{ route('password.email') }}" class="space-y-4" id="reset-request-form">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="email">Correo</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('email')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit" id="reset-request-submit"
                            class="rounded-xl border border-[#6B8E23] px-6 py-2 text-sm font-medium text-[#6B8E23] bg-[#F4F7EE] hover:bg-[#E9F0DF] transition">
                            Enviar enlace
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('reset-request-form');
                const button = document.getElementById('reset-request-submit');
                if (!form || !button) return;

                form.addEventListener('submit', () => {
                    button.disabled = true;
                    button.classList.add('opacity-70', 'cursor-not-allowed');
                    button.textContent = 'Enviando...';
                });
            });
        </script>
    @endpush
</x-layouts.clean>
