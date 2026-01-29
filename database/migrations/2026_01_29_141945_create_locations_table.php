<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('locaciones', function (Blueprint $table) {
            $table->id();

            $table->string('nombre');
            $table->string('slug')->unique();

            $table->unsignedBigInteger('locacion_padre_id')->nullable();

            $table->foreign('locacion_padre_id')
                ->references('id')
                ->on('locaciones')
                ->nullOnDelete();

            $table->boolean('activa')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locaciones');
    }
};
