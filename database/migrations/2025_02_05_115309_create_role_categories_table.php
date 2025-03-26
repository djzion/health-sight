<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('role_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('assessment_section_id');
            $table->timestamps();

            $table->foreign('role_id')
                  ->references('id')
                  ->on('roles')
                  ->onDelete('cascade');

            $table->foreign('assessment_section_id')
                  ->references('id')
                  ->on('assessment_sections')
                  ->onDelete('cascade');

            // Ensure unique role-section combinations
            $table->unique(['role_id', 'assessment_section_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('role_categories');
    }
}
