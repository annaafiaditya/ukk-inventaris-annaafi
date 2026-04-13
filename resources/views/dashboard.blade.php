<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-2">Selamat Datang, {{ Auth::user()->name }}!</h3>
                    <p>Anda login sebagai <span class="font-semibold px-2 py-1 bg-blue-100 text-blue-800 rounded">{{ ucfirst(Auth::user()->role) }}</span>.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
