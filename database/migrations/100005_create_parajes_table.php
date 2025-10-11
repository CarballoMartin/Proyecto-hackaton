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
        Schema::create('parajes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('municipio_id')->nullable()->constrained('municipios')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['municipio_id', 'nombre']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parajes');
    }
};