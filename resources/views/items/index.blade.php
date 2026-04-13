<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kelola Items
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

            @if(Auth::user()->role === 'admin')
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 border-b border-gray-200 font-semibold text-lg">
                    Tambah Item Baru
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('items.store') }}" novalidate>
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <x-input-label for="nama" value="Nama Item" />
                                <x-text-input id="nama" class="block mt-1 w-full" type="text" name="nama" :value="old('nama')" required autofocus />
                                <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="category_id" value="Kategori" />
                                <select id="category_id" name="category_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>Pilih Kategori</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->nama_kategori }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="total" value="Total Items" />
                                <x-text-input id="total" class="block mt-1 w-full" type="number" name="total" :value="old('total', 0)" min="0" required />
                                <x-input-error :messages="$errors->get('total')" class="mt-2" />
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <x-primary-button>
                                Tambah Item
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 border-b border-gray-200 font-semibold text-lg">
                    Daftar Items
                </div>
                <div class="p-6 overflow-x-auto">
                    @if(Auth::user()->role === 'admin')
                    <div class="mb-4 flex justify-end">
                        <a href="{{ route('items.export') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Export Excel
                        </a>
                    </div>
                    @endif
                    <table class="w-full text-left table-auto min-w-max">
                        <thead>
                            <tr>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">#</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">Kategori</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">Nama Item</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-center">Total</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-center">Diperbaiki</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-center">Tersedia</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-center">Total Peminjaman</th>
                                @if(Auth::user()->role === 'admin')
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-right">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="p-4 border-b border-blue-gray-50">{{ $loop->iteration }}</td>
                                <td class="p-4 border-b border-blue-gray-50">
                                    <span class="px-2 py-1 rounded text-xs font-semibold bg-gray-100 text-gray-800">
                                        {{ $item->category->nama_kategori ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="p-4 border-b border-blue-gray-50">{{ $item->nama }}</td>
                                <td class="p-4 border-b border-blue-gray-50 text-center font-bold">
                                    {{ $item->total }}
                                </td>
                                <td class="p-4 border-b border-blue-gray-50 text-center font-bold text-orange-600">
                                    {{ $item->diperbaiki }}
                                </td>
                                <td class="p-4 border-b border-blue-gray-50 text-center font-bold text-green-600">
                                    {{ $item->total - $item->diperbaiki - $item->peminjaman }}
                                </td>
                                @php
                                    $activeBorrowed = $item->lendings->where('status', 'belum')->sum('total');
                                @endphp
                                <td class="p-4 border-b border-blue-gray-50 text-center font-bold text-blue-600">
                                    @if(Auth::user()->role === 'admin' && $activeBorrowed > 0)
                                        <a href="{{ route('items.lendings', $item) }}" class="underline hover:text-blue-800" title="Lihat Detail Peminjaman">
                                            {{ $activeBorrowed }}
                                        </a>
                                    @else
                                        {{ $activeBorrowed }}
                                    @endif
                                </td>
                                @if(Auth::user()->role === 'admin')
                                <td class="p-4 border-b border-blue-gray-50 text-right">
                                    <a href="{{ route('items.edit', $item) }}" class="text-sm text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                    <form action="{{ route('items.destroy', $item) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-900" onclick="return confirm('Hapus item ini?')">Hapus</button>
                                    </form>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ Auth::user()->role === 'admin' ? 8 : 7 }}" class="p-4 text-center text-gray-500">Belum ada data item.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
