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
        Schema::create('guest_questions', function (Blueprint $table) {
            $table->id();
            $table->string('fullName');
            $table->string('formPhoneNumber');
            $table->string('formEmail');
            $table->text('message');
            $table->boolean('is_read')->default(false); // Default value for is_read                    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_questions');
    }
};
