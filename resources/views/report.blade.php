<?php
// app/Http/Controllers/ReportController.php
namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\Criterion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ReportController extends Controller
{
    public function printReport()
    {
        // 1. Ambil data master (Alternatif dan Kriteria)
        $alternativesData = Alternative::orderBy('id')->get();
        $criteriaData = Criterion::orderBy('id')->get();

        // 2. Ambil hasil perhitungan VIKOR dari session
        //    Pastikan nama kunci session ini SAMA PERSIS dengan yang Anda gunakan
        //    saat menyimpan hasil di VikorController@calculate()
        //    Misalnya, jika di calculate() Anda menggunakan Session::put('vikor_calculation_results_permanent', $results);
        $vikorResults = Session::get('vikor_calculation_results_permanent');

        // 3. Validasi apakah hasil perhitungan ada
        if (!$vikorResults) {
            // Jika tidak ada hasil, redirect kembali ke halaman index VIKOR
            // dengan pesan error agar pengguna tahu harus menghitung dulu.
            return redirect()->route('vikor.index')->with('error', 'Data laporan tidak ditemukan. Silakan lakukan perhitungan VIKOR terlebih dahulu.');
        }

        // 4. Render view 'report.print_page' dan kirim semua data yang dibutuhkan
        //    Pemanggilan 'report.print_page' akan mencari file:
        //    resources/views/report/print_page.blade.php
        return view('report.print_page', [
            'alternatives' => $alternativesData,
            'criteria' => $criteriaData,
            'vikorResults' => $vikorResults  // Ini adalah array utama yang berisi sub-array hasil
        ]);
    }
}