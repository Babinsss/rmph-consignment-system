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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('delivery_item_id')->nullable()->constrained('delivery_items')->nullOnDelete();
            $table->string('department')->index(); // pharmacy, medical_supplies
            $table->string('movement_type')->index(); // DELIVERY_IN, REQUEST_OUT, RETURN_IN, RETURN_TO_SUPPLIER, ADJUSTMENT
            $table->string('reference_number')->index(); // DR #, RIS #, Adj #
            $table->nullableMorphs('reference'); // polymorphic link to Delivery, StockRequest, etc.
            $table->string('source_destination')->nullable(); // Supplier name or Requesting unit
            $table->string('lot_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('quantity_in')->default(0);
            $table->integer('quantity_out')->default(0);
            $table->integer('balance_after')->default(0);
            $table->decimal('unit_cost', 12, 2)->default(0.00);
            $table->decimal('total_cost', 14, 2)->default(0.00);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
