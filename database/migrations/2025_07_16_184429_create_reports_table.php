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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->foreignId('contract_id')->constrained('contracts')->onDelete('cascade');
            $table->enum('reporter_type', ['room', 'individual']); 
            $table->foreignId('reporter_id'); 
            $table->string('subject');
            $table->text('description'); 
            $table->enum('status', ['report_received', 'in_process', 'disposed_to_admin', 'disposed_to_rusunawa', 'completed', 'confirmed_completed'])->default('report_received');
            $table->foreignId('current_handler_id')->nullable()->constrained('users')->onDelete('set null'); // User (admin/staff/kepala) yang sedang menangani
            $table->date('completion_deadline')->nullable(); // Batas waktu konfirmasi 7 hari
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};