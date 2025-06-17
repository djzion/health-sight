<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // public function up(): void
    // {

    //     DB::table('assessments')
    //         ->where('id', 6)
    //         ->where('question', 'Year of commencement of Primary healthcare centre')
    //         ->update([
    //             'response_type' => 'year',
    //             'validation_rules' => json_encode([
    //                 'required' => true,
    //                 'min' => 1960,
    //                 'max' => date('Y')
    //             ])
    //         ]);

    //     DB::table('assessments')
    //         ->where('id', 7)
    //         ->where('question', 'Category of primary healthcare centre')
    //         ->update([
    //             'response_type' => 'select-category',
    //             'options' => json_encode(['CHC/Flagship', 'PHC', 'PHCc', 'HP']),
    //             'validation_rules' => json_encode([
    //                 'required' => true,
    //                 'in' => ['CHC/Flagship', 'PHC', 'PHCc', 'HP']
    //             ])
    //         ]);

    //     DB::table('assessments')
    //         ->where('id', 22)
    //         ->where('question', 'Days of operation per week')
    //         ->update([
    //             'response_type' => 'select-days-operation',
    //             'options' => json_encode(['5 days 8 hours', '7 days 24 hours']),
    //             'validation_rules' => json_encode([
    //                 'required' => true,
    //                 'in' => ['5 days 8 hours', '7 days 24 hours']
    //             ])
    //         ]);

    //     // Update Water sources question (ID 150)
    //     DB::table('assessments')
    //         ->where('id', 150)
    //         ->where('question', 'What are the sources of water available?')
    //         ->update([
    //             'response_type' => 'select-water-source',
    //             'options' => json_encode(['Borehole', 'Well', 'None']),
    //             'validation_rules' => json_encode([
    //                 'required' => true,
    //                 'in' => ['Borehole', 'Well', 'None']
    //             ])
    //         ]);

    //     // Update Borehole treatment frequency question (ID 151)
    //     DB::table('assessments')
    //         ->where('id', 151)
    //         ->where('question', 'If borehole, how often is it treated?')
    //         ->update([
    //             'response_type' => 'select-frequency',
    //             'options' => json_encode(['Weekly', 'Bi-Weekly', 'Monthly', 'Bi-Monthly', 'Quarterly', 'Annually', 'Not Treated']),
    //             'validation_rules' => json_encode([
    //                 'required' => true,
    //                 'in' => ['Weekly', 'Bi-Weekly', 'Monthly', 'Bi-Monthly', 'Quarterly', 'Annually', 'Not Treated']
    //             ])
    //         ]);

    //     // Update Waste disposal frequency question (ID 175)
    //     DB::table('assessments')
    //         ->where('id', 175)
    //         ->where('question', 'How often do they come to dispose of the waste?')
    //         ->update([
    //             'response_type' => 'select-frequency',
    //             'options' => json_encode(['Weekly', 'Bi-Weekly', 'Monthly', 'Bi-Monthly', 'Quarterly', 'Annually', 'None']),
    //             'validation_rules' => json_encode([
    //                 'required' => true,
    //                 'in' => ['Weekly', 'Bi-Weekly', 'Monthly', 'Bi-Monthly', 'Quarterly', 'Annually', 'None']
    //             ])
    //         ]);

    //     // Add required validation to text fields that should not be empty
    //     DB::table('assessments')
    //         ->whereIn('response_type', ['text', 'int'])
    //         ->whereNull('validation_rules')
    //         ->update([
    //             'validation_rules' => json_encode(['required' => true])
    //         ]);

    //     $staffQuestionIds = [184, 185, 186, 187, 188, 189, 190, 191, 192, 193, 194, 195, 234];

    //     DB::table('assessments')
    //         ->whereIn('id', $staffQuestionIds)
    //         ->where('response_type', 'form')
    //         ->update([
    //             'validation_rules' => json_encode([
    //                 'required' => false,
    //                 'structure' => [
    //                     'full_time' => 'integer|min:0',
    //                     'contract' => 'integer|min:0',
    //                     'nysc_intern' => 'integer|min:0'
    //                 ]
    //             ])
    //         ]);
    // }

    // /**
    //  * Reverse the migrations.
    //  */
    // public function down(): void
    // {

    //     DB::table('assessments')
    //         ->where('id', 7)
    //         ->update([
    //             'response_type' => 'text',
    //             'validation_rules' => null
    //         ]);

    //     DB::table('assessments')
    //         ->where('id', 8)
    //         ->update([
    //             'response_type' => 'text',
    //             'options' => null,
    //             'validation_rules' => null
    //         ]);

    //     DB::table('assessments')
    //         ->where('id', 22)
    //         ->update([
    //             'response_type' => 'text',
    //             'options' => null,
    //             'validation_rules' => null
    //         ]);

    //     DB::table('assessments')
    //         ->where('id', 150)
    //         ->update([
    //             'response_type' => 'text',
    //             'options' => null,
    //             'validation_rules' => null
    //         ]);

    //     DB::table('assessments')
    //         ->where('id', 151)
    //         ->update([
    //             'response_type' => 'text',
    //             'options' => null,
    //             'validation_rules' => null
    //         ]);

    //     DB::table('assessments')
    //         ->where('id', 175)
    //         ->update([
    //             'response_type' => 'text',
    //             'options' => null,
    //             'validation_rules' => null
    //         ]);

    //     DB::table('assessments')
    //         ->whereIn('response_type', ['text', 'int', 'form'])
    //         ->update([
    //             'validation_rules' => null
    //         ]);
    // }
};
