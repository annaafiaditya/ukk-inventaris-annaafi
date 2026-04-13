<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Sistem Inventaris</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,600,800&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body {
                font-family: 'Inter', sans-serif;
            }
            .bg-gradient {
                background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            }
            .glassmorphism {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            .hover-scale {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }
            .hover-scale:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            }
        </style>
    </head>
    <body class="antialiased bg-gray-50 text-gray-900">
        <!-- Navbar -->
        <nav class="fixed w-full z-50 transition-all duration-300 py-4 glassmorphism" style="background: rgba(255, 255, 255, 0.8);">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-12">
                    <div class="flex-shrink-0 flex items-center gap-2">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-lg">
                            SI
                        </div>
                        <span class="font-bold text-xl tracking-tight text-blue-900">Sistem Inventaris</span>
                    </div>
                    <div>
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-medium rounded-full text-white bg-blue-600 hover:bg-blue-700 hover-scale shadow-md">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-medium rounded-full text-white bg-blue-600 hover:bg-blue-700 hover-scale shadow-md">
                                    Login
                                </a>
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <main class="relative bg-gradient overflow-hidden min-h-screen flex items-center justify-center pt-20">
            <!-- Decorative blobs -->
            <div class="absolute top-0 left-0 w-96 h-96 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
            <div class="absolute top-0 right-0 w-96 h-96 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-32 left-20 w-96 h-96 bg-indigo-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
                <h1 class="text-5xl md:text-7xl font-extrabold text-white tracking-tight mb-8">
                    Kelola <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-200 to-cyan-200">Aset</span> Anda<br/>dengan Cerdas.
                </h1>
                <p class="mt-4 text-xl text-blue-100 max-w-3xl mx-auto mb-10 leading-relaxed">
                    Sistem inventaris modern dan terintegrasi untuk melacak, mengelola, dan mengoptimalkan seluruh aset perusahaan Anda dengan mudah dan aman.
                </p>
                <div class="flex justify-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-8 py-4 text-lg font-bold rounded-full bg-white text-blue-700 hover:text-blue-900 hover-scale shadow-xl border border-white">
                                Ke Dashboard Saya
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-8 py-4 text-lg font-bold rounded-full bg-white text-blue-700 hover:text-blue-900 hover-scale shadow-xl border border-white">
                                Mulai Sekarang
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </main>

        <!-- Features Section -->
        <section class="py-24 bg-white relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12 text-center">
                    <div class="p-8 rounded-2xl bg-gray-50 border border-gray-100 hover-scale">
                        <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-6 text-blue-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Keamanan Optimal</h3>
                        <p class="text-gray-600">Diakses hanya oleh user terdaftar dalam role admin maupun staff.</p>
                    </div>
                    <div class="p-8 rounded-2xl bg-gray-50 border border-gray-100 hover-scale">
                        <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-6 text-purple-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Analisis Mudah</h3>
                        <p class="text-gray-600">Pantau pergerakan stok, barang masuk, dan barang keluar secara akurat.</p>
                    </div>
                    <div class="p-8 rounded-2xl bg-gray-50 border border-gray-100 hover-scale">
                        <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-6 text-green-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Role Based Access</h3>
                        <p class="text-gray-600">Sistem tersegregasi untuk Admin dan Staff demi kenyamanan dan kontrol penuh.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-gray-400 py-12 text-center">
            <p>&copy; 2026 Sistem Inventaris. Dilindungi oleh hak cipta(annaafi).</p>
        </footer>
    </body>
</html>
