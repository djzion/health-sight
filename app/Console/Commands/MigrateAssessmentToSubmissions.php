<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Exception;

class MigrateAssessmentToSubmissions extends Command
{
    protected $signature = 'assessment:migrate-submissions
                            {--dry-run : Preview migration without making changes}
                            {--force : Force migration even if data exists}
                            {--backup : Create backup before migration}';

    protected $description = 'Migrate assessment responses to wide submission format';

    private array $questionMap = [];
    private array $allowedColumns = [];

    public function handle(): int
    {
        $this->info('🚀 Assessment Submission Migration Tool');
        $this->info('Converting normalized responses to wide table format');
        $this->newLine();

        try {
            // Step 1: Validate environment
            $this->validateEnvironment();

            // Step 2: Analyze current data
            $stats = $this->analyzeData();
            $this->displayStats($stats);

            // Step 3: Confirm migration
            if (!$this->option('force') && !$this->confirm('Proceed with migration?')) {
                $this->warn('Migration cancelled.');
                return 1;
            }

            // Step 4: Create backup if requested
            if ($this->option('backup')) {
                $this->createBackup();
            }

            // Step 5: Run migration
            if ($this->option('dry-run')) {
                $this->info('🧪 DRY RUN MODE - No changes will be made');
                $this->previewMigration($stats);
            } else {
                $this->runMigration($stats);
            }

            $this->info('✅ Migration completed successfully!');
            return 0;

        } catch (Exception $e) {
            $this->error('❌ Migration failed: ' . $e->getMessage());
            return 1;
        }
    }

    private function validateEnvironment(): void
    {
        $this->info('🔍 Validating environment...');

        if (!Schema::hasTable('assessment_responses')) {
            throw new Exception('Source table assessment_responses not found');
        }

        if (!Schema::hasTable('assessment_submissions')) {
            throw new Exception('Target table assessment_submissions not found. Run the table creation script first.');
        }

        if (!$this->option('force')) {
            $existingCount = DB::table('assessment_submissions')->count();
            if ($existingCount > 0) {
                throw new Exception("Target table has {$existingCount} records. Use --force to override.");
            }
        }

        $this->info('✅ Environment validation passed');
    }

