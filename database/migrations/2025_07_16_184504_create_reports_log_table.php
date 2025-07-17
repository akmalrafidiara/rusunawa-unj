// database/migrations/YYYY_MM_DD_HHMMSS_create_reports_log_table.php
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
        Schema::create('reports_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // User yang melakukan update (Staff, Kepala Rusunawa, Admin)
            $table->string('action_by_role')->nullable(); // Role user yang melakukan aksi
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->text('notes')->nullable(); // Notes untuk setiap update status
            $table->timestamps();

            $table->index('report_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports_log');
    }
};