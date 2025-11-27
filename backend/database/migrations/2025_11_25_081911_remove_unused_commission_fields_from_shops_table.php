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
        Schema::table('shops', function (Blueprint $table) {
            // Remove PayMongo and commission-related columns that are not being used
            $table->dropColumn([
                'paymongo_enabled',
                'paymongo_secret_key',
                'paymongo_public_key',
                'commission_rate',
                'commission_owed',
                'total_commission_paid',
                'last_commission_payment_date',
                'commission_payment_cycle',
                'is_suspended',
                'suspended_reason',
                'suspended_at'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            // Restore the columns if needed
            $table->boolean('paymongo_enabled')->default(false);
            $table->string('paymongo_secret_key')->nullable();
            $table->string('paymongo_public_key')->nullable();
            $table->decimal('commission_rate', 5, 2)->default(10.00);
            $table->decimal('commission_owed', 10, 2)->default(0.00);
            $table->decimal('total_commission_paid', 10, 2)->default(0.00);
            $table->timestamp('last_commission_payment_date')->nullable();
            $table->enum('commission_payment_cycle', ['weekly', 'monthly'])->default('monthly');
            $table->boolean('is_suspended')->default(false);
            $table->text('suspended_reason')->nullable();
            $table->timestamp('suspended_at')->nullable();
        });
    }
};
