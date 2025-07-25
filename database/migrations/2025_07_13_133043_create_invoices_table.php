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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('contract_id')->constrained('contracts')->onDelete('cascade');

            $table->string('description');
            $table->unsignedInteger('amount');

            $table->timestamp('due_at');
            $table->timestamp('paid_at')->nullable();

            $table->enum('status', [
                'unpaid',
                'paid',
                'pending_payment_verification',
                'overdue',
                'cancelled',
            ])->default('unpaid');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
