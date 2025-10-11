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
        Schema::table('unidades_productivas', function (Blueprint $table) {
            $table->foreignId('municipio_id')->nullable()->after('superficie')->constrained('municipios')->nullOnDelete();
            $table->foreignId('paraje_id')->nullable()->after('municipio_id')->constrained('parajes')->nullOnDelete();
            $table->boolean('completo')->default(false)->after('activo');
            $table->boolean('activo')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unidades_productivas', function (Blueprint $table) {
            $table->dropForeign(['municipio_id']);
            $table->dropForeign(['paraje_id']);
            $table->dropColumn(['municipio_id', 'paraje_id', 'completo']);
            $table->boolean('activo')->default(true)->change();
        });
    }
};