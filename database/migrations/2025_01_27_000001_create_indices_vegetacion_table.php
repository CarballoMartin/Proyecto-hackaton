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
        Schema::create('indices_vegetacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidad_productiva_id')->constrained('unidades_productivas')->onDelete('cascade');
            $table->decimal('ndvi', 5, 3)->comment('Índice de vegetación normalizado (-1 a 1)');
            $table->decimal('evi', 5, 3)->nullable()->comment('Índice de vegetación mejorado');
            $table->decimal('evi2', 5, 3)->nullable()->comment('Índice de vegetación mejorado v2');
            $table->string('clasificacion', 20)->comment('baja, media, alta');
            $table->date('fecha_imagen')->comment('Fecha de la imagen satelital');
            $table->string('satelite', 20)->default('sentinel-2')->comment('Satélite utilizado');
            $table->decimal('nubosidad_porcentaje', 5, 2)->default(0)->comment('Porcentaje de nubosidad');
            $table->decimal('latitud', 10, 8)->comment('Latitud del punto');
            $table->decimal('longitud', 11, 8)->comment('Longitud del punto');
            $table->json('datos_completos')->nullable()->comment('Datos adicionales de la imagen');
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['unidad_productiva_id', 'fecha_imagen']);
            $table->index('clasificacion');
            $table->index('ndvi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indices_vegetacion');
    }
};
