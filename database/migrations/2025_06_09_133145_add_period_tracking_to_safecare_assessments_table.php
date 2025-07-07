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
        Schema::table('safecare_assessments', function (Blueprint $table) {
            // Add period tracking columns
            $table->foreignId('safecare_period_id')->nullable()->constrained('safecare_periods')->onDelete('set null');
            $table->enum('quarter', ['Q1', 'Q2', 'Q3', 'Q4'])->nullable();
            $table->integer('year')->nullable();

            // Add edit tracking columns if they don't exist
            if (!Schema::hasColumn('safecare_assessments', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            }

            if (!Schema::hasColumn('safecare_assessments', 'last_updated_date')) {
                $table->datetime('last_updated_date')->nullable();
            }

            // Add indexes for performance
            $table->index(['quarter', 'year']);
            $table->index('safecare_period_id');
            $table->index(['user_id', 'phc_id', 'created_at']); // For checking existing assessments
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('safecare_assessments', function (Blueprint $table) {
            $table->dropForeign(['safecare_period_id']);
            $table->dropColumn(['safecare_period_id', 'quarter', 'year']);

            if (Schema::hasColumn('safecare_assessments', 'updated_by')) {
                $table->dropForeign(['updated_by']);
                $table->dropColumn('updated_by');
            }

            if (Schema::hasColumn('safecare_assessments', 'last_updated_date')) {
                $table->dropColumn('last_updated_date');
            }
        });
    }
};
