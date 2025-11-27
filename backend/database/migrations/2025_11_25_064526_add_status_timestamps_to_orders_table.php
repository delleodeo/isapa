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
            $table->timestamp('approved_at')->nullable()->after('order_status');
            $table->timestamp('processing_at')->nullable()->after('approved_at');
            $table->timestamp('ready_for_pickup_at')->nullable()->after('processing_at');
            $table->timestamp('for_delivery_at')->nullable()->after('ready_for_pickup_at');
            $table->timestamp('delivered_at')->nullable()->after('for_delivery_at');
            $table->timestamp('picked_up_at')->nullable()->after('delivered_at');
            $table->timestamp('cancelled_at')->nullable()->after('picked_up_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'approved_at',
                'processing_at',
                'ready_for_pickup_at',
                'for_delivery_at',
                'delivered_at',
                'picked_up_at',
                'cancelled_at'
            ]);
        });
    }
};
