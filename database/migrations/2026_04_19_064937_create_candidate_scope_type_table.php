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
        Schema::create('candidate_scope_type', function (Blueprint $table) {
            $table->foreignId('request_candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('scope_type_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['new', 'in_progress', 'complete', 'flagged'])->default('new');
            $table->primary(['request_candidate_id', 'scope_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_scope_type');
    }
};
