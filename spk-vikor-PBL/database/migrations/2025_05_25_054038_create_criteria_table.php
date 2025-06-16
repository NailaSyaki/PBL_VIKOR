<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_criteria_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('criteria', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code'); // C1, C2, dst.
            $table->enum('type', ['benefit', 'cost']);
            $table->decimal('weight', 5, 4); // Bobot, misal 0.3000
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criteria');
    }
};
