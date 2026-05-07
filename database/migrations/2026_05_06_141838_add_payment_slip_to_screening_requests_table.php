<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('screening_requests', function (Blueprint $table) {
            $table->string('payment_slip_path')->nullable()->after('invoice_id');
            $table->timestamp('payment_slip_uploaded_at')->nullable()->after('payment_slip_path');
        });
    }

    public function down(): void
    {
        Schema::table('screening_requests', function (Blueprint $table) {
            $table->dropColumn(['payment_slip_path', 'payment_slip_uploaded_at']);
        });
    }
};
