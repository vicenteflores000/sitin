<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">
                Gestión de usuarios
            </h2>
        </div>
    </x-slot>

    <div class="py-6" x-data="{ showModal: false, editingUser: null }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <button
                class="w-full mb-3 rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                @click="editingUser = null; showModal = true">
                + Crear usuario
            </button>

            {{-- Mensaje de éxito --}}
            @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
            @endif

            {{-- Tabla de usuarios --}}
            <div class="bg-white shadow rounded overflow-x-auto">
                <table class="divide-y divide-gray-200" style="width: 100%;">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase" style="text-align: left;">Nombre</th>
                            <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase" style="text-align: left;">Email</th>
                            <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase" style="text-align: left;">Rol</th>
                            <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase" style="text-align: left;">Estado</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $user)
                        <tr>
                            <td class="px-4 py-3 text-sm">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-sm">{{ $user->email }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if($user->isAdmin())
                                <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">Admin</span>
                                @else
                                <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">Usuario</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($user->active)
                                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">Activo</span>
                                @else
                                <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700">Inactivo</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <button
                                    class="text-blue-600 hover:underline"
                                    @click="editingUser = {{ $user->toJson() }}; showModal = true">
                                    Editar
                                </button>
                            </td>
                        </tr>
                        @endforeach

                        @if($users->isEmpty())
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                No hay usuarios registrados
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>


        </div>
        {{-- Modal para crear/editar --}}
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
</x-app-layout>