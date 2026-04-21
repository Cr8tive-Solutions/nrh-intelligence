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
        Schema::table('scope_types', function (Blueprint $table) {
            $table->string('category')->nullable()->after('country_id');
            $table->boolean('price_on_request')->default(false)->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scope_types', function (Blueprint $table) {
            $table->dropColumn(['category', 'price_on_request']);
        });
    }
};
