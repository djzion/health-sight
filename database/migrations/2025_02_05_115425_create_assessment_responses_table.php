<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssessmentResponsesTable extends Migration
{
    public function up()
    {
        Schema::create('assessment_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('assessment_id');
            $table->text('response');
            $table->text('comments')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('assessment_id')
                  ->references('id')
                  ->on('assessments')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('assessment_responses');
    }
}