    private function analyzeData(): array
    {
        $this->info('📊 Analyzing source data...');

        $result = DB::selectOne("
            SELECT
                COUNT(*) as total_responses,
                COUNT(DISTINCT CONCAT(COALESCE(user_id,''), '-', COALESCE(phc_id,''), '-', COALESCE(quarter,''), '-', COALESCE(year,''))) as unique_submissions,
                COUNT(DISTINCT assessment_id) as unique_questions,
                COUNT(DISTINCT phc_id) as unique_phcs,
                COUNT(DISTINCT user_id) as unique_users,
                MIN(created_at) as earliest_date,
                MAX(created_at) as latest_date
            FROM assessment_responses
            WHERE user_id IS NOT NULL AND phc_id IS NOT NULL
        ");

        return (array) $result;
    }

    private function displayStats(array $stats): void
    {
        $this->table(['Metric', 'Value'], [
            ['Total Response Records', number_format($stats['total_responses'])],
            ['Unique Submissions', number_format($stats['unique_submissions'])],
            ['Unique Questions', $stats['unique_questions']],
            ['Unique PHCs', $stats['unique_phcs']],
            ['Unique Users', $stats['unique_users']],
            ['Date Range', $stats['earliest_date'] . ' to ' . $stats['latest_date']],
            ['Expected Output Records', number_format($stats['unique_submissions'])]
        ]);
    }

    private function createBackup(): void
    {
        $this->info('💾 Creating backup...');

        $backupTable = 'assessment_responses_backup_' . date('Y_m_d_H_i_s');

        DB::statement("CREATE TABLE {$backupTable} AS SELECT * FROM assessment_responses");

        $count = DB::table($backupTable)->count();
        $this->info("✅ Backup created: {$backupTable} ({$count} records)");
    }

    private function previewMigration(array $stats): void
    {
        $this->info('🧪 Preview Mode - Sample submissions that would be created:');

        $samples = DB::select("
            SELECT
                user_id, phc_id, quarter, year, assessment_period_id,
                COUNT(*) as response_count,
                GROUP_CONCAT(DISTINCT assessment_id ORDER BY assessment_id SEPARATOR ',') as sample_questions
            FROM assessment_responses
            WHERE user_id IS NOT NULL AND phc_id IS NOT NULL
            GROUP BY user_id, phc_id, quarter, year, assessment_period_id
            LIMIT 5
        ");

        foreach ($samples as $sample) {
            // Truncate the sample questions if too long
            $questions = $sample->sample_questions;
            if (strlen($questions) > 50) {
                $questionArray = explode(',', $questions);
                $questions = implode(',', array_slice($questionArray, 0, 10)) . '...';
            }

            $this->line(sprintf(
                'User %d, PHC %d, %s %d → %d responses (Q: %s)',
                $sample->user_id,
                $sample->phc_id,
                $sample->quarter ?? 'NULL',
                $sample->year ?? 0,
                $sample->response_count,
                $questions
            ));
        }

        $this->info("Would create {$stats['unique_submissions']} submission records from {$stats['total_responses']} response records");
    }

    private function runMigration(array $stats): void
    {
        $this->info('🔄 Starting migration...');

        // Use database transaction for data consistency
        DB::beginTransaction();

        try {
            $progressBar = $this->output->createProgressBar(3);

            // Step 1: Create base submissions
            $this->info('Step 1: Creating base submission records...');
            $this->createBaseSubmissions();
            $progressBar->advance();

            // Step 2: Create question mapping
            $this->info('Step 2: Setting up question mapping...');
            $this->createQuestionMapping();
            $progressBar->advance();

            // Step 3: Populate responses
            $this->info('Step 3: Populating question responses...');
            $this->populateResponses();
            $progressBar->advance();

            $progressBar->finish();
            $this->newLine();

            // Validate results
            $this->validateMigration($stats);

            DB::commit();
            $this->info('✅ Transaction committed successfully');

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Migration failed and rolled back: ' . $e->getMessage());
        }
    }

    private function createBaseSubmissions(): void
    {
        DB::statement("
            INSERT IGNORE INTO assessment_submissions (
                user_id, phc_id, district_id, lga_id, quarter, year, assessment_period_id,
                submission_date, last_updated_date, submitted_at, is_final_submission,
                total_questions, answered_questions, completion_percentage,
                created_at, updated_at
            )
            SELECT
                user_id, phc_id, district_id, lga_id, quarter, year, assessment_period_id,
                MIN(created_at) as submission_date,
                MAX(updated_at) as last_updated_date,
                MAX(submitted_at) as submitted_at,
                MAX(is_final_submission) as is_final_submission,
                241 as total_questions,
                0 as answered_questions,
                0.00 as completion_percentage,
                MIN(created_at) as created_at,
                MAX(updated_at) as updated_at
            FROM assessment_responses
            WHERE user_id IS NOT NULL AND phc_id IS NOT NULL
            GROUP BY user_id, phc_id, district_id, lga_id, quarter, year, assessment_period_id
        ");
    }

    private function createQuestionMapping(): void
    {
        // Complete question mapping based on your table structure
        $this->questionMap = [
            // Section 1: Accessibility (1-5)
            1 => 'motorable_road', 2 => 'road_network', 3 => 'signpost_present',
            4 => 'center_fenced', 5 => 'security_gate',

            // Section 2: Operations (6-21)
            6 => 'year_commenced', 7 => 'phc_category', 8 => 'organogram_displayed',
            9 => 'complaint_box', 10 => 'dedicated_telephone', 11 => 'staff_files',
            12 => 'quality_team', 13 => 'outpatients_total', 14 => 'inpatients_total',
            15 => 'deliveries_total', 16 => 'insurance_enrolled', 17 => 'staff_accommodation',
            18 => 'sop_available', 19 => 'services_rendered', 20 => 'duty_rosters',
            21 => 'training_schedule',

            // Section 3: Operating Schedule (22-25)
            22 => 'days_operation', 23 => 'hours_daily', 24 => 'rooms_total',
            25 => 'appointment_system',

            // Section 4: Consulting Room (26-30)
            26 => 'consulting_rooms', 27 => 'consulting_equipped', 28 => 'consulting_painting',
            29 => 'consulting_lighting', 30 => 'consulting_sop',

            // Section 5: Treatment Room (31-35)
            31 => 'treatment_equipped', 32 => 'treatment_lighting', 33 => 'treatment_painting',
            34 => 'treatment_ventilated', 35 => 'treatment_sop',

            // Section 6: Labour Ward (36-51)
            36 => 'delivery_bed', 37 => 'angle_lamp', 38 => 'phototherapy',
            39 => 'delivery_ventilated', 40 => 'baby_cot', 41 => 'newborn_tag',
            42 => 'baby_scale', 43 => 'resuscitaire', 44 => 'fetal_monitor',
            45 => 'suturing_materials', 46 => 'delivery_packs', 47 => 'medical_oxygen',
            49 => 'delivery_lighting', 50 => 'delivery_sop', 51 => 'oxygen_protocol',

            // Section 7: Observation Room (52-54)
            52 => 'observation_beds', 53 => 'mattress_sealed', 54 => 'observation_sop',

            // Section 8: Inpatient Wards (55-59)
            55 => 'wards_total', 56 => 'beds_per_ward', 57 => 'wards_adequate',
            58 => 'ward_equipment', 59 => 'ward_sop',

            // Section 9: Emergency Services (60-68)
            60 => 'emergency_tray', 61 => 'emergency_adequate', 62 => 'suturing_emergency',
            63 => 'oxygen_cylinder', 64 => 'ambu_pediatric', 65 => 'ambu_adult',
            66 => 'suction_machine', 67 => 'nebulizer', 68 => 'emergency_sop',

            // Section 10: Laboratory (69-86, 226, 230-233)
            69 => 'lab_illumination', 70 => 'lab_equipment', 71 => 'reagent_storage',
            72 => 'autoclave', 73 => 'slides_rack', 74 => 'specimen_bottles',
            75 => 'test_tube_racks', 76 => 'glucometer', 77 => 'malaria_kit',
            78 => 'urine_dipstick', 79 => 'microscope', 80 => 'pep_available',
            81 => 'centrifuge', 82 => 'lab_water', 83 => 'records_method',
            84 => 'lab_refrigerator', 85 => 'specimen_identifiers', 86 => 'lab_sop',
            226 => 'glucometer_strips', 230 => 'lab_ppes', 231 => 'outpatient_ppes',
            232 => 'inpatient_ppes', 233 => 'waste_ppes',

            // Section 11: Immunization (87-92)
            87 => 'immunization_trained', 88 => 'vaccine_stock', 89 => 'vaccine_storage',
            90 => 'epi_fridge', 91 => 'immunization_records', 92 => 'immunization_sop',

            // Section 12: Medical Records (93-99)
            93 => 'records_shelf', 94 => 'records_secure', 95 => 'nhmis_available',
            96 => 'nhmis_filled', 97 => 'unique_identifier', 98 => 'demographic_info',
            99 => 'records_sop',

            // Section 13: Pharmacy (100-108, 141)
            100 => 'pharmacy_ventilation', 101 => 'thermometer', 102 => 'drugs_available',
            103 => 'pharmacy_fridge', 104 => 'temperature_charts', 105 => 'dda_cupboard',
            106 => 'revolving_fund', 107 => 'drug_disposal', 108 => 'pharmacy_sop',
            141 => 'drug_stock',

            // Section 14: HIV Services (109-114, 235-236)
            109 => 'hiv_services', 110 => 'hiv_trained', 111 => 'hiv_registers',
            112 => 'arv_drugs', 113 => 'hiv_test_kits', 114 => 'hiv_sop',
            235 => 'hiv_counseling', 236 => 'hiv_screening_registers',

            // Section 15: TB Services (115-119, 237-238)
            115 => 'tb_services', 116 => 'tb_trained', 117 => 'tb_registers',
            118 => 'tb_drugs', 119 => 'tb_sop', 237 => 'tb_identification',
            238 => 'tb_screening_registers',

            // Section 16: Family Planning (120-124)
            120 => 'fp_trained', 121 => 'fp_methods', 122 => 'stock_management',
            123 => 'fp_documentation', 124 => 'fp_sop',

            // Section 17: Oral Health (125-131)
            125 => 'dentist_available', 126 => 'dental_room', 127 => 'dental_chair',
            128 => 'oral_instruments', 129 => 'sterilization_instruments',
            130 => 'scaling_instruments', 131 => 'dental_sop',

            // Section 18: Eye Care (132-135, 228)
            132 => 'optometrist', 133 => 'eye_room', 134 => 'eye_instruments',
            135 => 'eye_sop', 228 => 'reading_glasses',

            // Section 19: Health Education (136-144)
            136 => 'health_talk_frequency', 137 => 'health_talk_schedule',
            138 => 'common_topics', 139 => 'talk_attendance', 140 => 'talk_feedback',
            142 => 'community_programs', 144 => 'health_education_sop',

            // Section 20: Infrastructure (145-165, 227, 229)
            145 => 'power_primary', 146 => 'power_alternate_available',
            147 => 'power_alternate', 148 => 'power_complements', 149 => 'water_potable',
            150 => 'water_sources', 151 => 'water_treatment', 152 => 'toilets_total',
            153 => 'toilets_segregated', 154 => 'toilets_functional',
            155 => 'patient_toilets', 156 => 'staff_toilets', 157 => 'handwashing_essentials',
            158 => 'cleaning_agents', 159 => 'running_water', 160 => 'functional_furniture',
            161 => 'handwashing_areas', 162 => 'drainage', 163 => 'mosquito_nets',
            164 => 'painting_adequate', 165 => 'environment_clean',
            227 => 'cracked_walls', 229 => 'leaking_roof',

            // Section 21: Infection Control (166-176, 225, 239-240)
            166 => 'sterilization_area', 167 => 'washing_area', 168 => 'autoclave_available',
            169 => 'consultation_ppes', 170 => 'laboratory_ppes', 171 => 'sharp_boxes',
            172 => 'color_bins', 173 => 'waste_storage', 174 => 'waste_registered',
            175 => 'waste_frequency', 176 => 'waste_awareness', 225 => 'waste_knowledge',
            239 => 'outpatient_ppes_alt', 240 => 'inpatient_ppes_alt',

            // Section 22: Fire Safety (178-183)
            178 => 'fire_equipment', 179 => 'fire_current', 180 => 'fire_certificate',
            181 => 'fire_exits', 182 => 'fire_training', 183 => 'fire_sop',

            // Section 23: Staffing (184-198, 234)
            184 => 'medical_officers', 185 => 'eho_officers', 186 => 'midwives',
            187 => 'chews', 188 => 'pharmacists', 189 => 'pharmacy_techs',
            190 => 'lab_scientists', 191 => 'lab_techs', 192 => 'heo_officers',
            193 => 'him_officers', 194 => 'him_techs', 195 => 'admin_officers',
            196 => 'security_personnel', 197 => 'health_attendants',
            198 => 'birth_registrars', 234 => 'cho_staff',

            // Conditional Response Fields (199-225)
            199 => 'training_details', 201 => 'services_details',
            202 => 'treatment_equipment_missing', 204 => 'enrollment_details',
            205 => 'consulting_equipment_missing', 206 => 'delivery_bed_count',
            207 => 'baby_cot_count', 208 => 'ward_equipment_needed',
            209 => 'feedback_method', 210 => 'borehole_treatment',
            211 => 'inpatient_details', 212 => 'maternity_details',
            213 => 'fp_details', 214 => 'immunization_details',
            215 => 'lab_details', 216 => 'hiv_details', 217 => 'tb_details',
            218 => 'oral_details', 219 => 'mental_health_details',
            220 => 'eye_care_details', 221 => 'mo_fulltime', 222 => 'mo_contract',
            223 => 'mo_nysc', 224 => 'community_evidence',

            // Final comment
            241 => 'general_comments'
        ];

        // Create whitelist of allowed columns for security
        $this->allowedColumns = array_unique(array_values($this->questionMap));
    }

    private function populateResponses(): void
    {
        // Special handling for problematic columns
        $specialColumns = [
            6 => 'year_commenced', // YEAR type - needs validation
            13 => 'outpatients_total', // INT type
            14 => 'inpatients_total', // INT type
            15 => 'deliveries_total', // INT type
            23 => 'hours_daily', // INT type
            24 => 'rooms_total', // INT type
            26 => 'consulting_rooms', // INT type
            52 => 'observation_beds', // INT type
            55 => 'wards_total', // INT type
            56 => 'beds_per_ward', // INT type
            136 => 'health_talk_frequency', // INT type
            139 => 'talk_attendance', // INT type
            152 => 'toilets_total', // INT type
            196 => 'security_personnel', // INT type
            197 => 'health_attendants', // INT type
            198 => 'birth_registrars', // INT type
            206 => 'delivery_bed_count', // INT type
            207 => 'baby_cot_count', // INT type
        ];

        // Use a larger batch approach for better performance
        $batchSize = 50; // Process 50 questions at a time
        $questionBatches = array_chunk($this->questionMap, $batchSize, true);
        $totalBatches = count($questionBatches);
        $currentBatch = 0;

        foreach ($questionBatches as $batch) {
            $currentBatch++;
            $this->info("Processing batch {$currentBatch}/{$totalBatches}...");

            foreach ($batch as $questionId => $columnName) {
                // Validate column name is in our whitelist
                if (!in_array($columnName, $this->allowedColumns)) {
                    $this->warn("Warning: Column {$columnName} not in whitelist, skipping");
                    continue;
                }

                try {
                    if (isset($specialColumns[$questionId])) {
                        // Handle special columns with data validation
                        $this->updateSpecialColumn($questionId, $columnName);
                    } else {
                        // Handle regular columns
                        $this->updateRegularColumn($questionId, $columnName);
                    }
                } catch (Exception $e) {
                    $this->warn("Warning: Failed to update question {$questionId} ({$columnName}): " . $e->getMessage());
                    // Continue with other columns rather than failing completely
                }
            }
        }

        // Update completion statistics
        $this->updateCompletionStatistics();
    }

    private function updateRegularColumn(int $questionId, string $columnName): void
    {
        $sql = "
            UPDATE assessment_submissions asub
            JOIN assessment_responses ar ON (
                ar.user_id = asub.user_id AND
                ar.phc_id = asub.phc_id AND
                COALESCE(ar.quarter, '') = COALESCE(asub.quarter, '') AND
                COALESCE(ar.year, 0) = COALESCE(asub.year, 0) AND
                COALESCE(ar.assessment_period_id, 0) = COALESCE(asub.assessment_period_id, 0) AND
                ar.assessment_id = ?
            )
            SET asub.`{$columnName}` = ar.response
        ";

        DB::statement($sql, [$questionId]);
    }

    private function updateSpecialColumn(int $questionId, string $columnName): void
    {
        if ($columnName === 'year_commenced') {
            // Handle YEAR type with validation (1901-2155)
            $sql = "
                UPDATE assessment_submissions asub
                JOIN assessment_responses ar ON (
                    ar.user_id = asub.user_id AND
                    ar.phc_id = asub.phc_id AND
                    COALESCE(ar.quarter, '') = COALESCE(asub.quarter, '') AND
                    COALESCE(ar.year, 0) = COALESCE(asub.year, 0) AND
                    COALESCE(ar.assessment_period_id, 0) = COALESCE(asub.assessment_period_id, 0) AND
                    ar.assessment_id = ?
                )
                SET asub.`{$columnName}` = CASE
                    WHEN ar.response REGEXP '^[0-9]+$'
                         AND CAST(ar.response AS UNSIGNED) BETWEEN 1901 AND 2155
                    THEN CAST(ar.response AS UNSIGNED)
                    ELSE NULL
                END
            ";
            DB::statement($sql, [$questionId]);
        } else {
            // Handle INT types with validation
            $sql = "
                UPDATE assessment_submissions asub
                JOIN assessment_responses ar ON (
                    ar.user_id = asub.user_id AND
                    ar.phc_id = asub.phc_id AND
                    COALESCE(ar.quarter, '') = COALESCE(asub.quarter, '') AND
                    COALESCE(ar.year, 0) = COALESCE(asub.year, 0) AND
                    COALESCE(ar.assessment_period_id, 0) = COALESCE(asub.assessment_period_id, 0) AND
                    ar.assessment_id = ?
                )
                SET asub.`{$columnName}` = CASE
                    WHEN ar.response REGEXP '^[0-9]+$'
                         AND CAST(ar.response AS UNSIGNED) <= 2147483647
                    THEN CAST(ar.response AS UNSIGNED)
                    ELSE NULL
                END
            ";
            DB::statement($sql, [$questionId]);
        }
    }

    private function updateCompletionStatistics(): void
    {
        $this->info('Updating completion statistics...');

        DB::statement("
            UPDATE assessment_submissions SET
                answered_questions = (
                    SELECT COUNT(*)
                    FROM assessment_responses ar
                    WHERE ar.user_id = assessment_submissions.user_id
                    AND ar.phc_id = assessment_submissions.phc_id
                    AND COALESCE(ar.quarter, '') = COALESCE(assessment_submissions.quarter, '')
                    AND COALESCE(ar.year, 0) = COALESCE(assessment_submissions.year, 0)
                    AND COALESCE(ar.assessment_period_id, 0) = COALESCE(assessment_submissions.assessment_period_id, 0)
                    AND ar.response IS NOT NULL
                    AND ar.response != ''
                ),
                completion_percentage = ROUND(
                    (SELECT COUNT(*)
                     FROM assessment_responses ar
                     WHERE ar.user_id = assessment_submissions.user_id
                     AND ar.phc_id = assessment_submissions.phc_id
                     AND COALESCE(ar.quarter, '') = COALESCE(assessment_submissions.quarter, '')
                     AND COALESCE(ar.year, 0) = COALESCE(assessment_submissions.year, 0)
                     AND COALESCE(ar.assessment_period_id, 0) = COALESCE(assessment_submissions.assessment_period_id, 0)
                     AND ar.response IS NOT NULL
                     AND ar.response != '') / 241.0 * 100, 2
                )
        ");
    }

    private function validateMigration(array $originalStats): void
    {
        $this->info('🔍 Validating migration results...');

        $newStats = DB::selectOne("
            SELECT
                COUNT(*) as total_submissions,
                AVG(completion_percentage) as avg_completion,
                MIN(completion_percentage) as min_completion,
                MAX(completion_percentage) as max_completion,
                SUM(answered_questions) as total_answers
            FROM assessment_submissions
        ");

        $this->table(['Validation Metric', 'Expected', 'Actual', 'Status'], [
            [
                'Total Submissions',
                number_format($originalStats['unique_submissions']),
                number_format($newStats->total_submissions),
                $originalStats['unique_submissions'] == $newStats->total_submissions ? '✅' : '❌'
            ],
            [
                'Avg Completion',
                'Variable',
                number_format($newStats->avg_completion ?? 0, 2) . '%',
                ($newStats->avg_completion ?? 0) > 0 ? '✅' : '❌'
            ],
            [
                'Total Answers',
                number_format($originalStats['total_responses']),
                number_format($newStats->total_answers ?? 0),
                '📊'
            ]
        ]);

        // Sample data check
        $sample = DB::selectOne("
            SELECT motorable_road, road_network, outpatients_total, completion_percentage
            FROM assessment_submissions
            WHERE motorable_road IS NOT NULL
            LIMIT 1
        ");

        if ($sample) {
            $this->info('✅ Sample data looks good:');
            $this->line("  - Motorable Road: {$sample->motorable_road}");
            $this->line("  - Road Network: {$sample->road_network}");
            $this->line("  - Outpatients: {$sample->outpatients_total}");
            $this->line("  - Completion: {$sample->completion_percentage}%");
        } else {
            $this->warn('⚠️  No sample data found with populated fields');
        }

        // Validate data integrity
        $integrityCheck = DB::selectOne("
            SELECT
                COUNT(*) as submissions_with_data,
                COUNT(DISTINCT user_id) as unique_users,
                COUNT(DISTINCT phc_id) as unique_phcs
            FROM assessment_submissions
            WHERE answered_questions > 0
        ");

        if ($integrityCheck->submissions_with_data > 0) {
            $this->info("✅ Data integrity check passed:");
            $this->line("  - Submissions with data: {$integrityCheck->submissions_with_data}");
            $this->line("  - Unique users: {$integrityCheck->unique_users}");
            $this->line("  - Unique PHCs: {$integrityCheck->unique_phcs}");
        } else {
            throw new Exception('Data integrity check failed: No submissions have populated data');
        }
    }
}
