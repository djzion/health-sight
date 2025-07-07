<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\SafecareResponses;
use App\Models\Safecare;

class MigrateSafecareData extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'safecare:migrate-data {--dry-run : Show what would be migrated without actually doing it}';

    /**
     * The console command description.
     */
    protected $description = 'Migrate SafeCare data from vertical (safecare_responses) to horizontal (safecare_assessments) format';

    /**
     * Question number to column name mapping
     */
    protected $questionMapping = [];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting SafeCare data migration...');

        // Build question mapping
        $this->buildQuestionMapping();

        if ($this->option('dry-run')) {
            $this->info('DRY RUN MODE - No data will be changed');
        }

        // Get all unique assessment sessions
        $assessmentSessions = $this->getAssessmentSessions();

        $this->info("Found {$assessmentSessions->count()} assessment sessions to migrate");

        $migratedCount = 0;
        $errorCount = 0;

        foreach ($assessmentSessions as $session) {
            try {
                $this->migrateAssessmentSession($session);
                $migratedCount++;

                if ($migratedCount % 10 == 0) {
                    $this->info("Migrated {$migratedCount} assessments...");
                }
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("Error migrating session for PHC {$session->phc_id} on {$session->assessment_date}: " . $e->getMessage());
            }
        }

        $this->info("Migration completed!");
        $this->info("Successfully migrated: {$migratedCount} assessments");
        if ($errorCount > 0) {
            $this->warn("Errors encountered: {$errorCount} assessments");
        }
    }

    /**
     * Build mapping between question numbers and column names
     */
    protected function buildQuestionMapping()
    {
        $questions = Safecare::all();

        foreach ($questions as $question) {
            // Convert question_no like "1.1.1.1" to column name like "q_1_1_1_1"
            $columnName = 'q_' . str_replace('.', '_', $question->question_no);
            $this->questionMapping[$question->id] = $columnName;
        }

        $this->info("Built mapping for " . count($this->questionMapping) . " questions");
    }

    /**
     * Get all unique assessment sessions
     */
    protected function getAssessmentSessions()
    {
        return DB::table('safecare_responses')
            ->select([
                'user_id',
                'district_id',
                'lga_id',
                'phc_id',
                DB::raw('DATE(created_at) as assessment_date'),
                DB::raw('MAX(created_at) as latest_time')
            ])
            ->groupBy(['user_id', 'district_id', 'lga_id', 'phc_id', DB::raw('DATE(created_at)')])
            ->orderBy('latest_time')
            ->get();
    }

    /**
     * Migrate a single assessment session
     */
    protected function migrateAssessmentSession($session)
    {
        // Get all responses for this assessment session
        $responses = SafecareResponses::where('phc_id', $session->phc_id)
            ->where('user_id', $session->user_id)
            ->where('district_id', $session->district_id)
            ->where('lga_id', $session->lga_id)
            ->whereDate('created_at', $session->assessment_date)
            ->get();

        if ($responses->isEmpty()) {
            $this->warn("No responses found for PHC {$session->phc_id} on {$session->assessment_date}");
            return;
        }

        // Build the assessment record
        $assessmentData = [
            'user_id' => $session->user_id,
            'district_id' => $session->district_id,
            'lga_id' => $session->lga_id,
            'phc_id' => $session->phc_id,
            'assessment_date' => $session->latest_time,
            'total_questions' => $responses->count(),
            'fully_compliant_count' => 0,
            'partially_compliant_count' => 0,
            'not_compliant_count' => 0,
            'not_applicable_count' => 0,
            'compliance_percentage' => 0.00,
            'created_at' => $session->latest_time,
            'updated_at' => $session->latest_time
        ];

        // Process each response
        foreach ($responses as $response) {
            if (isset($this->questionMapping[$response->safecare_id])) {
                $columnName = $this->questionMapping[$response->safecare_id];
                $assessmentData[$columnName] = $response->response;

                // Count compliance levels
                switch ($response->response) {
                    case 'FC':
                        $assessmentData['fully_compliant_count']++;
                        break;
                    case 'PC':
                        $assessmentData['partially_compliant_count']++;
                        break;
                    case 'NC':
                        $assessmentData['not_compliant_count']++;
                        break;
                    case 'NA':
                        $assessmentData['not_applicable_count']++;
                        break;
                }

                // Add comment if exists (optional)
                if (!empty($response->comment)) {
                    $commentColumn = 'comment_' . str_replace('q_', '', $columnName);
                    if ($this->columnExists('safecare_assessments', $commentColumn)) {
                        $assessmentData[$commentColumn] = $response->comment;
                    }
                }
            }
        }

        // Calculate compliance percentage
        $scoringQuestions = $assessmentData['fully_compliant_count'] +
                           $assessmentData['partially_compliant_count'] +
                           $assessmentData['not_compliant_count'];

        if ($scoringQuestions > 0) {
            $assessmentData['compliance_percentage'] = round(
                (($assessmentData['fully_compliant_count'] * 100) +
                 ($assessmentData['partially_compliant_count'] * 50)) /
                ($scoringQuestions * 100) * 100, 2
            );
        }

        // Insert the record
        if (!$this->option('dry-run')) {
            DB::table('safecare_assessments')->insert($assessmentData);
        } else {
            $this->line("Would insert assessment for PHC {$session->phc_id} on {$session->assessment_date} with {$responses->count()} responses");
        }
    }

    /**
     * Check if column exists in table
     */
    protected function columnExists($table, $column)
    {
        return DB::getSchemaBuilder()->hasColumn($table, $column);
    }
}
