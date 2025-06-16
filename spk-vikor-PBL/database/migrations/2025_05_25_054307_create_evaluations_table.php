<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_evaluations_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alternative_id')->constrained()->onDelete('cascade');
            $table->foreignId('criterion_id')->constrained()->onDelete('cascade');
            $table->decimal('value', 8, 2); // Nilai evaluasi
            $table->timestamps();

            $table->unique(['alternative_id', 'criterion_id']); // Setiap alternatif hanya punya 1 nilai per kriteria
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};