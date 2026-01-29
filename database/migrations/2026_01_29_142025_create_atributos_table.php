<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('atributos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->string('tipo_dato', 20)->default('string');
            $table->string('unidad')->nullable();
            $table->boolean('es_requerido')->default(false);
            $table->timestamps();
        });
        Schema::create('atributo_tipo_activo', function (Blueprint $table) {
            $table->foreignId('atributo_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tipo_activo_id')->constrained('tipos_activo')->cascadeOnDelete();
            $table->primary(['atributo_id', 'tipo_activo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atributos');
        Schema::dropIfExists('atributo_tipo_activo');
    }
};
