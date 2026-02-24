<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            return;
        }

        DB::statement('PRAGMA foreign_keys=OFF');
        DB::statement('ALTER TABLE tickets RENAME TO tickets_old');

        DB::statement(
            'CREATE TABLE "tickets" (' .
                '"id" integer primary key autoincrement not null, ' .
                '"tipo" varchar not null, ' .
                '"area" varchar not null, ' .
                '"categoria" varchar not null, ' .
                '"impacto" varchar not null, ' .
                '"pc" varchar not null, ' .
                '"usuario" varchar, ' .
                '"ip_origen" varchar not null, ' .
                '"descripcion" text not null, ' .
                '"origen" varchar not null default \'Formulario TI\', ' .
                '"estado_envio_glpi" varchar null default null, ' .
                '"glpi_ticket_id" integer, ' .
                '"payload_glpi" text, ' .
                '"created_at" datetime, ' .
                '"updated_at" datetime, ' .
                '"prioridad" integer, ' .
                '"urgencia" varchar, ' .
                '"usuario_mail" varchar, ' .
                '"ia_criticidad_sugerida" varchar, ' .
                '"ia_categoria_sugerida" varchar, ' .
                '"ia_modelo" varchar, ' .
                '"glpi_location_id" integer, ' .
                '"estado_glpi" varchar, ' .
                '"updated_at_estado_glpi" datetime, ' .
                '"locacion_id" integer' .
            ')'
        );

        DB::statement(
            'INSERT INTO "tickets" (' .
                '"id", "tipo", "area", "categoria", "impacto", "pc", "usuario", "ip_origen", ' .
                '"descripcion", "origen", "estado_envio_glpi", "glpi_ticket_id", "payload_glpi", ' .
                '"created_at", "updated_at", "prioridad", "urgencia", "usuario_mail", "ia_criticidad_sugerida", ' .
                '"ia_categoria_sugerida", "ia_modelo", "glpi_location_id", "estado_glpi", "updated_at_estado_glpi", "locacion_id"' .
            ') SELECT ' .
                '"id", "tipo", "area", "categoria", "impacto", "pc", "usuario", "ip_origen", ' .
                '"descripcion", "origen", "estado_envio_glpi", "glpi_ticket_id", "payload_glpi", ' .
                '"created_at", "updated_at", "prioridad", "urgencia", "usuario_mail", "ia_criticidad_sugerida", ' .
                '"ia_categoria_sugerida", "ia_modelo", "glpi_location_id", "estado_glpi", "updated_at_estado_glpi", "locacion_id" ' .
            'FROM "tickets_old"'
        );

        DB::statement('DROP TABLE tickets_old');
        DB::statement('PRAGMA foreign_keys=ON');
    }

    public function down(): void
    {
        // No-op: restoring NOT NULL in SQLite requires another table rebuild.
    }
};
