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
        if (!Schema::hasColumn('agents', 'region_id')) {
            Schema::table('agents', function (Blueprint $table) {
                $table->foreignId('region_id')
                      ->nullable()
                      ->after('campus_id')
                      ->constrained()
                      ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('agents', 'region_id')) {
            Schema::table('agents', function (Blueprint $table) {
                $table->dropForeign(['region_id']);
                $table->dropColumn('region_id');
            });
        }
    }
};