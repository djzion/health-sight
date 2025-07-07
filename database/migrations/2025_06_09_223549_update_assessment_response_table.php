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
       Schema::table('assessment_responses', function (Blueprint $table) {
            // Add period tracking
            $table->unsignedBigInteger('assessment_period_id')->nullable()->after('assessment_id');
            $table->enum('quarter', ['Q1', 'Q2', 'Q3', 'Q4'])->nullable()->after('assessment_period_id');
            $table->year('year')->nullable()->after('quarter');

            // Add submission tracking
            $table->timestamp('submitted_at')->nullable()->after('updated_at');
            $table->boolean('is_final_submission')->default(false)->after('submitted_at');

            // Add foreign key constraint
            $table->foreign('assessment_period_id')->references('id')->on('assessment_periods')->onDelete('set null');

            // Add index for better performance
            $table->index(['quarter', 'year', 'phc_id']);
            $table->index(['assessment_period_id', 'phc_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_responses', function (Blueprint $table) {
            $table->dropForeign(['assessment_period_id']);
            $table->dropIndex(['quarter', 'year', 'phc_id']);
            $table->dropIndex(['assessment_period_id', 'phc_id']);
            $table->dropColumn([
                'assessment_period_id',
                'quarter',
                'year',
                'submitted_at',
                'is_final_submission'
            ]);
        });
    }
};
