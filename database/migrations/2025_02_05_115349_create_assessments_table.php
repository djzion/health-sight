<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssessmentsTable extends Migration
{
    public function up()
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assessment_section_id');
            $table->text('question');
            $table->enum('response_type', ['yes_no', 'text'])->default('yes_no');
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->foreign('assessment_section_id')
                  ->references('id')
                  ->on('assessment_sections')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('assessments');
    }
}
