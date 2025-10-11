<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_animals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('declaracion_id')->constrained('declaraciones_stock')->onDelete('cascade');
            $table->foreignId('unidad_productiva_id')->constrained('unidades_productivas');
            $table->foreignId('especie_id')->constrained('especies');
            $table->foreignId('categoria_id')->constrained('categoria_animals');
            $table->foreignId('raza_id')->constrained('razas');
            $table->dateTime('fecha_registro')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('cantidad');
            $table->foreignId('tipo_registro_id')->constrained('tipo_registros');
            $table->text('observaciones')->nullable();

            $table->timestamps();
            
            // Índice para búsquedas por fecha
            $table->index('fecha_registro');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_animals');
    }
};
