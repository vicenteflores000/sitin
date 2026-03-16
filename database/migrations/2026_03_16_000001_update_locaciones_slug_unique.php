<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('locaciones', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->unique(['locacion_padre_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::table('locaciones', function (Blueprint $table) {
            $table->dropUnique(['locacion_padre_id', 'slug']);
            $table->unique('slug');
        });
    }
};
