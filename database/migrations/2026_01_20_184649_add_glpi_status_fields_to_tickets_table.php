<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('estado_glpi')
                ->nullable()
                ->after('glpi_ticket_id');

            $table->timestamp('updated_at_estado_glpi')
                ->nullable()
                ->after('estado_glpi');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'estado_glpi',
                'updated_at_estado_glpi',
            ]);
        });
    }
};
