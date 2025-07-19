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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('verified_by')->nullable()->constrained('users');

            $table->unsignedInteger('amount_paid')->default(0);
            $table->string('proof_of_payment_path');

            $table->text('notes')->nullable();

            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamp('verified_at')->nullable();

            $table->enum('status', [
                'pending_verification',
                'approved',
                'rejected',
            ])->default('pending_verification');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
