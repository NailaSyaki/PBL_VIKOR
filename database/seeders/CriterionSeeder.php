<?php
// database/seeders/CriterionSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Criterion;

class CriterionSeeder extends Seeder
{
    public function run(): void
    {
        Criterion::create(['name' => 'Akurasi', 'code' => 'C1', 'type' => 'benefit', 'weight' => 0.30]);
        Criterion::create(['name' => 'Kecepatan Prediksi (ops/s)', 'code' => 'C2', 'type' => 'benefit', 'weight' => 0.25]);
        Criterion::create(['name' => 'Biaya Komputasi (USD/jam)', 'code' => 'C3', 'type' => 'cost', 'weight' => 0.20]);
        Criterion::create(['name' => 'Kemudahan Interpretasi (1-5)', 'code' => 'C4', 'type' => 'benefit', 'weight' => 0.15]);
        Criterion::create(['name' => 'Ukuran Dataset Training (GB)', 'code' => 'C5', 'type' => 'cost', 'weight' => 0.10]);
    }
}