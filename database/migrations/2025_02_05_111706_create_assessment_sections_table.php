<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssessmentSectionsTable extends Migration
{
    public function up()
    {
        Schema::create('assessment_sections', function (Blueprint $table) {

            $table->id();
            $table->string('section_name');
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('assessment_sections');
    }
}
