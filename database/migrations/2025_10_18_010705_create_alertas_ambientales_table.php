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
            
            // Tipo de alerta
            $table->enum('tipo', ['sequia', 'tormenta', 'estres_termico', 'helada']);
            
            // Nivel de gravedad
            $table->enum('nivel', ['critico', 'alto', 'medio', 'bajo']);
            
            // Mensaje y detalles
            $table->string('titulo');
            $table->text('mensaje');
            $table->json('datos_contexto')->nullable(); // Datos específicos (temp, lluvia, etc)
            
            // Estado
            $table->boolean('activa')->default(true);
            $table->boolean('leida')->default(false);
            $table->timestamp('fecha_inicio');
            $table->timestamp('fecha_fin')->nullable();
            
            // Notificaciones
            $table->boolean('notificado_email')->default(false);
            $table->boolean('notificado_sms')->default(false);
            $table->timestamp('fecha_notificacion')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index('unidad_productiva_id');
            $table->index(['activa', 'leida']);
            $table->index('tipo');
            $table->index('nivel');
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
