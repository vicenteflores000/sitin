<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'locacion_hija_texto')) {
                $table->string('locacion_hija_texto', 255)->nullable()->after('locacion_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'locacion_hija_texto')) {
                $table->dropColumn('locacion_hija_texto');
            }
        });
    }
};
