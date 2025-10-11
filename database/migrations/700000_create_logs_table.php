<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            $table->string('accion');         // ejemplo: login, crear, editar, eliminar
            $table->string('modelo')->nullable();   // modelo afectado (ej: Campo)
            $table->unsignedBigInteger('modelo_id')->nullable(); // id del registro afectado
            $table->text('descripcion')->nullable(); // detalle de la acción
            $table->ipAddress('ip')->nullable();
            $table->string('navegador')->nullable(); // User-Agent
            $table->timestamps();

            $table->index(['modelo', 'modelo_id']); // consultas rápidas
        });
    }

    public function down(): void {
        Schema::dropIfExists('logs');
    }
};
