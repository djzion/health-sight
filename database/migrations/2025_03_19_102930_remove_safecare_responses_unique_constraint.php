<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RemoveSafecareResponsesUniqueConstraint extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First, identify and drop the foreign key constraint
        $foreignKeys = $this->getForeignKeysUsingIndex();

        foreach ($foreignKeys as $foreignKey) {
            Schema::table('safecare_responses', function (Blueprint $table) use ($foreignKey) {
                $table->dropForeign($foreignKey);
            });
        }

        // Now drop the unique constraint
        Schema::table('safecare_responses', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'phc_id', 'safecare_id']);
        });

        // Recreate the foreign keys if needed
        foreach ($foreignKeys as $foreignKey) {
            // You'll need to customize this part based on your actual foreign key definitions
            // This is just a placeholder
            // Schema::table('safecare_responses', function (Blueprint $table) {
            //     $table->foreign(['user_id', 'phc_id', 'safecare_id'])->references(...);
            // });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('safecare_responses', function (Blueprint $table) {
            $table->unique(['user_id', 'phc_id', 'safecare_id']);
        });
    }

    /**
     * Get foreign keys that use this index
     *
     * @return array
     */
    private function getForeignKeysUsingIndex()
    {
        // This is a MySQL-specific query to find foreign keys referencing this index
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE CONSTRAINT_TYPE = 'FOREIGN KEY'
            AND TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'safecare_responses'
        ");

        return array_map(function($fk) {
            return $fk->CONSTRAINT_NAME;
        }, $foreignKeys);
    }
}
