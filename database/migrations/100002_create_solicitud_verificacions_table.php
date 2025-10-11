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
        Schema::create('solicitud_verificacions', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_institucion');
            $table->string('email_contacto')->unique();
            $table->string('cuit')->nullable();// Pendiente confirmar si se solicita cuit u otro numero unico de registro
            $table->string('nombre_solicitante');
            $table->string('telefono_contacto')->nullable();
            $table->string('mensaje')->nullable();
            $table->string('localidad')->nullable();
            $table->string('provincia')->nullable();
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_verificacions');
    }
};
