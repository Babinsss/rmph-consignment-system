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
        Schema::create('delivery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('deliveries')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->string('lot_number')->nullable()->index();
            $table->date('expiry_date')->nullable()->index();
            $table->integer('quantity_received');
            $table->integer('quantity_remaining'); // Current live stock in this batch
            $table->decimal('unit_cost', 12, 2)->default(0.00);
            $table->decimal('total_cost', 14, 2)->default(0.00);
            $table->string('status')->default('available'); // available, depleted, expired, quarantined
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_items');
    }
};
