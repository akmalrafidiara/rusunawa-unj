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
        Schema::create('occupants', function (Blueprint $table) {
            $table->id();
            
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('whatsapp_number');

            $table->string('identity_card_file');
            $table->string('community_card_file')->nullable();

            $table->boolean('is_student')->default(false);
            $table->string('student_id')->nullable();
            $table->string('faculty')->nullable();
            $table->string('study_program')->nullable();
            $table->year('class_year')->nullable();

            $table->boolean('agree_to_regulations')->default(false);
            $table->string('notes')->nullable();
            
            $table->enum('status', [
                'pending_verification', 
                'active', 
                'inactive', 
                'rejected'
            ])->default('pending_verification');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('occupants');
    }
};
