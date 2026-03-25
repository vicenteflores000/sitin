<x-layouts.clean>
    <div class="w-full h-screen flex flex-col items-center px-4 bg-[#FAFAF7] overflow-hidden">
        <div class="w-full max-w-xl py-8 flex flex-col justify-center" style="height: calc(100vh - 2rem);">

            <div class="mb-6 text-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Tickets TI" class="theme-logo-light mx-auto h-12" style="width: 200px; height: auto;">
                <img src="{{ asset('images/logo-white.png') }}" alt="Logo Tickets TI" class="theme-logo-dark mx-auto h-12" style="width: 200px; height: auto;">
                <p class="text-gray-600">Editar usuario</p>
            </div>

            <div class="bg-white rounded-xl shadow-xl border border-gray-200 p-6 flex flex-col"
                style="height: 380px; min-height: 380px; max-height: 380px;">

                <div class="flex items-center justify-between mb-4 gap-3">
                    <form action="{{ route('admin.profiles.index') }}">
                        <button
                            class="mb-3 rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                            type="submit">
                            &larr; Volver
                        </button>
                    </form>
                </div>

                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.profiles.update', $user) }}" class="flex-1 flex flex-col justify-between profile-edit-form">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nueva contraseña (opcional)</label>
                            <input type="password" name="password"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700"
                                placeholder="Dejar en blanco para mantener">
                        </div>

                        {{-- Rol y estado se gestionan desde administración --}}
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="submit"
                            class="rounded-lg border border-[#6B8E23] px-4 py-2 text-sm text-[#6B8E23] hover:bg-[#F4F7EE] profile-edit-submit">
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.querySelector('.profile-edit-form');
                if (!form) return;
                form.addEventListener('submit', () => {
                    const submit = form.querySelector('.profile-edit-submit');
                    if (!submit) return;
                    submit.disabled = true;
                    submit.classList.add('opacity-70', 'cursor-not-allowed');
                    submit.textContent = 'Guardando...';
                });
            });
        </script>
    @endpush
</x-layouts.clean>
