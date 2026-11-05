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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->unique();
            $table->string('name');
            $table->string('generic_name')->nullable();
            $table->string('brand_name')->nullable();
            $table->text('description')->nullable();
            $table->string('department')->index(); // pharmacy, medical_supplies
            $table->string('category')->nullable()->index();
            $table->string('uom'); // piece, box, vial, ampoule, bottle, pack, roll, etc.
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->integer('minimum_stock_level')->default(10);
            $table->decimal('unit_cost', 12, 2)->default(0.00);
            $table->decimal('selling_price', 12, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
