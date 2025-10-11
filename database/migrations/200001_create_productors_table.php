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
        Schema::create('productors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()->constrained('users');
            $table->string('nombre');
            $table->string('dni', 20)->nullable()->unique();
            $table->string('cuil', 20)->nullable()->unique();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('municipio')->nullable();
            $table->string('paraje')->nullable();
            $table->text('direccion')->nullable();
            $table->string('telefono', 50)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productors');
    }
};
