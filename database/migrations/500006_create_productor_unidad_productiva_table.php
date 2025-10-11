<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productor_unidad_productiva', function (Blueprint $table) {
            $table->id();
            $table->foreignId('productor_id')->constrained('productors')->onDelete('cascade');
            $table->foreignId('condicion_tenencia_id')->nullable()->constrained('condicion_tenencias')->nullOnDelete();
            $table->foreignId('unidad_productiva_id')->constrained('unidades_productivas')->onDelete('cascade');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->timestamps();

            $table->unique(['productor_id', 'unidad_productiva_id', 'fecha_inicio'], 'unique_productor_unidad_inicio');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productor_unidad_productiva');
    }
};