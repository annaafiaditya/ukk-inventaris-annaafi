<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Riwayat Peminjaman Item: {{ $item->nama }}
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
                <div class="p-6 text-gray-900 border-b border-gray-200 font-semibold text-lg flex items-center justify-between">
                    <span>Detail Peminjaman untuk {{ $item->nama }}</span>
                    <a href="{{ route('items.index') }}" class="text-sm text-blue-600 hover:text-blue-900">Kembali ke daftar item</a>
                </div>
                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-left table-auto min-w-max">
                        <thead>
                            <tr>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">#</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">Nama Peminjam</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-center">Jumlah</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">Keterangan</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-center">Tanggal Pinjam</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-center">Status</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50 text-center">Tanggal Kembali</th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lendings as $lending)
                            <tr class="hover:bg-gray-50">
                                <td class="p-4 border-b border-blue-gray-50">{{ $loop->iteration }}</td>
                                <td class="p-4 border-b border-blue-gray-50 font-semibold">{{ $lending->nama_peminjam }}</td>
                                <td class="p-4 border-b border-blue-gray-50 text-center font-bold text-blue-600">{{ $lending->total }}</td>
                                <td class="p-4 border-b border-blue-gray-50">{{ $lending->keterangan ?? '-' }}</td>
                                <td class="p-4 border-b border-blue-gray-50 text-center">{{ $lending->tanggal_pinjam ? $lending->tanggal_pinjam->format('d M Y') : '-' }}</td>
                                <td class="p-4 border-b border-blue-gray-50 text-center">
                                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $lending->status === 'belum' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                                        {{ $lending->status === 'belum' ? 'Belum kembali' : 'Sudah kembali' }}
                                    </span>
                                </td>
                                <td class="p-4 border-b border-blue-gray-50 text-center">{{ $lending->tanggal_kembali ? $lending->tanggal_kembali->format('d M Y') : '-' }}</td>
                                <td class="p-4 border-b border-blue-gray-50">{{ $lending->user->name ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="p-4 text-center text-gray-500">Belum ada data peminjaman untuk item ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
