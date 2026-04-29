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
        Schema::create('asset_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();

            $table->foreignId('from_campus_id')->nullable()->constrained('campuses')->nullOnDelete();
            $table->foreignId('to_campus_id')->nullable()->constrained('campuses')->nullOnDelete();

            $table->string('movement_type');
            $table->text('reason')->nullable();
            $table->date('movement_date');

            $table->foreignId('performed_by')->constrained('users')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_movements');
    }
};