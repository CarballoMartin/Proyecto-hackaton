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
        Schema::create('unidad_productiva_dispositivo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidad_productiva_id')->constrained('unidades_productivas');
            $table->foreignId('dispositivo_id')->constrained('dispositivos');
            $table->timestamps();

            $table->unique(['unidad_productiva_id', 'dispositivo_id'], 'up_dispositivo_unique'); // Evita duplicados
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidad_productiva_dispositivo');
    }
};
