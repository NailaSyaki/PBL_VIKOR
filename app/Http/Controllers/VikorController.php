<?php
// app/Http/Controllers/VikorController.php
namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\Criterion;
// use App\Models\Evaluation; // Tidak secara eksplisit digunakan di sini jika sudah di-eager load
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session; // Untuk menyimpan hasil ke session
use PDF; // Pastikan ini ada

class VikorController extends Controller
{
    public function index()
    {
        // Ambil hasil dari session jika ada (setelah perhitungan)
        // Session::get() lebih aman daripada Session::flash() jika ingin data tetap ada setelah refresh
        // atau jika akan digunakan di request lain (seperti print).
        // Jika hanya untuk sekali tampil, flash() oke. Untuk print, get() lebih baik.
        $results = Session::get('vikor_results'); 
        $alternatives = Alternative::with('evaluations.criterion')->get();
        $criteria = Criterion::orderBy('code')->get();
        
        return view('vikor.index', compact('alternatives', 'criteria', 'results'));
    }

    public function calculate(Request $request)
    {
        $v_weight = (float) $request->input('v_weight', 0.5);

        $alternatives = Alternative::with('evaluations.criterion')->get();
        $criteria = Criterion::orderBy('code')->get();

        if ($alternatives->isEmpty() || $criteria->isEmpty()) {
            return redirect()->route('vikor.index')->with('error', 'Tidak ada data alternatif atau kriteria untuk dihitung.');
        }

        // Validasi kelengkapan data evaluasi
        foreach ($alternatives as $alt) {
            if ($alt->evaluations->count() < $criteria->count()) {
                return redirect()->route('vikor.index')->with('error', "Alternatif '{$alt->name}' tidak memiliki evaluasi untuk semua kriteria. Harap lengkapi data.");
            }
            foreach ($criteria as $crit) {
                if (!$alt->evaluations->firstWhere('criterion_id', $crit->id)) {
                     return redirect()->route('vikor.index')->with('error', "Alternatif '{$alt->name}' tidak memiliki evaluasi untuk kriteria '{$crit->name}'. Harap lengkapi data.");
                }
            }
        }

        // 1. Membuat Matriks Keputusan (F)
        $F = [];
        foreach ($alternatives as $alt) {
            foreach ($alt->evaluations as $eval) {
                $F[$alt->id][$eval->criterion_id] = (float) $eval->value;
            }
        }

        // 2. Menentukan Nilai f_best (f*) dan f_worst (f-) untuk setiap kriteria
        $f_best = [];
        $f_worst = [];
        foreach ($criteria as $crit) {
            $valuesForCriterion = [];
            foreach ($alternatives as $alt) {
                if (isset($F[$alt->id][$crit->id])) {
                     $valuesForCriterion[] = $F[$alt->id][$crit->id];
                }
            }

            if (empty($valuesForCriterion)) {
                return redirect()->route('vikor.index')->with('error', "Kriteria '{$crit->name}' tidak memiliki nilai evaluasi dari alternatif manapun.");
            }

            if ($crit->type == 'benefit') {
                $f_best[$crit->id] = max($valuesForCriterion);
                $f_worst[$crit->id] = min($valuesForCriterion);
            } else { // cost
                $f_best[$crit->id] = min($valuesForCriterion);
                $f_worst[$crit->id] = max($valuesForCriterion);
            }
        }
        
        // 3. Normalisasi dan Perhitungan S dan R
        $S_values = [];
        $R_values = [];

        foreach ($alternatives as $alt) {
            $S_i = 0;
            $R_i_terms = [];

            foreach ($criteria as $crit) {
                if (!isset($F[$alt->id][$crit->id])) {
                    continue;
                }

                $f_ij = $F[$alt->id][$crit->id];
                $f_star_j = $f_best[$crit->id];
                $f_minus_j = $f_worst[$crit->id];
                $w_j = (float) $crit->weight;

                $denominator = $f_star_j - $f_minus_j;
                if ($denominator == 0) { // Jika f_star_j sama dengan f_minus_j
                    $normalized_value = 0; 
                } else {
                    if ($crit->type == 'cost') {
                       $normalized_value = ($f_ij - $f_star_j) / ($f_minus_j - $f_star_j); // Perhatikan perubahan agar pembagi positif jika f_minus_j > f_star_j
                       if ($f_minus_j - $f_star_j == 0) $normalized_value = 0; // Double check
                    } else { // benefit
                       $normalized_value = ($f_star_j - $f_ij) / ($f_star_j - $f_minus_j);
                    }
                }
                
                $S_i += $w_j * $normalized_value;
                $R_i_terms[] = $w_j * $normalized_value;
            }
            $S_values[$alt->id] = $S_i;
            $R_values[$alt->id] = !empty($R_i_terms) ? max($R_i_terms) : 0;
        }

        // 4. Menghitung Nilai Q
        $S_star = !empty($S_values) ? min(array_values($S_values)) : 0; // array_values() untuk min dari value
        $S_minus = !empty($S_values) ? max(array_values($S_values)) : 0;
        $R_star = !empty($R_values) ? min(array_values($R_values)) : 0;
        $R_minus = !empty($R_values) ? max(array_values($R_values)) : 0;


        $Q_values = [];
        foreach ($alternatives as $alt) {
            $s_i_val = $S_values[$alt->id];
            $r_i_val = $R_values[$alt->id];

            $term1 = 0;
            if (($S_minus - $S_star) != 0) {
                $term1 = $v_weight * (($s_i_val - $S_star) / ($S_minus - $S_star));
            } elseif ($S_minus == $S_star && $s_i_val == $S_star) { // Semua S sama, jadi jarak 0
                $term1 = 0;
            }


            $term2 = 0;
            if (($R_minus - $R_star) != 0) {
                $term2 = (1 - $v_weight) * (($r_i_val - $R_star) / ($R_minus - $R_star));
            } elseif ($R_minus == $R_star && $r_i_val == $R_star) { // Semua R sama, jadi jarak 0
                $term2 = 0;
            }
            
            $Q_values[$alt->id] = $term1 + $term2;
        }

        // 5. Menyusun Peringkat
        $ranked_alternatives = $alternatives->map(function ($alt) use ($S_values, $R_values, $Q_values) {
            return [
                'id' => $alt->id,
                'name' => $alt->name,
                'S' => $S_values[$alt->id],
                'R' => $R_values[$alt->id],
                'Q' => $Q_values[$alt->id],
            ];
        })->all(); // Ubah menjadi array agar usort bisa bekerja

        usort($ranked_alternatives, function ($a, $b) {
            if ($a['Q'] != $b['Q']) {
                return $a['Q'] <=> $b['Q'];
            }
            if ($a['S'] != $b['S']) {
                return $a['S'] <=> $b['S'];
            }
            return $a['R'] <=> $b['R'];
        });


        // 6. Menentukan Solusi Kompromi
        $compromise_solution_text = "Tidak ada solusi kompromi yang jelas berdasarkan kondisi standar.";
        $compromise_solutions_ids = [];

        if (count($ranked_alternatives) > 0) {
            $A_prime = $ranked_alternatives[0];
            
            if (count($ranked_alternatives) == 1) {
                $compromise_solution_text = "Solusi kompromi tunggal: " . $A_prime['name'] . " (hanya ada 1 alternatif).";
                $compromise_solutions_ids[] = $A_prime['id'];
            } else {
                $A_double_prime = $ranked_alternatives[1];
                $DQ = (count($alternatives) > 1) ? (1 / (count($alternatives) - 1)) : 0; // Hindari pembagian dengan nol jika hanya 1 alternatif

                // Kondisi 1: Acceptable Advantage
                // Q(A'') - Q(A') >= DQ
                $C1 = ($A_double_prime['Q'] - $A_prime['Q']) >= $DQ;
                
                // Kondisi 2: Acceptable Stability in Decision Making
                // A' juga harus terbaik di S atau R
                $temp_ranked_S = collect($ranked_alternatives)->sortBy('S')->values()->all();
                $temp_ranked_R = collect($ranked_alternatives)->sortBy('R')->values()->all();
                
                $best_S_id = !empty($temp_ranked_S) ? $temp_ranked_S[0]['id'] : null;
                $best_R_id = !empty($temp_ranked_R) ? $temp_ranked_R[0]['id'] : null;
                
                $C2 = ($A_prime['id'] == $best_S_id || $A_prime['id'] == $best_R_id);

                if ($C1 && $C2) {
                    $compromise_solution_text = "Solusi kompromi tunggal: " . $A_prime['name'] . ". (Memenuhi Kondisi 1 dan Kondisi 2)";
                    $compromise_solutions_ids[] = $A_prime['id'];
                } elseif (!$C1) { // C1 tidak terpenuhi, C2 bisa terpenuhi atau tidak
                    $compromise_solutions_ids[] = $A_prime['id']; // A' pasti masuk
                    // Tambahkan alternatif lain A(m) dimana Q(A(m)) - Q(A') < DQ
                    for ($m_idx = 1; $m_idx < count($ranked_alternatives); $m_idx++) {
                        if (($ranked_alternatives[$m_idx]['Q'] - $A_prime['Q']) < $DQ) {
                            $compromise_solutions_ids[] = $ranked_alternatives[$m_idx]['id'];
                        } else {
                            break;
                        }
                    }
                    // Hilangkan duplikat ID
                    $compromise_solutions_ids = array_unique($compromise_solutions_ids);
                    $compromise_solution_names = Alternative::whereIn('id', $compromise_solutions_ids)->pluck('name')->join(', ');
                    $compromise_solution_text = "Set solusi kompromi (karena Kondisi 1 tidak terpenuhi): {" . $compromise_solution_names . "}.";
                } elseif ($C1 && !$C2) { // C1 terpenuhi, C2 tidak
                    $compromise_solution_text = "Solusi kompromi adalah: " . $A_prime['name'] . " dan " . $A_double_prime['name'] . ". (Karena Kondisi 2 tidak terpenuhi untuk A')";
                    $compromise_solutions_ids = [$A_prime['id'], $A_double_prime['id']];
                }
            }
        }


        $results = [
            'alternatives_data_matrix' => $F, // Matriks keputusan awal
            'f_best' => $f_best,
            'f_worst' => $f_worst,
            'S_values' => $S_values,
            'R_values' => $R_values,
            'Q_values' => $Q_values,
            'ranked_alternatives' => $ranked_alternatives, // Ini sudah array yang di-sort
            'compromise_solution_text' => $compromise_solution_text,
            'compromise_solutions_ids' => array_unique($compromise_solutions_ids),
            'v_weight' => $v_weight,
        ];
        
        // Simpan hasil ke session untuk ditampilkan di view index dan digunakan oleh printReport
        // Session::flash() hanya untuk next request. Gunakan Session::put() agar data tetap ada.
        Session::put('vikor_results', $results);

        return redirect()->route('vikor.index');
    }

