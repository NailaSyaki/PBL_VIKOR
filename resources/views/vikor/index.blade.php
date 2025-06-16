<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Perhitungan VIKOR') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            
            {{-- Mengembalikan ke bg-white untuk mode terang --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Data Awal (Matriks Keputusan)</h3>
                    @if($alternatives->isNotEmpty() && $criteria->isNotEmpty())
                    <div class="overflow-x-auto">
                        {{-- Mengembalikan ke divide-gray-200 dan bg-gray-50 untuk thead --}}
                        <table class="min-w-full divide-y divide-gray-200 mb-4">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alternatif</th>
                                    @foreach ($criteria as $criterion)
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $criterion->name }} ({{$criterion->code}})</th>
                                    @endforeach
                                </tr>
                            </thead>
                            {{-- Mengembalikan ke bg-white dan divide-gray-200 untuk tbody --}}
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($alternatives as $alternative)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $alternative->name }}</td>
                                        @foreach ($criteria as $criterion)
                                            @php
                                                $evaluation = $alternative->evaluations->firstWhere('criterion_id', $criterion->id);
                                            @endphp
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $evaluation ? $evaluation->value : '-' }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <form action="{{ route('vikor.calculate') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <x-input-label for="v_weight" :value="__('Bobot V (Strategi Mayoritas Kriteria, default: 0.5)')" />
                            <x-text-input id="v_weight" class="block mt-1 w-full md:w-1/4" type="number" step="0.01" min="0" max="1" name="v_weight" :value="old('v_weight', $results['v_weight'] ?? 0.5)" />
                            <x-input-error :messages="$errors->get('v_weight')" class="mt-2" />
                        </div>
                        <x-primary-button>
                            {{ __('Hitung VIKOR') }}
                        </x-primary-button>
                    </form>
                    @else
                        <p class="text-gray-600">Silakan tambahkan data alternatif dan kriteria terlebih dahulu.</p>
                        <div class="mt-4">
                            <a href="{{ route('alternatives.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2">
                                Tambah Alternatif
                            </a>
                            <a href="{{ route('criteria.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Tambah Kriteria
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            @if ($results)
                {{-- Mengembalikan ke bg-white untuk mode terang --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Nilai Terbaik (f*) dan Terburuk (f-)</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 mb-4">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kriteria</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">f* (Terbaik)</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">f- (Terburuk)</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($criteria as $criterion)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $criterion->name }} ({{ $criterion->type }})</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $results['f_best'][$criterion->id] ?? '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $results['f_worst'][$criterion->id] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Mengembalikan ke bg-white untuk mode terang --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Nilai S, R, dan Q</h3>
                         <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 mb-4">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alternatif</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">S</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">R</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Q</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($alternatives as $alternative)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $alternative->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($results['S_values'][$alternative->id] ?? 0, 4) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($results['R_values'][$alternative->id] ?? 0, 4) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($results['Q_values'][$alternative->id] ?? 0, 4) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                {{-- Mengembalikan ke bg-white untuk mode terang --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-lg font-medium text-gray-900">
                                Peringkat Alternatif (Berdasarkan Q, S, R - Terkecil Lebih Baik)
                            </h3>
                            {{-- Menghapus kelas dark mode dari tombol cetak --}}
                            <a href="{{ route('vikor.print') }}" target="_blank"
                               class="inline-flex items-center px-4 py-2 bg-sky-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-sky-500 active:bg-sky-700 focus:outline-none focus:border-sky-700 focus:ring ring-sky-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Cetak Laporan (PDF)
                            </a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 mb-4">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peringkat</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alternatif</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Q</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">S</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">R</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($results['ranked_alternatives'] as $index => $ranked_alt)
                                        {{-- Menghapus kelas dark mode dari baris highlight --}}
                                        <tr class="{{ in_array($ranked_alt['id'], $results['compromise_solutions_ids'] ?? []) ? 'bg-green-100' : '' }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $ranked_alt['name'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($ranked_alt['Q'], 4) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($ranked_alt['S'], 4) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($ranked_alt['R'], 4) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{-- Mengembalikan warna kotak kesimpulan ke mode terang --}}
                        <div class="mt-4 p-4 border border-gray-300 rounded-md bg-gray-50">
                            <h4 class="font-semibold text-gray-900">Kesimpulan Solusi Kompromi:</h4>
                            <p class="text-gray-700">{{ $results['compromise_solution_text'] }}</p>
                            <p class="text-sm text-gray-600 mt-1">(Bobot V yang digunakan: {{ $results['v_weight'] }})</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>