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
        Schema::create('campos', function (Blueprint $table) {
            $table->id();
            $table->string('localidad')->nullable();
            $table->string('paraje')->nullable();
            $table->decimal('latitud', 15, 13)->nullable();
            $table->decimal('longitud', 15, 13)->nullable();
            $table->string('nomenclatura_catastral')->nullable();
            $table->string('nombre')->nullable();
    
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campos');
    }
};
