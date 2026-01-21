<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            $table->string('tipo', 20);
            $table->string('area', 50);
            $table->string('categoria', 50);
            $table->string('impacto', 50);

            $table->string('pc', 100);
            $table->string('usuario', 100)->nullable();
            $table->string('ip_origen', 45);

            $table->text('descripcion');

            $table->string('origen', 50)->default('Formulario TI');

            $table->string('estado_envio_glpi', 20)->default('pendiente');
            $table->unsignedBigInteger('glpi_ticket_id')->nullable();

            $table->json('payload_glpi')->nullable();

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
