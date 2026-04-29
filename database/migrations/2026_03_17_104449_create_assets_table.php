<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();

            $table->enum('type', ['staff', 'student']);

            $table->string('name')->nullable();

            $table->string('assigned_to_name')->nullable();
            $table->string('assigned_to_email')->nullable();
            $table->string('role')->nullable();

            $table->string('device_type')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->unique();

            $table->enum('status', ['active', 'faulty', 'retired'])->default('active');
            $table->date('manufacture_date')->nullable();

            $table->foreignId('campus_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};