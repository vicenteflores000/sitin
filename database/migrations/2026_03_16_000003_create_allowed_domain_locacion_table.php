<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('allowed_domain_locacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('locacion_id')->constrained('locaciones')->cascadeOnDelete();
            $table->foreignId('allowed_domain_id')->constrained('allowed_domains')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['locacion_id', 'allowed_domain_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('allowed_domain_locacion');
    }
};
