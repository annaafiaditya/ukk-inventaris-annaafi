<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Item') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 border-b border-gray-200 font-semibold text-lg flex justify-between items-center">
                    <span>Edit Item</span>
                    <a href="{{ route('items.index') }}" class="text-sm font-normal text-blue-600 hover:text-blue-800">&larr; Kembali ke Daftar</a>
                </div>
                <div class="p-6">
                    @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Berhasil!</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                    @endif
                    @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Kesalahan!</strong>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                    @endif
                    @if($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Validasi gagal:</strong>
                        <ul class="list-disc list-inside mt-2 text-sm">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form method="POST" action="{{ route('items.update', $item) }}" novalidate>
                        @csrf
                        @method('PUT')
                        <div class="space-y-6 max-w-2xl">
                            <div>
                                <x-input-label for="nama" value="Nama Item" />
                                <x-text-input id="nama" class="block mt-1 w-full" type="text" name="nama" :value="old('nama', $item->nama)" required autofocus />
                                <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="category_id" value="Kategori" />
                                <select id="category_id" name="category_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>{{ $category->nama_kategori }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="total" value="Total Items" />
                                <x-text-input id="total" class="block mt-1 w-full" type="number" name="total" :value="old('total', $item->total)" min="0" required />
                                <x-input-error :messages="$errors->get('total')" class="mt-2" />
                            </div>
                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
                                <div>
                                    <x-input-label for="rusak">
                                        Rusak <span class="text-xs text-gray-500 font-normal ml-1">(tersedia: {{ $item->total - $item->diperbaiki - $item->peminjaman }})</span>
                                    </x-input-label>
                                    <x-text-input id="rusak" class="block mt-1 w-full" type="number" name="rusak" :value="old('rusak', 0)" min="0" />
                                    <p class="text-xs text-gray-500 mt-1">Tambah jumlah barang rusak saat ini.</p>
                                    <x-input-error :messages="$errors->get('rusak')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="sudah_diperbaiki">
                                        Sudah Diperbaiki <span class="text-xs text-gray-500 font-normal ml-1">(rusak: {{ $item->diperbaiki }})</span>
                                    </x-input-label>
                                    <x-text-input id="sudah_diperbaiki" class="block mt-1 w-full" type="number" name="sudah_diperbaiki" :value="old('sudah_diperbaiki', 0)" min="0" />
                                    <p class="text-xs text-gray-500 mt-1">Kurangi rusak dan tambahkan kembali ke total.</p>
                                    <x-input-error :messages="$errors->get('sudah_diperbaiki')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="hilang">
                                        Hilang <span class="text-xs text-gray-500 font-normal ml-1">(sekarang: {{ $item->hilang ?? 0 }})</span>
                                    </x-input-label>
                                    <x-text-input id="hilang" class="block mt-1 w-full" type="number" name="hilang" :value="old('hilang', 0)" min="0" />
                                    <p class="text-xs text-gray-500 mt-1">Kurangi total dan tambahkan ke hilang.</p>
                                    <x-input-error :messages="$errors->get('hilang')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="ketemu">
                                        Ketemu <span class="text-xs text-gray-500 font-normal ml-1">(hilang: {{ $item->hilang ?? 0 }})</span>
                                    </x-input-label>
                                    <x-text-input id="ketemu" class="block mt-1 w-full" type="number" name="ketemu" :value="old('ketemu', 0)" min="0" />
                                    <p class="text-xs text-gray-500 mt-1">Mengurangi hilang dan menambahkan kembali ke total.</p>
                                    <x-input-error :messages="$errors->get('ketemu')" class="mt-2" />
                                </div>
                            </div>
                            <div class="pt-4 border-t border-gray-100 flex justify-end">
                                <x-primary-button>
                                    Simpan Perubahan
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
