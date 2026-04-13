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
                            <div>
                                <x-input-label for="baru_rusak">
                                    Baru Rusak <span class="text-xs text-gray-500 font-normal ml-1">(sekarang: {{ $item->diperbaiki }})</span>
                                </x-input-label>
                                <x-text-input id="baru_rusak" class="block mt-1 w-full" type="number" name="baru_rusak" :value="old('baru_rusak', 0)" min="0" />
                                <p class="text-xs text-gray-500 mt-1">Jumlah yang dimasukkan di sini akan ditambahkan ke total diperbaiki saat ini.</p>
                                <x-input-error :messages="$errors->get('baru_rusak')" class="mt-2" />
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
