<?php
// app/Http/Controllers/AlternativeController.php
namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\Criterion;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlternativeController extends Controller
{
    public function index()
    {
        $alternatives = Alternative::latest()->paginate(10);
        return view('alternatives.index', compact('alternatives'));
    }

    public function create()
    {
        $criteria = Criterion::orderBy('code')->get();
        return view('alternatives.create', compact('criteria'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:alternatives,name',
            'description' => 'nullable|string',
            'criteria' => 'required|array',
            'criteria.*' => 'required|numeric|min:0', // Sesuaikan validasi jika perlu (misal skala 1-5)
        ]);

        DB::transaction(function () use ($request) {
            $alternative = Alternative::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            foreach ($request->criteria as $criterionId => $value) {
                Evaluation::create([
                    'alternative_id' => $alternative->id,
                    'criterion_id' => $criterionId,
                    'value' => $value,
                ]);
            }
        });

        return redirect()->route('alternatives.index')->with('success', 'Alternatif berhasil ditambahkan.');
    }

    public function show(Alternative $alternative)
    {
        // Biasanya tidak digunakan jika ada edit, tapi bisa untuk detail view
        return view('alternatives.show', compact('alternative'));
    }

    public function edit(Alternative $alternative)
    {
        $criteria = Criterion::orderBy('code')->get();
        $alternative_evaluations = $alternative->evaluations->pluck('value', 'criterion_id')->all();
        return view('alternatives.edit', compact('alternative', 'criteria', 'alternative_evaluations'));
    }

    public function update(Request $request, Alternative $alternative)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:alternatives,name,' . $alternative->id,
            'description' => 'nullable|string',
            'criteria' => 'required|array',
            'criteria.*' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $alternative) {
            $alternative->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            // Hapus evaluasi lama dan buat yang baru (atau update jika ada)
            // $alternative->evaluations()->delete(); // Opsi hapus semua lalu insert
            foreach ($request->criteria as $criterionId => $value) {
                Evaluation::updateOrCreate(
                    ['alternative_id' => $alternative->id, 'criterion_id' => $criterionId],
                    ['value' => $value]
                );
            }
        });

        return redirect()->route('alternatives.index')->with('success', 'Alternatif berhasil diperbarui.');
    }

    public function destroy(Alternative $alternative)
    {
        // Evaluasi akan terhapus otomatis karena onDelete('cascade') di migrasi
        $alternative->delete();
        return redirect()->route('alternatives.index')->with('success', 'Alternatif berhasil dihapus.');
    }
}