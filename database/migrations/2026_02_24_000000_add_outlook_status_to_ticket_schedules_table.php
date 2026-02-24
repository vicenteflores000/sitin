<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_schedules', function (Blueprint $table) {
            $table->string('outlook_status')->default('pending')->after('outlook_event_id');
            $table->text('outlook_error')->nullable()->after('outlook_status');
        });

        DB::table('ticket_schedules')
            ->whereNotNull('outlook_event_id')
            ->update(['outlook_status' => 'synced']);
    }

    public function down(): void
    {
        Schema::table('ticket_schedules', function (Blueprint $table) {
            $table->dropColumn(['outlook_status', 'outlook_error']);
        });
    }
};
