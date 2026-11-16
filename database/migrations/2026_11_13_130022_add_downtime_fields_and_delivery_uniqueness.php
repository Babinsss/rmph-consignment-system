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
        Schema::table('requests', function (Blueprint $table) {
            $table->boolean('is_downtime')->default(false)->after('is_urgent');
            $table->string('downtime_reference', 100)->nullable()->after('is_downtime');
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->unique(['consignor_id', 'dr_number'], 'deliveries_consignor_dr_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropUnique('deliveries_consignor_dr_unique');
        });

        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn(['is_downtime', 'downtime_reference']);
        });
    }
};
