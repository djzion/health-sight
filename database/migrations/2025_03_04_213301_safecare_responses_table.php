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
        Schema::create('safecare_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('district_id')->constrained()->onDelete('cascade');
            $table->foreignId('lga_id')->constrained()->onDelete('cascade');
            $table->foreignId('phc_id')->constrained()->onDelete('cascade');

            // Simply create an integer column with no foreign key constraint
            $table->integer('safecare_id')->unsigned();
            $table->index('safecare_id');

            $table->enum('response', ['NC', 'PC', 'FC', 'NA']);
            $table->text('comment')->nullable();
            $table->timestamps();

            // Add a unique constraint
            $table->unique(['user_id', 'phc_id', 'safecare_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('safecare_responses');
    }
};
