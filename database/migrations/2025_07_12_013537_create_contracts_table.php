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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_code')->unique();
            $table->foreignId('contract_pic')->nullable()->constrained('occupants')->onDelete('cascade');

            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            $table->foreignId('occupant_type_id')->constrained('occupant_types');

            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('pricing_basis', ['per_night', 'per_month']);
            $table->unsignedInteger('total_price');

            $table->timestamp('expired_date')->nullable();

            $table->enum('key_status', [
               'pending_handover',
               'handed_over',
               'returned',
               'lost',
           ])->nullable();

            $table->enum('status', [
                'pending_payment',
                'active',
                'expired',
                'cancelled',
                'terminated',
            ])->default('pending_payment');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
