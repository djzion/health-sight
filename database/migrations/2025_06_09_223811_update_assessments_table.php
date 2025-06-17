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
       Schema::table('assessments', function (Blueprint $table) {
            // Remove the old next_available_date approach
            $table->dropColumn('next_available_date');

            // Add assessment type if not exists
            if (!Schema::hasColumn('assessments', 'assessment_type')) {
                $table->enum('assessment_type', ['general', 'safecare'])->default('general')->after('response_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->datetime('next_available_date')->nullable();
            $table->dropColumn('assessment_type');
        });
    }
};
