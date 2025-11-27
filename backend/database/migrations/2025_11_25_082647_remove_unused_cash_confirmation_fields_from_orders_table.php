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
            // Remove unused cash confirmation fields
            $table->dropColumn([
                'cash_confirmed_at',
                'cash_confirmed_by'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Restore the columns if needed
            $table->timestamp('cash_confirmed_at')->nullable();
            $table->unsignedBigInteger('cash_confirmed_by')->nullable();
        });
    }
};
