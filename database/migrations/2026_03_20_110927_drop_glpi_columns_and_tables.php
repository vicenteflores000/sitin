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
        if (Schema::hasTable('tickets')) {
            $columns = [
                'estado_envio_glpi',
                'glpi_ticket_id',
                'payload_glpi',
                'glpi_location_id',
                'estado_glpi',
                'updated_at_estado_glpi',
            ];

            $drop = array_values(array_filter($columns, function (string $column) {
                return Schema::hasColumn('tickets', $column);
            }));

            if (! empty($drop)) {
                Schema::table('tickets', function (Blueprint $table) use ($drop) {
                    $table->dropColumn($drop);
                });
            }
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'glpi_user_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('glpi_user_id');
            });
        }

        if (Schema::hasTable('glpi_locations')) {
            Schema::drop('glpi_locations');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('glpi_locations')) {
            // Table already exists; avoid recreating.
        } else {
            Schema::create('glpi_locations', function (Blueprint $table) {
                $table->id();
                $table->integer('glpi_id')->unique();
                $table->string('name');
                $table->integer('parent_id')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'glpi_user_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('glpi_user_id')->nullable()->after('email');
            });
        }

        if (Schema::hasTable('tickets')) {
            Schema::table('tickets', function (Blueprint $table) {
                if (! Schema::hasColumn('tickets', 'estado_envio_glpi')) {
                    $table->string('estado_envio_glpi', 20)->nullable();
                }
                if (! Schema::hasColumn('tickets', 'glpi_ticket_id')) {
                    $table->unsignedBigInteger('glpi_ticket_id')->nullable();
                }
                if (! Schema::hasColumn('tickets', 'payload_glpi')) {
                    $table->json('payload_glpi')->nullable();
                }
                if (! Schema::hasColumn('tickets', 'glpi_location_id')) {
                    $table->integer('glpi_location_id')->nullable();
                }
                if (! Schema::hasColumn('tickets', 'estado_glpi')) {
                    $table->string('estado_glpi')->nullable();
                }
                if (! Schema::hasColumn('tickets', 'updated_at_estado_glpi')) {
                    $table->timestamp('updated_at_estado_glpi')->nullable();
                }
            });
        }
    }
};
