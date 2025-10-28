<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion_alertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('productor_id')->constrained('productors')->cascadeOnDelete();
            
            // Umbrales de Sequía
            $table->integer('sequia_dias_sin_lluvia')->default(15);
            $table->decimal('sequia_temperatura_umbral', 4, 1)->default(32.0);
            $table->integer('sequia_dias_consecutivos')->default(5);
            
            // Umbrales de Tormenta
            $table->decimal('tormenta_lluvia_umbral', 5, 1)->default(50.0);
            $table->decimal('tormenta_viento_umbral', 5, 1)->default(60.0);
            
            // Umbrales de Estrés Térmico
            $table->decimal('estres_temperatura_umbral', 4, 1)->default(35.0);
            $table->integer('estres_dias_consecutivos')->default(3);
            
            // Umbrales de Helada
            $table->decimal('helada_temperatura_umbral', 4, 1)->default(5.0);
            
            // Preferencias
            $table->boolean('notificaciones_email')->default(true);
            $table->boolean('notificaciones_sms')->default(false);
            
            $table->timestamps();
            
            // Un solo registro de configuración por productor
            $table->unique('productor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_alertas');
    }
};
