<?php
// app/Http/Controllers/VikorController.php
namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\Criterion;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session; // Untuk menyimpan hasil ke session

class VikorController extends Controller
{
    public function index()
    {
        // Ambil hasil dari session jika ada (setelah perhitungan)
        $results = Session::get('vikor_results');
        $alternatives = Alternative::with('evaluations.criterion')->get();
        $criteria = Criterion::orderBy('code')->get();
        
        return view('vikor.index', compact('alternatives', 'criteria', 'results'));
    }

    public function calculate(Request $request)
    {
        $v_weight = (float) $request->input('v_weight', 0.5); // Bobot untuk strategi "majority of criteria" (biasanya 0.5)

        $alternatives = Alternative::with('evaluations.criterion')->get();
        $criteria = Criterion::orderBy('code')->get();

        if ($alternatives->isEmpty() || $criteria->isEmpty()) {
            return redirect()->route('vikor.index')->with('error', 'Tidak ada data alternatif atau kriteria untuk dihitung.');
        }

        // 1. Membuat Matriks Keputusan (F)
        // $F[alternative_id][criterion_id] = value
        $F = [];
        foreach ($alternatives as $alt) {
            foreach ($alt->evaluations as $eval) {
                $F[$alt->id][$eval->criterion_id] = (float) $eval->value;
            }
            // Pastikan semua kriteria ada untuk setiap alternatif, jika tidak beri nilai default (misal 0)
            // atau handle error. Untuk simplicity, kita asumsikan data lengkap.
        }

        // 2. Menentukan Nilai f_best (f*) dan f_worst (f-) untuk setiap kriteria
        // $f_best[criterion_id], $f_worst[criterion_id]
        $f_best = [];
        $f_worst = [];
        foreach ($criteria as $crit) {
            $valuesForCriterion = [];
            foreach ($alternatives as $alt) {
                // Pastikan nilai ada sebelum mengakses
                if (isset($F[$alt->id][$crit->id])) {
                     $valuesForCriterion[] = $F[$alt->id][$crit->id];
                }
            }

            if (empty($valuesForCriterion)) {
                // Handle jika kriteria tidak memiliki nilai evaluasi sama sekali
                // Ini seharusnya tidak terjadi jika input data benar
                return redirect()->route('vikor.index')->with('error', "Kriteria '{$crit->name}' tidak memiliki nilai evaluasi.");
            }

            if ($crit->type == 'benefit') {
                $f_best[$crit->id] = max($valuesForCriterion);
                $f_worst[$crit->id] = min($valuesForCriterion);
            } else { // cost
                $f_best[$crit->id] = min($valuesForCriterion); // Best untuk cost adalah nilai minimum
                $f_worst[$crit->id] = max($valuesForCriterion); // Worst untuk cost adalah nilai maksimum
            }
        }
        
        // 3. Normalisasi dan Perhitungan S dan R
        // $S[alternative_id], $R[alternative_id]
        $S_values = [];
        $R_values = [];

        foreach ($alternatives as $alt) {
            $S_i = 0;
            $R_i_terms = [];

            foreach ($criteria as $crit) {
                if (!isset($F[$alt->id][$crit->id])) {
                    // Seharusnya tidak terjadi jika data lengkap.
                    // Mungkin beri nilai default atau skip.
                    continue;
                }

                $f_ij = $F[$alt->id][$crit->id];
                $f_star_j = $f_best[$crit->id];
                $f_minus_j = $f_worst[$crit->id];
                $w_j = (float) $crit->weight;

                if (($f_star_j - $f_minus_j) == 0) {
                    $normalized_value = 0; // Jika semua nilai sama untuk kriteria ini
                } else {
                    // Rumus VIKOR untuk normalisasi: (f*j - fij) / (f*j - f-j)
                    // Ini berlaku untuk benefit dan cost karena f*j sudah disesuaikan
                    $normalized_value = ($f_star_j - $f_ij) / ($f_star_j - $f_minus_j);
                    // Jika cost: f_star_j adalah min, f_minus_j adalah max
                    // (min_val - current_val) / (min_val - max_val) -> (current_val - min_val) / (max_val - min_val)
                    // Ini adalah normalisasi standar min-max untuk cost.
                    // Jika benefit: f_star_j adalah max, f_minus_j adalah min
                    // (max_val - current_val) / (max_val - min_val)
                    // Ini adalah normalisasi standar min-max untuk benefit (jarak dari ideal).
                    
                    // Pastikan nilai yang dinormalisasi selalu positif atau nol,
                    // sesuai dengan definisi (f*j - fij)
                    // Jika tipe cost: f*j = min, f-j = max. (min - fij) / (min - max).
                    // Agar hasilnya positif (seperti jarak dari ideal), kita bisa balik:
                    // (fij - f*j) / (f-j - f*j) untuk cost
                    // (f*j - fij) / (f*j - f-j) untuk benefit
                    // Namun, VIKOR biasanya pakai satu rumus (f*j - fij) / (f*j - f-j)
                    // dimana f*j adalah nilai terbaik (max untuk benefit, min untuk cost)
                    // dan f-j adalah nilai terburuk (min untuk benefit, max untuk cost)

                    if ($crit->type == 'cost') {
                       // f*j adalah nilai minimum, f-j adalah nilai maksimum
                       // (f_ij - f_star_j) karena f_star_j adalah nilai terbaik (terkecil)
                       // agar semakin besar f_ij (menjauh dari terbaik), semakin besar nilai normalisasinya
                       $normalized_value = ($f_ij - $f_star_j) / ($f_minus_j - $f_star_j);
                    } else { // benefit
                       // f*j adalah nilai maksimum, f-j adalah nilai minimum
                       // (f_star_j - f_ij) karena f_star_j adalah nilai terbaik (terbesar)
                       // agar semakin kecil f_ij (menjauh dari terbaik), semakin besar nilai normalisasinya
                       $normalized_value = ($f_star_j - $f_ij) / ($f_star_j - $f_minus_j);
                    }
                     // Pastikan denominator tidak nol
                    if (($f_star_j - $f_minus_j) == 0) { // Jika f_star_j sama dengan f_minus_j
                        $normalized_value = 0; // Tidak ada perbedaan, kontribusi ke S dan R adalah 0
                    }
                }
                
                $S_i += $w_j * $normalized_value;
                $R_i_terms[] = $w_j * $normalized_value;
            }
            $S_values[$alt->id] = $S_i;
            $R_values[$alt->id] = !empty($R_i_terms) ? max($R_i_terms) : 0;
        }

        // 4. Menghitung Nilai Q
        // $Q[alternative_id]
        $S_star = !empty($S_values) ? min($S_values) : 0;
        $S_minus = !empty($S_values) ? max($S_values) : 0;
        $R_star = !empty($R_values) ? min($R_values) : 0;
        $R_minus = !empty($R_values) ? max($R_values) : 0;

        $Q_values = [];
        foreach ($alternatives as $alt) {
            $term1 = 0;
            if (($S_minus - $S_star) != 0) {
                $term1 = $v_weight * (($S_values[$alt->id] - $S_star) / ($S_minus - $S_star));
            }

            $term2 = 0;
            if (($R_minus - $R_star) != 0) {
                $term2 = (1 - $v_weight) * (($R_values[$alt->id] - $R_star) / ($R_minus - $R_star));
            }
            $Q_values[$alt->id] = $term1 + $term2;
        }

        // 5. Menyusun Peringkat berdasarkan S, R, dan Q (nilai terkecil lebih baik)
        $ranked_alternatives = $alternatives->map(function ($alt) use ($S_values, $R_values, $Q_values) {
            return [
                'id' => $alt->id,
                'name' => $alt->name,
                'S' => $S_values[$alt->id],
                'R' => $R_values[$alt->id],
                'Q' => $Q_values[$alt->id],
            ];
        });

        // Urutkan berdasarkan Q, lalu S, lalu R
        $ranked_alternatives_sorted = $ranked_alternatives->sortBy('Q')->values()->all();
        // Jika Q sama, urutkan berdasarkan S. Jika S sama, urutkan berdasarkan R.
        // sortBy lebih dulu berdasarkan Q. Untuk tie-breaking, kita bisa sort beberapa kali atau custom sort.
        // Cara mudah:
        usort($ranked_alternatives_sorted, function ($a, $b) {
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

        if (count($ranked_alternatives_sorted) > 0) {
            $A_prime = $ranked_alternatives_sorted[0]; // Alternatif terbaik berdasarkan Q
            $compromise_solutions_ids[] = $A_prime['id'];


            if (count($ranked_alternatives_sorted) > 1) {
                $A_double_prime = $ranked_alternatives_sorted[1]; // Alternatif kedua terbaik
                $DQ = 1 / (count($alternatives) - 1);

                // Kondisi 1: Acceptable Advantage
                $C1 = ($A_double_prime['Q'] - $A_prime['Q']) >= $DQ;

                // Kondisi 2: Acceptable Stability in Decision Making
                // A' juga harus terbaik di S atau R
                $best_S_id = $ranked_alternatives->sortBy('S')->first()['id'] ?? null;
                $best_R_id = $ranked_alternatives->sortBy('R')->first()['id'] ?? null;
                
                $C2 = ($A_prime['id'] == $best_S_id || $A_prime['id'] == $best_R_id);

                if ($C1 && $C2) {
                    $compromise_solution_text = "Solusi kompromi tunggal: " . $A_prime['name'];
                } elseif (!$C1 && $C2) { // C1 tidak terpenuhi, C2 terpenuhi
                    $compromise_solution_text = "Solusi kompromi: " . $A_prime['name'] . ". Pertimbangkan juga alternatif lain dalam set solusi kompromi jika perbedaannya tidak signifikan (Q(A(m)) - Q(A') < DQ).";
                     // Cari semua yang memenuhi Q(A(m)) - Q(A') < DQ
                    $compromise_solutions_ids = [$A_prime['id']];
                    for ($m_idx = 1; $m_idx < count($ranked_alternatives_sorted); $m_idx++) {
                        if (($ranked_alternatives_sorted[$m_idx]['Q'] - $A_prime['Q']) < $DQ) {
                            $compromise_solutions_ids[] = $ranked_alternatives_sorted[$m_idx]['id'];
                        } else {
                            break;
                        }
                    }
                    $compromise_solution_names = Alternative::whereIn('id', $compromise_solutions_ids)->pluck('name')->join(', ');
                    $compromise_solution_text = "Set solusi kompromi (karena C1 tidak terpenuhi): " . $compromise_solution_names;


                } elseif ($C1 && !$C2) { // C1 terpenuhi, C2 tidak
                    $compromise_solution_text = "Solusi kompromi: " . $A_prime['name'] . " dan " . $A_double_prime['name'] . " (karena C2 tidak terpenuhi).";
                    $compromise_solutions_ids = [$A_prime['id'], $A_double_prime['id']];
                } else { // Keduanya tidak terpenuhi
                    // Sama seperti C1 tidak terpenuhi, C2 terpenuhi
                     $compromise_solutions_ids = [$A_prime['id']];
                    for ($m_idx = 1; $m_idx < count($ranked_alternatives_sorted); $m_idx++) {
                        if (($ranked_alternatives_sorted[$m_idx]['Q'] - $A_prime['Q']) < $DQ) {
                            $compromise_solutions_ids[] = $ranked_alternatives_sorted[$m_idx]['id'];
                        } else {
                            break;
                        }
                    }
                    $compromise_solution_names = Alternative::whereIn('id', array_unique($compromise_solutions_ids))->pluck('name')->join(', ');
                    $compromise_solution_text = "Set solusi kompromi (karena C1 dan C2 tidak terpenuhi): " . $compromise_solution_names;
                }
            } else { // Hanya satu alternatif
                 $compromise_solution_text = "Solusi kompromi tunggal: " . $A_prime['name'] . " (hanya ada 1 alternatif).";
            }
        }


        $results = [
            'alternatives_data' => $F, // Matriks keputusan awal
            'f_best' => $f_best,
            'f_worst' => $f_worst,
            'S_values' => $S_values,
            'R_values' => $R_values,
            'Q_values' => $Q_values,
            'ranked_alternatives' => $ranked_alternatives_sorted,
            'compromise_solution_text' => $compromise_solution_text,
            'compromise_solutions_ids' => array_unique($compromise_solutions_ids),
            'v_weight' => $v_weight,
        ];
        
        // Simpan hasil ke session untuk ditampilkan di view index
        Session::flash('vikor_results', $results);

        return redirect()->route('vikor.index');
    }
}