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
        Schema::table('screening_requests', function (Blueprint $table) {
            $table->timestamp('payment_verified_at')->nullable()->after('payment_slip_uploaded_at');
            // No FK constraint: admin staff live in the sibling nrh-admin app's users table
            // (shared schema). The admin portal can add the constraint if it owns that table.
            $table->unsignedBigInteger('payment_verified_by')->nullable()->after('payment_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('screening_requests', function (Blueprint $table) {
            $table->dropColumn(['payment_verified_at', 'payment_verified_by']);
        });
    }
};
