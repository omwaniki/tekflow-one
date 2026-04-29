<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();

            $table->string('assigned_to_name');
            $table->string('assigned_to_email')->nullable();
            $table->string('assigned_to_type')->default('staff'); // staff, student, agent, other

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('returned_at')->nullable();

            $table->string('status')->default('active'); // active, returned, lost

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_assignments');
    }
};