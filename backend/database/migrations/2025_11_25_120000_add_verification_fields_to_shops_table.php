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
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending')->after('photo_path');
            $table->string('business_permit_path')->nullable()->after('verification_status');
            $table->string('owner_id_path')->nullable()->after('business_permit_path');
            $table->string('proof_of_address_path')->nullable()->after('owner_id_path');
            $table->text('rejection_reason')->nullable()->after('proof_of_address_path');
            $table->timestamp('verified_at')->nullable()->after('rejection_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn([
                'verification_status',
                'business_permit_path',
                'owner_id_path',
                'proof_of_address_path',
                'rejection_reason',
                'verified_at'
            ]);
        });
    }
};
