<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Record which package (if any) a request was ordered from.
 *
 * Until now a package was purely a client-side cart shortcut: its scopes were
 * copied into `candidate_scope_type` at submission and the package identity was
 * discarded, so "which package did this come from?" was unanswerable and the
 * portal showed fabricated placeholder names.
 *
 * Nullable by design — ad-hoc requests pick scopes individually, and requests
 * predating this column have no attribution. `nullOnDelete` rather than
 * cascade (which would delete the request along with the package) or restrict
 * (which would permanently block deleting a package once used).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('screening_requests', function (Blueprint $table) {
            $table->foreignId('package_id')->nullable()
                ->after('customer_user_id')
                ->constrained()
                ->nullOnDelete();
            $table->index('package_id');
        });
    }

    public function down(): void
    {
        Schema::table('screening_requests', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropIndex(['package_id']);
            $table->dropColumn('package_id');
        });
    }
};
