<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_users', function (Blueprint $table) {
            // Encrypted TOTP secret for the authenticator-app factor. Null until
            // the user begins enrollment. `confirmed_at` is set only once the
            // user proves the app is working by entering a valid code, so login
            // treats the app factor as active only when it is non-null.
            $table->text('two_factor_secret')->nullable()->after('password');
            $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_secret');
        });
    }

    public function down(): void
    {
        Schema::table('customer_users', function (Blueprint $table) {
            $table->dropColumn(['two_factor_secret', 'two_factor_confirmed_at']);
        });
    }
};
