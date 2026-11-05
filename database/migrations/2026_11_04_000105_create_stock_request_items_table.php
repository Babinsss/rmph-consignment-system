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
        Schema::create('stock_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_request_id')->constrained('stock_requests')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('delivery_item_id')->nullable()->constrained('delivery_items')->nullOnDelete();
            $table->integer('quantity_requested');
            $table->integer('quantity_issued')->default(0);
            $table->decimal('unit_cost', 12, 2)->default(0.00);
            $table->decimal('total_cost', 14, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_request_items');
    }
};
