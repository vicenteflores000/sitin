<x-layouts.clean>
    <div class="w-full h-screen flex flex-col items-center px-4 bg-[#FAFAF7] overflow-hidden">

        <div x-data="{ showModal: false, editingUser: null }" class="w-full max-w-xl py-8 flex flex-col justify-center" style="height: calc(100vh - 2rem);">

            <div class="mb-6 text-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Tickets TI" class="mx-auto h-12" style="width: 200px; height: auto;">
                <p class="text-gray-600">Administra los perfiles de usuario de la aplicación.</p>
            </div>

            <div class="bg-white rounded-xl shadow-xl border border-gray-200 p-6 flex flex-col"
                style="height: 380px; min-height: 380px; max-height: 380px;">

                {{-- Opciones --}}
                <div class="flex items-center justify-between mb-4 gap-3">
                    <div class="flex w-full items-center gap-3">
                        <form action="{{ route('dashboard') }}">
                            <button
                                class="mb-3 rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                                type="submit">
                                &larr; Volver
                            </button>
                        </form>
                        <button
                            class="mb-3 rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                            @click="editingUser = null; showModal = true">
                            + Crear usuario
                        </button>
                    </div>
                </div>


                {{-- Mensaje de éxito --}}
                @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
                @endif

                {{-- Tabla de usuarios --}}
                <div class="flex-1 overflow-y-auto" style="max-height: calc(380px - 5rem);">
                    <div class="space-y-3 pr-2">
                        @forelse($users as $user)
                        <div class="p-4 bg-gray-50 rounded-lg flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-800">{{ $user->name }}</p>
                                <p class="text-sm text-gray-600">{{ $user->email }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($user->isAdmin())
                                <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">Admin</span>
                                @else
                                <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">Usuario</span>
                                @endif
                                @if($user->active)
                                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">Activo</span>
                                @else
                                <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700">Inactivo</span>
                                @endif
                                <button
                                    class="text-blue-600 hover:underline"
                                    @click="editingUser = {{ $user->toJson() }}; showModal = true">
                                    Editar
                                </button>
                            </div>
                        </div>
                        @empty
                        <div class="p-4 text-center text-gray-500">
                            No hay usuarios registrados
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div x-show="showModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg w-96 p-6" @click.away="showModal=false; editingUser=null">
                    <h2 class="text-lg font-semibold mb-4" x-text="editingUser ? 'Editar usuario' : 'Crear usuario'"></h2>

                    <form :action="editingUser ? `/admin/profiles/${editingUser.id}` : '{{ route('admin.profiles.store') }}'"
                        method="POST">
                        @csrf
                        <template x-if="editingUser">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        {{-- Nombre --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Nombre</label>
                            <input type="text" name="name" required
                                class="w-full border rounded px-3 py-2"
                                :value="editingUser ? editingUser.name : ''">
                        </div>

                        {{-- Email (solo lectura si edición) --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Email</label>
                            <input type="email" name="email"
                                class="w-full border rounded px-3 py-2 bg-gray-100"
                                :value="editingUser ? editingUser.email : ''"
                                :readonly="editingUser">
                        </div>

                        {{-- Contraseña (solo si creación) --}}
                        <div class="mb-4" x-show="!editingUser">
                            <label class="block text-sm font-medium mb-1">Contraseña</label>
                            <input type="password" name="password" required
                                class="w-full border rounded px-3 py-2">
                        </div>

                        {{-- Rol --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Rol</label>
                            <select name="role" required class="w-full border rounded px-3 py-2"
                                :value="editingUser ? editingUser.role : 'user'">
                                <option value="user">Usuario</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        {{-- Estado --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Estado</label>
                            <select name="active" required class="w-full border rounded px-3 py-2"
                                :value="editingUser ? editingUser.active : 1">
                                <option :selected="editingUser ? editingUser.active : true" value="1">Activo</option>
                                <option :selected="editingUser ? !editingUser.active : false" value="0">Inactivo</option>
                            </select>
                        </div>

                        {{-- Botones --}}
                        <div class="flex justify-end space-x-2">
                            <button type="button"
                                class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400"
                                @click="showModal=false; editingUser=null">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700"
                                x-text="editingUser ? 'Actualizar' : 'Crear'">
                                Crear
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </x-app-layout>