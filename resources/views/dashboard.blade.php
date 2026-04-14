<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-2">Selamat Datang, {{ Auth::user()->name }}!</h3>
                    <p>Anda login sebagai <span class="font-semibold px-2 py-1 bg-blue-100 text-blue-800 rounded">{{ ucfirst(Auth::user()->role) }}</span>.</p>
                </div>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-blue-500">
                    <div class="p-6">
                        <p class="text-sm text-gray-500">Total Item</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalItems) }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-green-500">
                    <div class="p-6">
                        <p class="text-sm text-gray-500">Kategori</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalCategories) }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-indigo-500">
                    <div class="p-6">
                        <p class="text-sm text-gray-500">Total Peminjaman</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalLendings) }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-yellow-500">
                    <div class="p-6">
                        <p class="text-sm text-gray-500">Sedang Dipinjam</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($activeLendings) }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-red-500">
                    <div class="p-6">
                        <p class="text-sm text-gray-500">Total Sedang Diperbaiki</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalRepair) }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-gray-500">
                    <div class="p-6">
                        <p class="text-sm text-gray-500">Total Hilang</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalLost) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
