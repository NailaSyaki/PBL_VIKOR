<?php
// database/seeders/CriterionSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Criterion;

class CriterionSeeder extends Seeder
{
    public function run(): void
    {
        Criterion::create(['name' => 'Kemudahan Pengguna', 'code' => 'C1', 'type' => 'benefit', 'weight' => 0.1814]);
        Criterion::create(['name' => 'Akurasi Jawaban', 'code' => 'C2', 'type' => 'benefit', 'weight' => 0.1596]);
        Criterion::create(['name' => 'Kecepatan Respon', 'code' => 'C3', 'type' => 'benefit', 'weight' => 0.1704]);
        Criterion::create(['name' => 'Pemahaman Konteks', 'code' => 'C4', 'type' => 'benefit', 'weight' => 0.1637]);
        Criterion::create(['name' => 'Gratis/Berlangganan', 'code' => 'C5', 'type' => 'benefit', 'weight' => 0.1582]);
        Criterion::create(['name' => 'Dukungan Fitur', 'code' => 'C6', 'type' => 'benefit', 'weight' => 0.1667]);
    }
}