    // Method untuk Cetak PDF
    public function printReport()
    {
        // Ambil hasil dari session yang disimpan oleh method calculate()
        $results = Session::get('vikor_results');
        
        // Ambil data alternatif dan kriteria lagi jika diperlukan untuk ditampilkan di PDF
        // (misalnya jika view PDF butuh list nama kriteria/alternatif terpisah dari $results)
        $alternatives = Alternative::with('evaluations.criterion')->get(); // Digunakan jika PDF menampilkan matriks awal
        $criteria = Criterion::orderBy('code')->get(); // Digunakan jika PDF menampilkan matriks awal atau info kriteria

        if (!$results) {
            return redirect()->route('vikor.index')->with('error', 'Tidak ada hasil perhitungan untuk dicetak. Silakan lakukan perhitungan terlebih dahulu.');
        }

        // Data yang akan dikirim ke view PDF
        $data = [
            'results' => $results,
            'alternatives' => $alternatives, // Untuk tabel data awal di PDF (opsional)
            'criteria' => $criteria,       // Untuk tabel data awal di PDF (opsional)
            'title' => 'Laporan Hasil Perhitungan VIKOR' // Contoh judul untuk PDF
        ];

        // Load view PDF dan kirim data
        $pdf = PDF::loadView('vikor.report_pdf', $data);
        
        // Opsi: stream (tampilkan di browser) atau download
        // return $pdf->stream('laporan_vikor.pdf'); 
        return $pdf->download('Laporan_VIKOR_'.date('Ymd_His').'.pdf');
    }
}