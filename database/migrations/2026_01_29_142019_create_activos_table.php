<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('activos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tipo_activo_id')
                ->constrained('tipos_activo')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('nombre');
            $table->string('codigo_interno')->nullable()->unique();

            $table->enum('estado', [
                'operativo',
                'en_reparacion',
                'dado_de_baja'
            ])->default('operativo');

            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activos');
    }
};
