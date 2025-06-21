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
        Schema::create('occupant_type_unit_cluster', function (Blueprint $table) {
            $table->primary(['occupant_type_id', 'unit_cluster_id']);
            $table->foreignId('occupant_type_id')->constrained('occupant_types')->onDelete('cascade');
            $table->foreignId('unit_cluster_id')->constrained('unit_clusters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('occupant_type_unit_cluster');
    }
};
