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
        Schema::create('alertas_ambientales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidad_productiva_id')->constrained('unidades_productivas')->onDelete('cascade');
            $table->string('tipo_alerta', 50)->comment('sequia, tormenta, estres_termico, helada, viento, ndvi_bajo, suelo_degradado');
            $table->string('severidad', 20)->comment('baja, media, alta, critica');
            $table->string('titulo', 200)->comment('Título descriptivo de la alerta');
            $table->text('descripcion')->comment('Descripción detallada de la alerta');
            $table->json('datos_alerta')->nullable()->comment('Datos específicos que generaron la alerta');
            $table->json('recomendaciones')->nullable()->comment('Recomendaciones para manejar la alerta');
            $table->date('fecha_inicio')->comment('Fecha de inicio del evento');
            $table->date('fecha_fin')->nullable()->comment('Fecha de fin del evento (si aplica)');
            $table->boolean('activa')->default(true)->comment('Si la alerta está activa');
            $table->boolean('notificada')->default(false)->comment('Si ya se notificó al usuario');
            $table->timestamp('fecha_notificacion')->nullable()->comment('Fecha de notificación');
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['unidad_productiva_id', 'activa']);
            $table->index(['tipo_alerta', 'severidad']);
            $table->index('fecha_inicio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alertas_ambientales');
    }
};
