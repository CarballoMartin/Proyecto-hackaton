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
        // 1. Create the new table for movement reasons
        Schema::create('motivo_movimientos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('tipo', ['alta', 'baja']);
            $table->timestamps();
        });

        // 2. Add new columns to the stock_animals table
        Schema::table('stock_animals', function (Blueprint $table) {
            $table->foreignId('motivo_movimiento_id')->nullable()->after('tipo_registro_id')->constrained('motivo_movimientos');
            $table->string('destino_traslado')->nullable()->after('motivo_movimiento_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback in reverse order of creation
        Schema::table('stock_animals', function (Blueprint $table) {
            $table->dropForeign(['motivo_movimiento_id']);
            $table->dropColumn('motivo_movimiento_id');
            $table->dropColumn('destino_traslado');
        });

        Schema::dropIfExists('motivo_movimientos');
    }
};