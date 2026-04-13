<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Akun') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Berhasil!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Gagal!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 border-b border-gray-200 font-semibold text-lg">
                    Tambah Akun Baru
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <x-input-label for="name" value="Nama" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="email" value="Email" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="role" value="Role" />
                                <select id="role" name="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="staff">Staff</option>
                                    <option value="admin">Admin</option>
                                </select>
                                <x-input-error :messages="$errors->get('role')" class="mt-2" />
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <x-primary-button>
                                Buat Akun
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 border-b border-gray-200 font-semibold text-lg">
                    Daftar Akun
                </div>
                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-left table-auto min-w-max">
                        <thead>
                            <tr>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">Nama</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">Email</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">Role</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="p-4 border-b border-blue-gray-50">{{ $user->name }}</td>
                                <td class="p-4 border-b border-blue-gray-50">{{ $user->email }}</td>
                                <td class="p-4 border-b border-blue-gray-50">
                                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="p-4 border-b border-blue-gray-50 text-right">
                                    <form action="{{ route('users.reset', $user) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-sm text-yellow-600 hover:text-yellow-900 mr-3" onclick="return confirm('Reset password akun ini?')">Reset PW</button>
                                    </form>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-900" onclick="return confirm('Hapus akun ini secara permanen?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
