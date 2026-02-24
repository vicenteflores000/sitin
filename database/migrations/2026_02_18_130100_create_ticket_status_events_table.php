<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ticket_status_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->index(['ticket_id', 'to_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_status_events');
    }
};
