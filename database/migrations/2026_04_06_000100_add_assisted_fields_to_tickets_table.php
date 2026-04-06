<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('assisted_by')->nullable()->after('origen')->constrained('users')->nullOnDelete();
            $table->string('assisted_channel', 50)->nullable()->after('assisted_by');
            $table->string('assisted_reason', 255)->nullable()->after('assisted_channel');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['assisted_by']);
            $table->dropColumn(['assisted_by', 'assisted_channel', 'assisted_reason']);
        });
    }
};
