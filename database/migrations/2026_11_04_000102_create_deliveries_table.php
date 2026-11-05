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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_number')->unique(); // DR number
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->string('department')->index(); // pharmacy, medical_supplies
            $table->date('delivery_date');
            $table->foreignId('received_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('po_reference')->nullable();
            $table->decimal('total_cost', 14, 2)->default(0.00);
            $table->string('status')->default('received'); // pending, received, cancelled
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
        Schema::dropIfExists('deliveries');
    }
};
