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
       Schema::create('safecare_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Q1 2024 SafeCare Assessment"
            $table->text('description')->nullable();
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->enum('quarter', ['Q1', 'Q2', 'Q3', 'Q4']);
            $table->integer('year');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Indexes for performance
            $table->index(['start_date', 'end_date']);
            $table->index(['quarter', 'year']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('safecare_periods');
    }
};
