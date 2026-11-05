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
        Schema::create('stock_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique(); // RIS number
            $table->string('department')->index(); // pharmacy, medical_supplies
            $table->string('requesting_unit'); // ICU, ER, OR, Ward 1, OPD, etc.
            $table->foreignId('requester_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('requester_name');
            $table->date('request_date');
            $table->string('status')->default('pending'); // pending, approved, issued, rejected, cancelled
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('issued_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('issued_at')->nullable();
            $table->text('purpose')->nullable();
            $table->string('patient_mrn')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_requests');
    }
};
