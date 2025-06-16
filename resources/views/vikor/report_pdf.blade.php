<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Hasil Perhitungan VIKOR</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif; /* Font yang mendukung karakter unicode lebih baik */
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        h1, h2, h3 {
            text-align: center;
            margin-bottom: 10px;
        }
        h1 { font-size: 18px; margin-bottom: 20px; }
        h2 { font-size: 16px; margin-top: 30px; }
        h3 { font-size: 14px; text-align: left; margin-bottom: 8px; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .highlight {
            background-color: #e6ffe6; /* Warna untuk solusi kompromi */
        }
        .conclusion {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #777;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Laporan Hasil Perhitungan Metode VIKOR</h1>
        <p style="text-align: center; margin-bottom: 20px;">Tanggal Cetak: {{ date('d F Y H:i:s') }}</p>

        @if ($results)
            <h2>Peringkat Alternatif</h2>
            <p style="text-align:left; font-size: 11px; margin-bottom: 10px;">(Berdasarkan Q Terkecil, kemudian S Terkecil, kemudian R Terkecil)</p>
            <table>
                <thead>
                    <tr>
                        <th>Peringkat</th>
                        <th>Alternatif</th>
                        <th>Nilai Q</th>
                        <th>Nilai S</th>
                        <th>Nilai R</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($results['ranked_alternatives'] as $index => $ranked_alt)
                        <tr class="{{ in_array($ranked_alt['id'], $results['compromise_solutions_ids'] ?? []) ? 'highlight' : '' }}">
                            <td style="text-align: center;">{{ $index + 1 }}</td>
                            <td>{{ $ranked_alt['name'] }}</td>
                            <td style="text-align: right;">{{ number_format($ranked_alt['Q'], 4) }}</td>
                            <td style="text-align: right;">{{ number_format($ranked_alt['S'], 4) }}</td>
                            <td style="text-align: right;">{{ number_format($ranked_alt['R'], 4) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="conclusion">
                <h3>Kesimpulan Solusi Kompromi:</h3>
                <p>{{ $results['compromise_solution_text'] }}</p>
                <p style="font-size: 11px; margin-top: 5px;">(Bobot V yang digunakan: {{ $results['v_weight'] }})</p>
            </div>

            {{-- Opsional: Jika ingin menyertakan data awal dan f*/f- --}}
            {{-- <div class="page-break"></div>
            <h2>Data Awal (Matriks Keputusan)</h2>
            <table>
                <thead>
                    <tr>
                        <th>Alternatif</th>
                        @foreach ($criteria as $criterion)
                            <th>{{ $criterion->name }} ({{$criterion->code}})</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($alternatives as $alternative)
                        <tr>
                            <td>{{ $alternative->name }}</td>
                            @foreach ($criteria as $criterion)
                                @php
                                    $evaluation = $alternative->evaluations->firstWhere('criterion_id', $criterion->id);
                                @endphp
                                <td style="text-align: center;">{{ $evaluation ? $evaluation->value : '-' }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <h2>Nilai Terbaik (f*) dan Terburuk (f-)</h2>
            <table>
                <thead>
                    <tr>
                        <th>Kriteria</th>
                        <th>f* (Terbaik)</th>
                        <th>f- (Terburuk)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($criteria as $criterion)
                        <tr>
                            <td>{{ $criterion->name }} ({{ $criterion->type }})</td>
                            <td style="text-align: center;">{{ $results['f_best'][$criterion->id] ?? '-' }}</td>
                            <td style="text-align: center;">{{ $results['f_worst'][$criterion->id] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table> --}}

        @else
            <p>Tidak ada hasil perhitungan yang dapat dicetak.</p>
        @endif

        <div class="footer">
            Sistem Pendukung Keputusan - Metode VIKOR
        </div>
    </div>
</body>
</html>