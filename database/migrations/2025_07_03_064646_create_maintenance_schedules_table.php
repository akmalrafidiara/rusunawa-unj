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
        Schema::create('maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            $table->integer('frequency_months');
            $table->date('next_due_date');
            $table->timestamp('last_completed_at')->nullable()->default(null);
            $table->enum('status', ['scheduled', 'upcoming', 'overdue', 'postponed'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique('unit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_schedules');
    }
};