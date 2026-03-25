<x-layouts.clean>
    <div class="w-full h-screen flex flex-col items-center px-4 bg-[#FAFAF7] overflow-hidden">
        <div class="w-full max-w-md py-8 flex flex-col justify-center" style="height: calc(100vh - 2rem);">

            <div class="mb-6 text-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Tickets TI" class="theme-logo-light mx-auto h-12" style="width: 200px; height: auto;">
                <img src="{{ asset('images/logo-white.png') }}" alt="Logo Tickets TI" class="theme-logo-dark mx-auto h-12" style="width: 200px; height: auto;">
                <p class="text-gray-600">Crear nueva contraseña</p>
            </div>

            <div class="bg-white rounded-xl shadow-xl border border-gray-200 p-6">
                <form method="POST" action="{{ route('password.store', $request->route('token')) }}" class="space-y-4">
                    @csrf

                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                    <input type="hidden" name="email" value="{{ old('email', $request->email) }}">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="password">Nueva contraseña</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('password')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="password_confirmation">Confirmar contraseña</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('password_confirmation')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit"
                            class="rounded-xl border border-[#6B8E23] px-6 py-2 text-sm font-medium text-[#6B8E23] bg-[#F4F7EE] hover:bg-[#E9F0DF] transition">
                            Restablecer clave
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.clean>
