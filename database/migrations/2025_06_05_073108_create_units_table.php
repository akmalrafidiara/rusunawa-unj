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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique();
            $table->integer('capacity')->default(1);
            $table->string('virtual_account_number')->nullable();
            $table->enum('gender_allowed', ['male', 'female', 'general'])->default('general');
            $table->enum('status', ['available', 'not_available', 'occupied', 'under_maintenance'])->default('available');
            $table->string('notes')->nullable();
            $table->string('image')->nullable();
            $table->foreignId('unit_type_id')->constrained('unit_types')->onDelete('cascade');
            $table->foreignId('unit_cluster_id')->constrained('unit_clusters')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
