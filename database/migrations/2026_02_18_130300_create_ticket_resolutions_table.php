<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ticket_resolutions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id')->unique();
            $table->string('categoria_interna');
            $table->string('root_cause')->nullable();
            $table->text('resolution_text');
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->timestamp('resolved_at');
            $table->timestamps();

            $table->index(['ticket_id', 'resolved_by']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_resolutions');
    }
};
