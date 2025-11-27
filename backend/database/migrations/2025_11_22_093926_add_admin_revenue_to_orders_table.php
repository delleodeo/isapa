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
            $table->decimal('shop_revenue', 10, 2)->after('total_amount')->default(0); // 90% to shop
            $table->decimal('admin_revenue', 10, 2)->after('shop_revenue')->default(0); // 10% to admin
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shop_revenue', 'admin_revenue']);
        });
    }
};
