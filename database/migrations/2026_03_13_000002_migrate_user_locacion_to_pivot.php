<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'locacion_id') && Schema::hasTable('locacion_user')) {
            $rows = DB::table('users')
                ->whereNotNull('locacion_id')
                ->select('id as user_id', 'locacion_id')
                ->get();

            foreach ($rows as $row) {
                DB::table('locacion_user')->updateOrInsert(
                    ['locacion_id' => $row->locacion_id, 'user_id' => $row->user_id],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        if (Schema::hasColumn('users', 'locacion_id')) {
            Schema::table('users', function ($table) {
                $table->dropForeign(['locacion_id']);
                $table->dropColumn('locacion_id');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('users', 'locacion_id')) {
            Schema::table('users', function ($table) {
                $table->foreignId('locacion_id')->nullable()->constrained('locaciones')->nullOnDelete();
            });
        }

        if (Schema::hasTable('locacion_user')) {
            $rows = DB::table('locacion_user')->get();
            foreach ($rows as $row) {
                DB::table('users')
                    ->where('id', $row->user_id)
                    ->update(['locacion_id' => $row->locacion_id]);
            }
        }
    }
};
