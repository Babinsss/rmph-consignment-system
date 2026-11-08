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
            $table->foreignId('batch_id')->constrained('batches')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('type', 20); // RECEIVE, ISSUE, ADJUST
            $table->integer('quantity');
            $table->unsignedInteger('balance_after');
            $table->date('movement_date');
            $table->string('reference_number', 100)->nullable();
            $table->string('reason', 255)->nullable();
            $table->timestamps();

            $table->index(['batch_id', 'movement_date']);
            $table->index(['type', 'movement_date']);
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
