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
            $table->enum('payment_method', ['online', 'cash'])->default('cash')->after('order_status');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'cancelled'])->default('pending')->after('payment_method');
            $table->string('paymongo_payment_id')->nullable()->after('payment_status');
            $table->string('paymongo_source_id')->nullable()->after('paymongo_payment_id');
            $table->string('paymongo_payment_method')->nullable()->after('paymongo_source_id')->comment('gcash, paymaya, card');
            $table->json('payment_details')->nullable()->after('paymongo_payment_method');
            $table->timestamp('paid_at')->nullable()->after('payment_details');
            $table->timestamp('cash_confirmed_at')->nullable()->after('paid_at');
            $table->unsignedBigInteger('cash_confirmed_by')->nullable()->after('cash_confirmed_at');
            
            $table->index('payment_method');
            $table->index('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['payment_method']);
            $table->dropIndex(['payment_status']);
            $table->dropColumn([
                'payment_method',
                'payment_status',
                'paymongo_payment_id',
                'paymongo_source_id',
                'paymongo_payment_method',
                'payment_details',
                'paid_at',
                'cash_confirmed_at',
                'cash_confirmed_by'
            ]);
        });
    }
};
