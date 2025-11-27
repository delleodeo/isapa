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
            $table->boolean('paymongo_enabled')->default(false)->after('address');
            $table->string('paymongo_secret_key')->nullable()->after('paymongo_enabled');
            $table->string('paymongo_public_key')->nullable()->after('paymongo_secret_key');
            $table->decimal('commission_rate', 5, 2)->default(10.00)->after('paymongo_public_key');
            $table->decimal('commission_owed', 10, 2)->default(0.00)->after('commission_rate');
            $table->decimal('total_commission_paid', 10, 2)->default(0.00)->after('commission_owed');
            $table->timestamp('last_commission_payment_date')->nullable()->after('total_commission_paid');
            $table->enum('commission_payment_cycle', ['weekly', 'monthly'])->default('monthly')->after('last_commission_payment_date');
            $table->boolean('is_suspended')->default(false)->after('commission_payment_cycle');
            $table->text('suspended_reason')->nullable()->after('is_suspended');
            $table->timestamp('suspended_at')->nullable()->after('suspended_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
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
};
