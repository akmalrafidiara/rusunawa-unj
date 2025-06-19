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
        Schema::create('unit_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_type_id')->constrained('unit_types')->onDelete('cascade');
            $table->foreignId('occupant_type_id')->constrained('occupant_types')->onDelete('cascade');
            $table->enum('pricing_basis', ['per_night', 'per_month']);
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('max_price')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['unit_type_id', 'occupant_type_id', 'pricing_basis']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_prices');
    }
};
