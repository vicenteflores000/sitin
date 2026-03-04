<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('allowed_domains', function (Blueprint $table) {
            $table->id();
            $table->string('domain')->unique();
            $table->timestamps();
        });

        DB::table('allowed_domains')->insert([
            [
                'domain' => 'salud.mdonihue.cl',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'domain' => 'mdonihue.cl',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('allowed_domains');
    }
};
