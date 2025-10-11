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
        Schema::create('superficies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidad_productiva_id')->constrained('unidades_productivas');
            $table->decimal('total_hectareas', 6, 2)->nullable();
            $table->decimal('hectareas_ovinos', 6, 2)->nullable();
            $table->decimal('hectareas_bovinos', 6, 2)->nullable();
            $table->decimal('superficie_frutales', 6, 2)->nullable();
            $table->decimal('superficie_hortalizas', 6, 2)->nullable();
            $table->decimal('superficie_agricultura', 6, 2)->nullable();
            $table->boolean('dedica_forestal')->nullable();
            $table->text('tipo_forestal')->nullable();
            $table->boolean('monte')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('superficies');
    }
};
