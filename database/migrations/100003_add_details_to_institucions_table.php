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
            $table->string('email_secundario')->nullable()->after('contacto_email');
            $table->string('telefono')->nullable()->after('email_secundario');
            $table->string('localidad')->nullable()->after('telefono');
            $table->string('provincia')->nullable()->after('localidad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('institucions', function (Blueprint $table) {
            $table->dropColumn(['email_secundario', 'telefono', 'localidad', 'provincia']);
        });
    }
};
