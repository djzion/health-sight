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
            // Add columns for district, LGA, and PHC relationships
            $table->foreignId('district_id')->nullable()->after('user_id')
                  ->constrained('districts')->onDelete('set null');

            $table->foreignId('lga_id')->nullable()->after('district_id')
                  ->constrained('lgas')->onDelete('set null');

            $table->foreignId('phc_id')->nullable()->after('lga_id')
                  ->constrained('phcs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_responses', function (Blueprint $table) {
            $table->dropForeign(['district_id']);
            $table->dropForeign(['lga_id']);
            $table->dropForeign(['phc_id']);

            $table->dropColumn(['district_id', 'lga_id', 'phc_id']);
        });
    }
};
