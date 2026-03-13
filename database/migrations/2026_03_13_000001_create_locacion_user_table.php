<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locacion_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('locacion_id')->constrained('locaciones')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['locacion_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locacion_user');
    }
};
