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
            $table->text('additional_response')->nullable()->after('response');
        });
    }

    public function down()
    {
        Schema::table('assessment_responses', function (Blueprint $table) {
            $table->dropColumn('additional_response');
        });
    }

};
