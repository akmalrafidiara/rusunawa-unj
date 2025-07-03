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
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            $table->foreignId('maintenance_schedule_id')->nullable()->constrained('maintenance_schedules')->onDelete('set null');
            $table->enum('type', ['routine', 'urgent'])->default('routine');
            $table->date('scheduled_date');
            $table->timestamp('completion_date')->nullable();
            $table->enum('status', ['scheduled', 'completed_on_time', 'completed_late', 'completed_early', 'postponed', 'urgent'])->default('scheduled');
            $table->text('notes')->nullable();
            // Kolom 'photo_proof' akan dihapus oleh migrasi selanjutnya jika sudah ada
            // $table->string('photo_proof')->nullable(); // Ini dihapus
            $table->boolean('is_late')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
    }
};