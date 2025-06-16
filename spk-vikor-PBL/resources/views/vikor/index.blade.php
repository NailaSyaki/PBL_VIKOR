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
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Data Awal (Matriks Keputusan)</h3>
                    @if($alternatives->isNotEmpty() && $criteria->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 mb-4">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alternatif</th>
                                    @foreach ($criteria as $criterion)
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $criterion->name }} ({{$criterion->code}})</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($alternatives as $alternative)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $alternative->name }}</td>
                                        @foreach ($criteria as $criterion)
                                            @php
                                                $evaluation = $alternative->evaluations->firstWhere('criterion_id', $criterion->id);
                                            @endphp
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $evaluation ? $evaluation->value : '-' }}</td>
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
                        </div>
                        <x-primary-button>
                            {{ __('Hitung VIKOR') }}
                        </x-primary-button>
                    </form>
                    @else
                        <p>Silakan tambahkan data alternatif dan kriteria terlebih dahulu.</p>
                    @endif
                </div>
            </div>

            @if ($results)
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
                                <tbody>
                                    @foreach ($criteria as $criterion)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $criterion->name }} ({{ $criterion->type }})</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $results['f_best'][$criterion->id] ?? '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $results['f_worst'][$criterion->id] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

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
                                <tbody>
                                    @foreach ($alternatives as $alternative)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $alternative->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($results['S_values'][$alternative->id] ?? 0, 4) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($results['R_values'][$alternative->id] ?? 0, 4) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($results['Q_values'][$alternative->id] ?? 0, 4) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Peringkat Alternatif (Berdasarkan Q, S, R - Terkecil Lebih Baik)</h3>
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
                                <tbody>
                                    @foreach ($results['ranked_alternatives'] as $index => $ranked_alt)
                                        <tr class="{{ in_array($ranked_alt['id'], $results['compromise_solutions_ids'] ?? []) ? 'bg-green-100' : '' }}">
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $ranked_alt['name'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($ranked_alt['Q'], 4) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($ranked_alt['S'], 4) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($ranked_alt['R'], 4) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 p-4 border border-gray-300 rounded-md bg-gray-50">
                            <h4 class="font-semibold">Kesimpulan Solusi Kompromi:</h4>
                            <p>{{ $results['compromise_solution_text'] }}</p>
                            <p class="text-sm text-gray-600 mt-1">(Bobot V yang digunakan: {{ $results['v_weight'] }})</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>