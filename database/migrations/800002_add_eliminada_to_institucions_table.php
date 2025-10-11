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
        Schema::table('institucions', function (Blueprint $table) {
            $table->boolean('eliminada')->default(false)->after('validada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('institucions', function (Blueprint $table) {
            $table->dropColumn('eliminada');
        });
    }
};