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
        Schema::create('datos_climaticos_cache', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidad_productiva_id')->constrained('unidades_productivas')->onDelete('cascade');
            $table->string('fuente')->default('open_meteo'); // open_meteo, nasa_power
            
            // Datos actuales
            $table->decimal('temperatura_actual', 5, 2)->nullable();
            $table->decimal('velocidad_viento', 5, 2)->nullable();
            $table->integer('codigo_clima')->nullable(); // Weather code
            
            // Datos diarios (próximos 7 días)
            $table->json('temperaturas_max')->nullable(); // Array de 7 valores
            $table->json('temperaturas_min')->nullable();
            $table->json('precipitacion')->nullable();
            $table->json('probabilidad_lluvia')->nullable();
            $table->json('viento_max')->nullable();
            $table->json('fechas')->nullable(); // Array de fechas
            
            // Metadatos
            $table->json('datos_completos')->nullable(); // Respuesta completa de la API
            $table->timestamp('fecha_consulta');
            $table->timestamps();
            
            // Índices
            $table->index('unidad_productiva_id');
            $table->index('fecha_consulta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datos_climaticos_cache');
    }
};
