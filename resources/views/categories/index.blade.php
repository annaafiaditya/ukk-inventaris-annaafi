<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Kategori') }}
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 border-b border-gray-200 font-semibold text-lg">
                    Tambah Kategori Baru
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('categories.store') }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="nama_kategori" value="Nama Kategori" />
                                <x-text-input id="nama_kategori" class="block mt-1 w-full" type="text" name="nama_kategori" :value="old('nama_kategori')" required autofocus />
                                <x-input-error :messages="$errors->get('nama_kategori')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="divisi_pj" value="Divisi Penanggung Jawab" />
                                <select id="divisi_pj" name="divisi_pj" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="" disabled selected>Pilih Divisi</option>
                                    <option value="sarpras">Sarpras</option>
                                    <option value="tata usaha">Tata Usaha</option>
                                    <option value="tefa">Tefa</option>
                                </select>
                                <x-input-error :messages="$errors->get('divisi_pj')" class="mt-2" />
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <x-primary-button>
                                Tambah Kategori
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 border-b border-gray-200 font-semibold text-lg">
                    Daftar Kategori
                </div>
                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-left table-auto min-w-max">
                        <thead>
                            <tr>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">#</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">Nama Kategori</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">Divisi PJ</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-center">Total Items</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                            <tr class="hover:bg-gray-50">
                                <td class="p-4 border-b border-blue-gray-50">{{ $loop->iteration }}</td>
                                <td class="p-4 border-b border-blue-gray-50">{{ $category->nama_kategori }}</td>
                                <td class="p-4 border-b border-blue-gray-50">
                                    <span class="px-2 py-1 rounded text-xs font-semibold
                                        @if($category->divisi_pj === 'sarpras') bg-purple-100 text-purple-800
                                        @elseif($category->divisi_pj === 'tata usaha') bg-blue-100 text-blue-800
                                        @elseif($category->divisi_pj === 'tefa') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucwords($category->divisi_pj) }}
                                    </span>
                                </td>
                                <td class="p-4 border-b border-blue-gray-50 text-center font-bold">
                                    {{ $category->items->count() }}
                                </td>
                                <td class="p-4 border-b border-blue-gray-50 text-right">
                                    <a href="{{ route('categories.edit', $category) }}" class="text-sm text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-900" onclick="return confirm('Hapus kategori ini?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-gray-500">Belum ada data kategori.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
