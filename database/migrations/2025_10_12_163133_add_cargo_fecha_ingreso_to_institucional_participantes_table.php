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
        Schema::table('institucional_participantes', function (Blueprint $table) {
            $table->string('cargo')->nullable()->after('rol');
            $table->date('fecha_ingreso')->nullable()->after('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('institucional_participantes', function (Blueprint $table) {
            $table->dropColumn(['cargo', 'fecha_ingreso']);
        });
    }
};
