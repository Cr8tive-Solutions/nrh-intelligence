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
        Schema::create('request_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('screening_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('identity_type_id')->constrained();
            $table->string('name');
            $table->string('identity_number');
            $table->string('mobile')->nullable();
            $table->string('remarks')->nullable();
            $table->enum('status', ['new', 'in_progress', 'complete', 'flagged'])->default('new')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_candidates');
    }
};
