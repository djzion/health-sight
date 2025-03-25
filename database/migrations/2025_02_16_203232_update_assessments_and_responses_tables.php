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
            // Only add columns that don't already exist
            if (!Schema::hasColumn('assessments', 'response_type')) {
                $table->enum('response_type', [
                    'yes_no',
                    'good_bad',
                    'text',
                    'conditional_text',
                    'multi_select',
                    'single_select'
                ])->default('yes_no')->after('question');
            }

            if (!Schema::hasColumn('assessments', 'options')) {
                $table->text('options')->nullable()->after('response_type');
            }

            if (!Schema::hasColumn('assessments', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('options');
                $table->foreign('parent_id')->references('id')->on('assessments')->onDelete('cascade');
            }

            if (!Schema::hasColumn('assessments', 'conditional_logic')) {
                $table->text('conditional_logic')->nullable()->after('parent_id');
            }

            if (!Schema::hasColumn('assessments', 'order')) {
                $table->integer('order')->default(0)->after('conditional_logic');
            }
        });

        // Modify assessment_responses table
        Schema::table('assessment_responses', function (Blueprint $table) {
            // Add additional_response column if it doesn't exist
            if (!Schema::hasColumn('assessment_responses', 'additional_response')) {
                $table->text('additional_response')->nullable()->after('response');
            }
        });
    }

    public function down()
    {
        Schema::table('assessments', function (Blueprint $table) {
            // Remove added columns
            if (Schema::hasColumn('assessments', 'response_type')) {
                $table->dropColumn('response_type');
            }
            if (Schema::hasColumn('assessments', 'options')) {
                $table->dropColumn('options');
            }
            if (Schema::hasColumn('assessments', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }
            if (Schema::hasColumn('assessments', 'conditional_logic')) {
                $table->dropColumn('conditional_logic');
            }
            if (Schema::hasColumn('assessments', 'order')) {
                $table->dropColumn('order');
            }
        });

        Schema::table('assessment_responses', function (Blueprint $table) {
            if (Schema::hasColumn('assessment_responses', 'additional_response')) {
                $table->dropColumn('additional_response');
            }
        });
    }};
