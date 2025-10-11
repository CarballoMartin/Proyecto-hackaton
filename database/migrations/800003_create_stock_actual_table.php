<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_actual', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidad_productiva_id')->constrained('unidades_productivas')->onDelete('cascade');
            $table->foreignId('especie_id')->constrained()->onDelete('cascade');
            $table->foreignId('categoria_id')->constrained('categoria_animals')->onDelete('cascade');
            $table->foreignId('raza_id')->constrained()->onDelete('cascade');
            $table->integer('cantidad_actual')->default(0);
            $table->timestamps();

            // Índice único para evitar filas duplicadas
            $table->unique(['unidad_productiva_id', 'especie_id', 'categoria_id', 'raza_id'], 'stock_actual_unique_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_actual');
    }
};