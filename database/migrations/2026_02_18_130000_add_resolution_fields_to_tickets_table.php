<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('categoria_interna')->nullable()->after('categoria');
            $table->string('problem_type')->nullable()->after('categoria_interna');
            $table->unsignedBigInteger('resolved_by')->nullable()->after('problem_type');
            $table->timestamp('resolved_at')->nullable()->after('resolved_by');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'categoria_interna',
                'problem_type',
                'resolved_by',
                'resolved_at',
            ]);
        });
    }
};
