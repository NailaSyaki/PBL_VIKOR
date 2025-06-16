<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Ringkasan') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Pesan Selamat Datang --}}
            <div class="bg-slate-100 dark:bg-slate-700/50 overflow-hidden shadow-lg sm:rounded-xl border border-slate-200 dark:border-slate-600/50">
                <div class="p-6 sm:p-8">
                    <h3 class="text-2xl md:text-3xl font-bold text-slate-800 dark:text-slate-100">
                        Selamat Datang Kembali, {{ Auth::user()->name }}!
                    </h3>
                    <p class="mt-2 text-slate-600 dark:text-slate-300 text-sm md:text-base">
                        Ringkasan data Sistem Pendukung Keputusan Anda.
                    </p>
                </div>
            </div>

            {{-- Judul untuk Bagian Statistik Cepat --}}
            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 -mb-4">Statistik Cepat</h3>

            {{-- Grid untuk Card Statistik Kecil (Gaya Small Box Berwarna) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">

                {{-- Card Total Alternatif --}}
                <div class="bg-sky-500 dark:bg-sky-600 p-5 rounded-lg shadow-lg text-white relative overflow-hidden hover:bg-sky-600 dark:hover:bg-sky-700 transition-colors duration-200">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 opacity-20">
                        <svg class="w-24 h-24" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                        </svg>
                    </div>
                    <div class="relative">
                        <h3 class="text-3xl font-bold">
                            {{ $totalAlternatives ?? (\App\Models\Alternative::count()) }}
                        </h3>
                        <p class="text-sm mt-1 text-sky-100 dark:text-sky-200">Total Alternatif</p>
                    </div>
                    {{-- Link Selengkapnya jika diperlukan, bisa di-uncomment dan disesuaikan --}}
                    {{-- <a href="{{ route('alternatives.index') }}" class="absolute bottom-0 left-0 right-0 bg-sky-600 dark:bg-sky-700 hover:bg-sky-700 dark:hover:bg-sky-800 py-2 text-center text-xs font-medium transition-colors duration-150">
                        Selengkapnya <span aria-hidden="true">→</span>
                    </a> --}}
                </div>

                {{-- Card Total Kriteria --}}
                <div class="bg-emerald-500 dark:bg-emerald-600 p-5 rounded-lg shadow-lg text-white relative overflow-hidden hover:bg-emerald-600 dark:hover:bg-emerald-700 transition-colors duration-200">
                    <div class="absolute top-0 right-0 -mt-3 -mr-3 opacity-20">
                        <svg class="w-24 h-24" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                        </svg>
                    </div>
                    <div class="relative">
                        <h3 class="text-3xl font-bold">
                            {{ $totalCriteria ?? (\App\Models\Criterion::count()) }}
                        </h3>
                        <p class="text-sm mt-1 text-emerald-100 dark:text-emerald-200">Total Kriteria</p>
                    </div>
                    {{-- <a href="{{ route('criteria.index') }}" class="absolute bottom-0 left-0 right-0 bg-emerald-600 dark:bg-emerald-700 hover:bg-emerald-700 dark:hover:bg-emerald-800 py-2 text-center text-xs font-medium transition-colors duration-150">
                        Selengkapnya <span aria-hidden="true">→</span>
                    </a> --}}
                </div>

                {{-- Card Kriteria Terbaru --}}
                @php $latestCriterion = $latestCriterion ?? \App\Models\Criterion::latest()->first(); @endphp
                @if($latestCriterion)
                <div class="bg-purple-500 dark:bg-purple-600 p-5 rounded-lg shadow-lg text-white relative overflow-hidden hover:bg-purple-600 dark:hover:bg-purple-700 transition-colors duration-200">
                    <div class="absolute top-0 right-0 -mt-3 -mr-3 opacity-20">
                        <svg class="w-24 h-24" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.82.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.82-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                        </svg>
                    </div>
                    <div class="relative">
                        <p class="text-xs uppercase text-purple-200 dark:text-purple-300">Kriteria Terbaru</p>
                        <p class="text-lg font-semibold truncate mt-1" title="{{ $latestCriterion->name }}">
                            {{ Str::limit($latestCriterion->name, 20) }}
                        </p>
                    </div>
                </div>
                @endif

                {{-- Card Alternatif Terbaru --}}
                @php $latestAlternative = $latestAlternative ?? \App\Models\Alternative::latest()->first(); @endphp
                @if($latestAlternative)
                <div class="bg-yellow-500 dark:bg-yellow-600 p-5 rounded-lg shadow-lg text-white relative overflow-hidden hover:bg-yellow-600 dark:hover:bg-yellow-700 transition-colors duration-200">
                    <div class="absolute top-0 right-0 -mt-3 -mr-3 opacity-20">
                        <svg class="w-24 h-24" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h7.5M8.25 12h7.5m-7.5 5.25h7.5" />
                        </svg>
                    </div>
                    <div class="relative">
                        <p class="text-xs uppercase text-yellow-200 dark:text-yellow-300">Alternatif Terbaru</p>
                        <p class="text-lg font-semibold truncate mt-1" title="{{ $latestAlternative->name }}">
                            {{ Str::limit($latestAlternative->name, 20) }}
                        </p>
                    </div>
                </div>
                @endif
            </div>

            {{-- Judul untuk Menu Utama --}}
            <div class="mt-10 pt-6 border-t border-gray-200 dark:border-gray-700/50">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-5">Menu Utama</h3>
            </div>

            {{-- Kontainer untuk Menu Utama (Rata Kiri) --}}
            <div class="space-y-4 md:space-y-5">

                {{-- Item Menu: Tambah Alternatif --}}
                <a href="{{ route('alternatives.create') }}"
                    class="group flex items-center p-4 sm:p-5 bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out transform hover:-translate-y-1 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                    <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 group-hover:bg-blue-200 dark:group-hover:bg-blue-800 transition-colors">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4 flex-grow">
                        <h5 class="text-md sm:text-lg font-semibold tracking-tight text-gray-800 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Tambah Alternatif</h5>
                        <p class="text-xs sm:text-sm font-normal text-gray-500 dark:text-gray-400 mt-0.5">Input data alternatif baru untuk dianalisis.</p>
                    </div>
                    <div class="ml-auto pl-3 text-gray-400 dark:text-gray-500 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </a>

                {{-- Item Menu: Perhitungan VIKOR --}}
                <a href="{{ route('vikor.index') }}"
                    class="group flex items-center p-4 sm:p-5 bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out transform hover:-translate-y-1 border-l-4 border-transparent hover:border-emerald-500 dark:hover:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-opacity-50">
                    <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-emerald-100 dark:bg-emerald-900 text-emerald-600 dark:text-emerald-400 group-hover:bg-emerald-200 dark:group-hover:bg-emerald-800 transition-colors">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4 flex-grow">
                        <h5 class="text-md sm:text-lg font-semibold tracking-tight text-gray-800 dark:text-gray-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Perhitungan VIKOR</h5>
                        <p class="text-xs sm:text-sm font-normal text-gray-500 dark:text-gray-400 mt-0.5">Lakukan proses SPK untuk mendapatkan hasil.</p>
                    </div>
                    <div class="ml-auto pl-3 text-gray-400 dark:text-gray-500 group-hover:text-emerald-500 dark:group-hover:text-emerald-400 transition-colors">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </a>

                {{-- Item Menu: Cetak Laporan DIHAPUS DARI SINI --}}
                {{-- <a href="{{ route('report.print') }}"
                    class="group flex items-center p-4 sm:p-5 bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out transform hover:-translate-y-1 border-l-4 border-transparent hover:border-orange-500 dark:hover:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-opacity-50">
                    <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-orange-100 dark:bg-orange-900 text-orange-600 dark:text-orange-400 group-hover:bg-orange-200 dark:group-hover:bg-orange-800 transition-colors">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a8.504 8.504 0 0110.56 0m-10.56 0L6 21m5.16-7.671a2.25 2.25 0 01-2.25 2.25H7.5A2.25 2.25 0 015.25 13.5V11.25a2.25 2.25 0 012.25-2.25h.149c.442 0 .86.094 1.241.264M10.84 11.25L12 21m2.16-7.671a2.25 2.25 0 00-2.25 2.25H16.5A2.25 2.25 0 0018.75 13.5V11.25a2.25 2.25 0 00-2.25-2.25h-.149c-.442 0-.86.094-1.241.264M13.16 11.25L12 21M3.75 3.75h16.5a1.5 1.5 0 011.5 1.5v6.75a1.5 1.5 0 01-1.5 1.5H3.75a1.5 1.5 0 01-1.5-1.5V5.25a1.5 1.5 0 011.5-1.5z" />
                        </svg>
                    </div>
                    <div class="ml-4 flex-grow">
                        <h5 class="text-md sm:text-lg font-semibold tracking-tight text-gray-800 dark:text-gray-100 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">Cetak Laporan</h5>
                        <p class="text-xs sm:text-sm font-normal text-gray-500 dark:text-gray-400 mt-0.5">Cetak data alternatif, kriteria, dan hasil perankingan.</p>
                    </div>
                    <div class="ml-auto pl-3 text-gray-400 dark:text-gray-500 group-hover:text-orange-500 dark:group-hover:text-orange-400 transition-colors">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </a> --}}

                {{-- Item Menu: Profil Saya --}}
                <a href="{{ route('profile.edit') }}"
                    class="group flex items-center p-4 sm:p-5 bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out transform hover:-translate-y-1 border-l-4 border-transparent hover:border-purple-500 dark:hover:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50">
                    <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-400 group-hover:bg-purple-200 dark:group-hover:bg-purple-800 transition-colors">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="ml-4 flex-grow">
                        <h5 class="text-md sm:text-lg font-semibold tracking-tight text-gray-800 dark:text-gray-100 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">Profil Saya</h5>
                        <p class="text-xs sm:text-sm font-normal text-gray-500 dark:text-gray-400 mt-0.5">Kelola informasi akun Anda.</p>
                    </div>
                    <div class="ml-auto pl-3 text-gray-400 dark:text-gray-500 group-hover:text-purple-500 dark:group-hover:text-purple-400 transition-colors">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>