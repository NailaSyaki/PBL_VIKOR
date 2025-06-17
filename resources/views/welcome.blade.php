<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Platform SPK - Ambil Keputusan Lebih Cerdas</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100 text-gray-800 font-sans antialiased">

        <!-- Header Navigation -->
        <header class="bg-white shadow-md sticky top-0 z-50">
            <div class="container mx-auto px-6 py-4 flex justify-between items-center">
                <a href="{{ url('/') }}" class="text-2xl font-bold text-blue-600 hover:text-blue-700 transition-colors">
                    SPK<span class="text-teal-500">Platform</span>
                </a>
                <nav class="space-x-4">
                    @auth
                        <a
                            href="{{ url('/dashboard') }}"
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 rounded-md transition-colors"
                        >
                            Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); this.closest('form').submit();"
                                   class="px-4 py-2 text-sm font-medium text-red-500 hover:text-red-700 rounded-md transition-colors">
                                {{ __('Log Out') }}
                            </a>
                        </form>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 rounded-md transition-colors"
                        >
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="px-4 py-2 text-sm font-medium text-white bg-teal-500 hover:bg-teal-600 rounded-md shadow-sm transition-colors"
                            >
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-blue-600 via-teal-500 to-green-400 text-white py-20 md:py-32">
            <div class="container mx-auto px-6 text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight text-balance">
                    Temukan Keputusan Terbaik, Dengan Mudah.
                </h1>
                <p class="text-lg md:text-xl mb-8 max-w-2xl mx-auto text-blue-100 text-balance">
                    Sistem Pendukung Keputusan (SPK) intuitif kami memberdayakan Anda dengan metode VIKOR untuk pilihan yang tepat, lebih cepat, dan penuh keyakinan.
                </p>
                <div class="space-y-4 md:space-y-0 md:space-x-4">
                    @guest
                    <a href="{{ route('register') }}" class="inline-block bg-yellow-400 text-gray-900 font-semibold px-8 py-3 rounded-lg shadow-xl hover:bg-yellow-500 transform hover:scale-105 transition-all duration-300">
                        Get Started for Free
                    </a>
                    @endguest
                    <a href="#features" class="inline-block border-2 border-white text-white font-semibold px-8 py-3 rounded-lg shadow-xl hover:bg-white hover:text-blue-600 transform hover:scale-105 transition-all duration-300">
                        Learn More
                    </a>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-16 md:py-24 bg-white">
            <div class="container mx-auto px-6">
                <div class="text-center mb-12 md:mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Mengapa Memilih Platform Kami?</h2>
                    <p class="text-gray-600 mt-4 max-w-xl mx-auto">
                        Kami menyediakan alat yang Anda butuhkan untuk menganalisis data kompleks dan mendapatkan solusi optimal dengan metode VIKOR.
                    </p>
                </div>
                <div class="grid md:grid-cols-3 gap-8 md:gap-12">
                    <!-- Feature 1 -->
                    <div class="bg-gray-50 p-8 rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300 transform hover:-translate-y-1">
                        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 text-blue-600 mb-6 mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 100 15 7.5 7.5 0 000-15z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.865-4.865M13.5 10.5h-6" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-3 text-center">Analisis VIKOR Mendalam</h3>
                        <p class="text-gray-600 text-center text-sm">Manfaatkan algoritma VIKOR yang teruji untuk perangkingan alternatif dan solusi kompromi yang akurat.</p>
                    </div>
                    <!-- Feature 2 -->
                    <div class="bg-gray-50 p-8 rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300 transform hover:-translate-y-1">
                        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-teal-100 text-teal-600 mb-6 mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-3 text-center">Antarmuka Intuitif</h3>
                        <p class="text-gray-600 text-center text-sm">Navigasi mudah, input data alternatif & kriteria, serta visualisasi hasil yang jelas melalui dashboard ramah pengguna.</p>
                    </div>
                    <!-- Feature 3 -->
                    <div class="bg-gray-50 p-8 rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300 transform hover:-translate-y-1">
                        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-purple-100 text-purple-600 mb-6 mx-auto">
                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 12h9.75m-9.75 6h9.75M3.75 6H7.5m3 12h-.75a2.25 2.25 0 01-2.25-2.25V15m0-3V9.75A2.25 2.25 0 017.5 7.5h7.5a2.25 2.25 0 012.25 2.25v2.25M3.75 12h7.5m-7.5 6h7.5" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-3 text-center">Laporan Komprehensif</h3>
                        <p class="text-gray-600 text-center text-sm">Hasilkan laporan peringkat dan analisis VIKOR yang detail dan dapat dibagikan, sesuai kebutuhan pengambilan keputusan Anda.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action Section -->
        <section class="bg-blue-700 text-white py-16 md:py-20">
            <div class="container mx-auto px-6 text-center">
                <h2 class="text-3xl md:text-4xl font-bold mb-6 text-balance">Siap Tingkatkan Kualitas Keputusan Anda?</h2>
                <p class="text-blue-200 mb-8 max-w-lg mx-auto text-balance">
                    Bergabunglah dengan pengguna lain yang membuat keputusan lebih baik dan berbasis data setiap hari. Mulai perjalanan Anda bersama kami sekarang.
                </p>
                @guest
                <a href="{{ route('register') }}" class="bg-yellow-400 text-gray-900 font-semibold px-10 py-4 rounded-lg shadow-xl hover:bg-yellow-500 transform hover:scale-105 transition-all duration-300 text-lg">
                    Sign Up Now & Decide Smarter
                </a>
                @else
                 <a href="{{ url('/dashboard') }}" class="bg-yellow-400 text-gray-900 font-semibold px-10 py-4 rounded-lg shadow-xl hover:bg-yellow-500 transform hover:scale-105 transition-all duration-300 text-lg">
                    Go to Your Dashboard
                </a>
                @endguest
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-800 text-gray-400 py-12">
            <div class="container mx-auto px-6 text-center">
                <div class="mb-4">
                    <a href="{{ url('/') }}" class="text-xl font-semibold text-gray-200 hover:text-white transition-colors">
                        SPK<span class="text-teal-400">Platform</span>
                    </a>
                </div>
                <p class="text-sm">© {{ date('Y') }} Platform SPK. Hak Cipta Dilindungi.</p>
                <p class="text-xs mt-2">
                    Dibangun dengan <a href="https://laravel.com" target="_blank" class="underline hover:text-teal-400">Laravel</a> &
                    <a href="https://tailwindcss.com" target="_blank" class="underline hover:text-teal-400">Tailwind CSS</a>.
                </p>
                 @if (Route::has('login'))
                    <div class="mt-6 space-x-4 text-sm">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="hover:text-gray-200">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="hover:text-gray-200">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="hover:text-gray-200">Register</a>
                            @endif
                        @endauth
                         <a href="#" class="hover:text-gray-200">Kebijakan Privasi</a>
                         <a href="#" class="hover:text-gray-200">Ketentuan Layanan</a>
                    </div>
                @endif
            </div>
        </footer>

    </body>
</html>