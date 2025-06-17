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
        Schema::create('assessment_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Q1 2024 General Assessment"
            $table->enum('quarter', ['Q1', 'Q2', 'Q3', 'Q4']);
            $table->year('year');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('assessment_type', ['general', 'safecare'])->default('general');
            $table->timestamps();

            // Unique constraint to prevent duplicate periods
            $table->unique(['quarter', 'year', 'assessment_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_periods');
    }
};
