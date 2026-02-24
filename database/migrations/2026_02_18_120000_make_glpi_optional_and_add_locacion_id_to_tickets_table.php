<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'locacion_id')) {
                $table->unsignedBigInteger('locacion_id')->nullable()->after('glpi_location_id');
            }
        });

        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE tickets MODIFY estado_envio_glpi VARCHAR(20) NULL DEFAULT NULL");
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE tickets ALTER COLUMN estado_envio_glpi DROP NOT NULL');
            DB::statement('ALTER TABLE tickets ALTER COLUMN estado_envio_glpi DROP DEFAULT');
        }
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'locacion_id')) {
                $table->dropColumn('locacion_id');
            }
        });

        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE tickets MODIFY estado_envio_glpi VARCHAR(20) NOT NULL DEFAULT 'pendiente'");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE tickets ALTER COLUMN estado_envio_glpi SET NOT NULL");
            DB::statement("ALTER TABLE tickets ALTER COLUMN estado_envio_glpi SET DEFAULT 'pendiente'");
        }
    }
};
