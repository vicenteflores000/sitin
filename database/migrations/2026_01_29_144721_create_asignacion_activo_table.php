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
        Schema::create('asignacion_activo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activo_id')
                ->constrained('activos')
                ->cascadeOnDelete();
            $table->enum('tipo_asignacion', ['usuario', 'locacion']);
            $table->unsignedBigInteger('asignable_id');
            $table->date('fecha_asignacion');
            $table->date('fecha_desasignacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacion_activo');
    }
};
