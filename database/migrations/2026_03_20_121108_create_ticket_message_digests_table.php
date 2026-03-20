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
        Schema::create('ticket_message_digests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->string('recipient_email');
            $table->unsignedInteger('pending_count')->default(0);
            $table->unsignedBigInteger('last_sent_message_id')->nullable();
            $table->timestamp('send_after')->nullable();
            $table->timestamps();

            $table->unique(['ticket_id', 'recipient_email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_message_digests');
    }
};
