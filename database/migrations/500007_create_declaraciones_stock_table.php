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
        Schema::create('declaraciones_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('productor_id')->constrained('productors')->onDelete('cascade');
            $table->foreignId('periodo_id')->constrained('configuracion_actualizacions')->onDelete('cascade');
            $table->foreignId('unidad_productiva_id')->constrained('unidades_productivas')->onDelete('cascade');
            $table->date('fecha_declaracion');
            $table->string('estado')->default('en_progreso'); // en_progreso, completada, archivada
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('declaraciones_stock');
    }
};
