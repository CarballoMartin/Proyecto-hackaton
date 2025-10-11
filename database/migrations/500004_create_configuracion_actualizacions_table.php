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
        Schema::create('configuracion_actualizacions', function (Blueprint $table) {
            $table->id();
            $table->integer('frecuencia_dias')->default(7);
            $table->date('ultima_actualizacion');
            $table->date('proxima_actualizacion');
            $table->boolean('activo')->default(true);
            $table->foreignId('superadmin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('fecha_configuracion')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracion_actualizacions');
    }
};
