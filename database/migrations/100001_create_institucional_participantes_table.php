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
        Schema::create('institucional_participantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users');
            $table->foreignId('institucion_id')->constrained('institucions');
            $table->enum('rol', ['admin', 'tecnico', 'investigador', 'educativo']);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['usuario_id', 'institucion_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institucional_participantes');
    }
};
