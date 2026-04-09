<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `tickets` MODIFY `tipo` VARCHAR(120) NOT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE tickets ALTER COLUMN tipo TYPE VARCHAR(120)');
        } else {
            Schema::table('tickets', function ($table) {
                $table->string('tipo', 120)->change();
            });
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `tickets` MODIFY `tipo` VARCHAR(20) NOT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE tickets ALTER COLUMN tipo TYPE VARCHAR(20)');
        } else {
            Schema::table('tickets', function ($table) {
                $table->string('tipo', 20)->change();
            });
        }
    }
};
