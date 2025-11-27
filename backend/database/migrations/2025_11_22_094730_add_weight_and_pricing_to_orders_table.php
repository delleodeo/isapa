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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('weight_kg', 8, 2)->nullable()->after('total_amount'); // Weight in kilograms
            $table->decimal('final_amount', 10, 2)->nullable()->after('weight_kg'); // Final calculated amount
            $table->decimal('final_shop_revenue', 10, 2)->nullable()->after('final_amount'); // Final shop revenue (90%)
            $table->decimal('final_admin_revenue', 10, 2)->nullable()->after('final_shop_revenue'); // Final admin revenue (10%)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['weight_kg', 'final_amount', 'final_shop_revenue', 'final_admin_revenue']);
        });
    }
};
