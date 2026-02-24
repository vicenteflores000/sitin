<x-layouts.clean>
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

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="email">Correo</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('email')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="password">Clave</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('password')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center text-sm text-gray-600">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#6B8E23] focus:ring-[#6B8E23]" name="remember">
                            <span class="ml-2">Recordarme</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm text-gray-500 hover:text-gray-700">
                                Olvidé mi contraseña
                            </a>
                        @endif
                    </div>

                    <button type="submit"
                        class="w-full rounded-xl border border-[#6B8E23] px-4 py-2 text-sm font-medium text-[#6B8E23] bg-[#F4F7EE] hover:bg-[#E9F0DF] transition">
                        Iniciar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.clean>
