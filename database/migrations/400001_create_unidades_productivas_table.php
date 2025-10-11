<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('unidades_productivas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->nullable();
            $table->foreignId('campo_id')->nullable()->constrained('campos')->onDelete('cascade');
            $table->string('identificador_local', 50);
            $table->foreignId('tipo_identificador_id')->nullable()->constrained('tipos_identificador')->nullOnDelete();
            $table->boolean('activo')->default(true);
            // Agregar campo para superficie total de la unidad productiva
            // Este campo es opcional y puede ser utilizado para registrar la superficie total de la unidad productiva
            // en hectáreas, si se desea.
            // Si no se utiliza, puede dejarse como NULL.
            $table->decimal('superficie', 10, 2)->nullable(); // hectáreas
            $table->boolean('habita')->default(false)->comment('Indica si la unidad productiva está habitada');

            //coordenadas geográficas de la unidad productiva
            $table->decimal('latitud', 15, 13)->nullable();
            $table->decimal('longitud', 15, 13)->nullable();

            // Agua para humanos
            $table->foreignId('agua_humano_fuente_id')->nullable()->constrained('fuente_aguas')->nullOnDelete();
            $table->boolean('agua_humano_en_casa')->default(false);
            $table->integer('agua_humano_distancia')->nullable();

            // Agua para animales
            $table->foreignId('agua_animal_fuente_id')->nullable()->constrained('fuente_aguas')->nullOnDelete();
            $table->integer('agua_animal_distancia')->nullable();

            // Pasto / Suelo
            $table->foreignId('tipo_pasto_predominante_id')->nullable()->constrained('tipo_pastos')->nullOnDelete();
            $table->foreignId('tipo_suelo_predominante_id')->nullable()->constrained('tipo_suelos')->nullOnDelete();
            $table->boolean('forrajeras_predominante')->default(false);

            $table->text('observaciones')->nullable();

            $table->timestamps();

            $table->unique('identificador_local');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unidades_productivas');
    }
};