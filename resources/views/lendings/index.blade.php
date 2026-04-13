<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Peminjaman Barang') }}
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
                <div class="p-6 text-gray-900 border-b border-gray-200 font-semibold text-lg border-l-4 border-blue-500">
                    Tambah Peminjaman
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('lendings.store') }}" x-data="lendingForm()">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="nama_peminjam" value="Nama Peminjam" />
                                <x-text-input id="nama_peminjam" class="block mt-1 w-full" type="text" name="nama_peminjam" :value="old('nama_peminjam')" required />
                                <x-input-error :messages="$errors->get('nama_peminjam')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="keterangan" value="Keterangan" />
                                <x-text-input id="keterangan" class="block mt-1 w-full" type="text" name="keterangan" :value="old('keterangan')" />
                                <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
                            </div>
                        </div>

                        <div class="border rounded-md p-4 mb-4 bg-gray-50">
                            <div class="font-semibold text-md mb-4 text-gray-700 flex justify-between items-center">
                                <span>Daftar Barang yang Dipinjam</span>
                                <button type="button" @click="addItem()" class="bg-blue-600 font-bold text-white px-3 py-1 rounded text-sm">
                                    + Tambah Barang
                                </button>
                            </div>
                            
                            <template x-for="(item, index) in items" :key="index">
                                <div class="flex items-end gap-4 mb-4 pb-4 border-b border-gray-200">
                                    <div class="flex-1">
                                        <x-input-label value="Pilih Item" />
                                        <select x-bind:name="'items[' + index + '][item_id]'" x-model="item.item_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                            <option value="" disabled selected>Pilih Item</option>
                                            @foreach($items as $dbItem)
                                            <option value="{{ $dbItem->id }}">{{ $dbItem->nama }} (Tersedia: {{ $dbItem->total - $dbItem->diperbaiki - $dbItem->peminjaman }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="w-32">
                                        <x-input-label value="Total" />
                                        <x-text-input x-bind:name="'items[' + index + '][total]'" x-model="item.total" class="block mt-1 w-full" type="number" min="1" required />
                                    </div>
                                    <div class="w-auto">
                                        <button type="button" @click="removeItem(index)" class="text-red-600 font-bold p-2 mb-1 border rounded">
                                            X
                                        </button>
                                    </div>
                                </div>
                            </template>
                            
                        </div>

                        <div class="mt-4 flex justify-end">
                            <x-primary-button>
                                Simpan Peminjaman
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 border-b border-gray-200 font-semibold text-lg border-l-4 border-indigo-500">
                    Daftar Peminjaman
                </div>
                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-left table-auto min-w-max">
                        <thead>
                            <tr>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-sm">#</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-sm">Item</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-sm">Total</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-sm">Nama</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-sm text-center">Tanggal</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-sm text-center">Pengembalian</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-sm">Edit Oleh</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-sm text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lendings as $lending)
                            <tr class="hover:bg-gray-50">
                                <td class="p-4 border-b border-blue-gray-50 text-sm">{{ $loop->iteration }}</td>
                                <td class="p-4 border-b border-blue-gray-50 text-sm font-semibold">{{ $lending->item->nama ?? 'Item terhapus' }}</td>
                                <td class="p-4 border-b border-blue-gray-50 text-sm text-center font-bold">{{ $lending->total }}</td>
                                <td class="p-4 border-b border-blue-gray-50 text-sm">{{ $lending->nama_peminjam }}</td>
                                <td class="p-4 border-b border-blue-gray-50 text-sm text-center">{{ \Carbon\Carbon::parse($lending->tanggal_pinjam)->format('d M Y') }}</td>
                                <td class="p-4 border-b border-blue-gray-50 text-sm text-center">
                                    @if($lending->status === 'sudah')
                                        <span class="inline-block px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                            {{ \Carbon\Carbon::parse($lending->tanggal_kembali)->format('d M Y') }}
                                        </span>
                                    @else
                                        <span class="inline-block px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">
                                            Belum
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 border-b border-blue-gray-50 text-sm">{{ $lending->user->name ?? 'Terhapus' }}</td>
                                <td class="p-4 border-b border-blue-gray-50 text-sm text-right flex items-center justify-end gap-2">
                                    @if($lending->status === 'belum')
                                    <form action="{{ route('lendings.return', $lending) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs bg-blue-500 hover:bg-blue-600 text-white font-semibold py-1 px-3 rounded drop-shadow transition" onclick="return confirm('Tandai barang telah dikembalikan?')">Pengembalian</button>
                                    </form>
                                    @endif
                                    <form action="{{ route('lendings.destroy', $lending) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-600 hover:text-white hover:bg-red-600 border border-red-600 font-semibold py-1 px-3 rounded transition" onclick="return confirm('Hapus data peminjaman ini?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="p-4 text-center text-gray-500">Belum ada data peminjaman.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    
    <script>
        function lendingForm() {
            return {
                items: [
                    { item_id: '', total: 1 }
                ],
                addItem() {
                    this.items.push({ item_id: '', total: 1 });
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                }
            }
        }
    </script>
</x-app-layout>
