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
        Schema::table('configuracion_actualizacions', function (Blueprint $table) {
            $table->integer('proxima_frecuencia_dias')->nullable()->after('activo')->comment('Frecuencia en días que se aplicará en el próximo ciclo.');
            $table->boolean('proxima_activo')->nullable()->after('proxima_frecuencia_dias')->comment('Estado activo/inactivo que se aplicará en el próximo ciclo.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configuracion_actualizacions', function (Blueprint $table) {
            $table->dropColumn(['proxima_frecuencia_dias', 'proxima_activo']);
        });
    }
};