<x-layouts.clean>
    <div class="w-full h-screen flex flex-col items-center px-4 bg-[#FAFAF7] overflow-hidden">
        <div class="w-full max-w-xl py-8 flex flex-col justify-center" style="height: calc(100vh - 2rem);">

            <div class="mb-6 text-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Tickets TI" class="mx-auto h-12" style="width: 200px; height: auto;">
                <p class="text-gray-600">Tu perfil</p>
            </div>

            <div class="bg-white rounded-xl shadow-xl border border-gray-200 p-6 flex flex-col"
                style="height: 320px; min-height: 320px; max-height: 320px;">

                <div class="flex items-center justify-between mb-4 gap-3">
                    <form action="{{ route('home') }}">
                        <button
                            class="mb-3 rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                            type="submit">
                            &larr; Volver
                        </button>
                    </form>
                </div>

                <div class="flex-1 text-sm text-gray-700 space-y-3">
                    <div>
                        <span class="text-xs uppercase tracking-wide text-gray-500">Nombre</span><br>
                        <span class="text-base font-medium text-gray-800">{{ $user->name }}</span>
                    </div>
                    <div>
                        <span class="text-xs uppercase tracking-wide text-gray-500">Correo</span><br>
                        <span class="text-base font-medium text-gray-800">{{ $user->email }}</span>
                    </div>
                    <div>
                        <span class="text-xs uppercase tracking-wide text-gray-500">Rol</span><br>
                        <span class="text-base font-medium text-gray-800">
                            {{ $user->role === 'admin' ? 'Administrador' : 'Usuario' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.clean>
