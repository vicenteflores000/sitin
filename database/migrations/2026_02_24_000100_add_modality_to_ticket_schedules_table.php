<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_schedules', function (Blueprint $table) {
            $table->string('modality')->default('remota')->after('end_at');
        });
    }

    public function down(): void
    {
        Schema::table('ticket_schedules', function (Blueprint $table) {
            $table->dropColumn('modality');
        });
    }
};
