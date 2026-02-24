<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ticket_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('technician_id');
            $table->timestamp('assigned_at');
            $table->timestamp('unassigned_at')->nullable();
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->timestamps();

            $table->index(['ticket_id', 'technician_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_assignments');
    }
};
