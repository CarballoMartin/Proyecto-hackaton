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
        Schema::create('caracteristicas_suelo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidad_productiva_id')->constrained('unidades_productivas')->onDelete('cascade');
            $table->decimal('ph_valor', 4, 2)->comment('pH del suelo (0-14)');
            $table->decimal('materia_organica_porcentaje', 5, 2)->comment('Porcentaje de materia orgánica');
            $table->decimal('arcilla_porcentaje', 5, 2)->comment('Porcentaje de arcilla');
            $table->decimal('limo_porcentaje', 5, 2)->comment('Porcentaje de limo');
            $table->decimal('arena_porcentaje', 5, 2)->comment('Porcentaje de arena');
            $table->decimal('capacidad_retencion_agua', 5, 2)->comment('Capacidad de retención de agua (mm/m)');
            $table->decimal('nitrogeno_total', 5, 2)->comment('Nitrógeno total (g/kg)');
            $table->decimal('fosforo_disponible', 5, 2)->comment('Fósforo disponible (mg/kg)');
            $table->decimal('potasio_intercambiable', 5, 2)->comment('Potasio intercambiable (cmol/kg)');
            $table->decimal('capacidad_intercambio_cationico', 5, 2)->comment('CIC (cmol/kg)');
            $table->decimal('saturacion_bases', 5, 2)->comment('Saturación de bases (%)');
            $table->decimal('densidad_aparente', 4, 2)->comment('Densidad aparente (g/cm³)');
            $table->integer('profundidad_cm')->default(30)->comment('Profundidad de análisis (cm)');
            $table->string('textura_clasificacion', 50)->comment('Clasificación textural');
            $table->string('fuente_datos', 50)->default('soilgrids')->comment('Fuente de los datos');
            $table->json('datos_fuente_json')->nullable()->comment('Datos completos de la fuente');
            $table->date('fecha_consulta')->comment('Fecha de consulta de datos');
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['unidad_productiva_id', 'fecha_consulta']);
            $table->index('textura_clasificacion');
            $table->index('ph_valor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caracteristicas_suelo');
    }
};
