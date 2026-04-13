<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Kategori') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 border-b border-gray-200 font-semibold text-lg flex justify-between items-center">
                    <span>Edit Kategori</span>
                    <a href="{{ route('categories.index') }}" class="text-sm font-normal text-blue-600 hover:text-blue-800">&larr; Kembali ke Daftar</a>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('categories.update', $category) }}">
                        @csrf
                        @method('PUT')
                        <div class="space-y-6 max-w-2xl">
                            <div>
                                <x-input-label for="nama_kategori" value="Nama Kategori" />
                                <x-text-input id="nama_kategori" class="block mt-1 w-full" type="text" name="nama_kategori" :value="old('nama_kategori', $category->nama_kategori)" required autofocus />
                                <x-input-error :messages="$errors->get('nama_kategori')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="divisi_pj" value="Divisi Penanggung Jawab" />
                                <select id="divisi_pj" name="divisi_pj" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="sarpras" {{ old('divisi_pj', $category->divisi_pj) === 'sarpras' ? 'selected' : '' }}>Sarpras</option>
                                    <option value="tata usaha" {{ old('divisi_pj', $category->divisi_pj) === 'tata usaha' ? 'selected' : '' }}>Tata Usaha</option>
                                    <option value="tefa" {{ old('divisi_pj', $category->divisi_pj) === 'tefa' ? 'selected' : '' }}>Tefa</option>
                                </select>
                                <x-input-error :messages="$errors->get('divisi_pj')" class="mt-2" />
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
