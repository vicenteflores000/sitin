<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('valores_atributo', function (Blueprint $table) {
            $table->id();

            $table->foreignId('activo_id')
                ->constrained('activos')
                ->cascadeOnDelete();

            $table->foreignId('atributo_id')
                ->constrained('atributos')
                ->cascadeOnDelete();

            $table->text('valor_string')->nullable();
            $table->integer('valor_integer')->nullable();
            $table->decimal('valor_decimal', 15, 4)->nullable();
            $table->boolean('valor_boolean')->nullable();
            $table->text('valor_text')->nullable();
            $table->date('valor_date')->nullable();
            $table->json('valor_json')->nullable();

            $table->timestamps();

            $table->unique(['activo_id', 'atributo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('valores_atributo');
    }
};
