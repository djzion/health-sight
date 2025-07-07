<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class SafecareAssessment extends Model
{
    protected $table = 'safecare_assessments';

    protected $fillable = [
        'user_id',
        'updated_by',
        'district_id',
        'lga_id',
        'phc_id',
        'assessment_date',
        'last_updated_date',
        'total_questions',
        'fully_compliant_count',
        'partially_compliant_count',
        'not_compliant_count',
        'not_applicable_count',
        'compliance_percentage',
        'safecare_period_id',
        'quarter',
        'year',

        // Governance and Management Section
        'Organogram',
        'Strategic_plan_mission_operational_plan',
        'Healthcare_organization_leader',
        'Care_and_Services',
        'Qualified_supply_chain_manager',
        'Organizational_Q_structure',
        'Books_of_accounts',
        'Cash_management',
        'Fixed_asset_register_FAR',
        'Business_and_management_processes',

        // Human Resources Management Section
        'Staffing_plan',
        'Performance_review',
        'Staff_job_descriptions',
        'Staff_personnel_files',
        'Credentialling',
        'Staff_orientation',
        'Staff_education_and_training',

        // Patient and Family Rights and Access to Care Section
        'Patient_rights',
        'Patients_privacy',
        'Patient_and_family_health_education',
        'Patient_information',
        'Informed_consent',
        'Complaints_process',
        'Opening_hours_display',
        'Signage',
        'Information_about_services_and_related_fees',

        // Management of Information Section
        'HMIS',
        'Internal_data_analysis_meetings',
        'Patient_record_unique_ID',
        'Patient_record',
        'Health_record_checks',
        'Health_record_storage',

        // Risk Management Section
        'Qualified_risk_manager',
        'Risk_management_plan',
        'Occupational_Health_Safety_OHS',
        'Security_system',
        'Fire_fighting_equipment',
        'IPC_policies',
        'Healthcare_waste_collection_assets',

        // Primary Healthcare (Outpatient) Services Section
        'Number_of_staff',
        'OPD_layout',
        'Waiting_area_ventilation_and_cleanliness',
        'Sufficient_consultation_rooms',
        'Handwashing_facilities',
        'PPE',
        'Sterilization_equipment',
        'Processing_sterile_packs',
        'Triage_process',
        'Qualified_staff_for_conducting_assessments',
        'Guideline_knowledge_Sexual_Transmitted_Infections_STI',
        'Guideline_knowledge_Rapid_Diagnostic_Tests_RDT',
        'Malaria_diagnostics',
        'Minor_surgery_equipment',
        'Vital_signs_equipment',
        'Resuscitation_training',
        'Emergency_guidelines',
        'Emergency_tray_or_trolley',
        'Oxygen_supplies',
        'Referral_organizations_list',
        'Ambulance',
        'Contraceptive_methods',
        'ANC_guideline_and_checklist',
        'Delivery_room_and_delivery_bed',
        'Partograph',
        'Neonatal_resuscitation_equipment',
        'Postnatal_guidelines',
        'Immunization_vaccination_cards',
        'Child_growth_monitoring',
        'Health_education_ORS',
        'TB_treatment_guidelines',
        'VCT_PITC_materials',
        'Guidelines_for_ART',
        'Qualified_specialized_staff',
        'Guidelines_for_cleaning_and_disinfection',

        // Inpatient Care Section
        'Duty_rosters',
        'Ward_rounds_and_documentation',
        'Identification_of_patients',
        'Adequate_space_and_privacy',
        'Beds_mattresses_and_linen',
        'Sufficient_operational_handwashing_stations',
        'Guidelines_for_handling_infectious_waste',
        'Vital_signs_monitoring',
        'Management_of_pain',
        'Protocol_compliance',
        'Resuscitation_equipment',
        'Guidelines_for_administering_oxygen',
        'Patient_identification',
        'Guideline_compliance',
        'Patient_and_family_education',
        'Mobility_devices',
        'Discharge_instructions',
        'Policy_for_deceased_patients',

        // Laboratory Services Section
        'Qualified_laboratory_manager',
        'Sufficient_laboratory_staff',
        'Laboratory_design',
        'Sufficient_adequate_Personal_Protective_Equipment_PPE',
        'Guidelines_for_handling_infectious_waste_lab',
        'Supplies_for_specimen_collection',
        'Labelling_of_specimens',
        'Assay_SOPs',
        'Sufficient_laboratory_equipment',
        'Storage_and_labelling_of_reagents',
        'Internal_Quality_Controls_IQA',
        'Result_registration',
        'Referral_register',

        // Diagnostic Imaging Service Section
        'Request_forms',

        // Medication Management Section
        'Qualified_pharmacy_manager',
        'Availability_of_medication',
        'Guidelines_for_procurement_of_medication',
        'Storage_area_safety',
        'Medication_labelling',
        'Prescription_requirements',
        'Dispensing_area',
        'Medication_labelling_dispensed',
        'Medication_error_reporting',

        // Facility Management Services Section
        'Infrastructure_healthcare_organization',
        'Maintenance_backup_services',
        'Infrastructure_inspections',
        'Electrical_power',
        'Water_supply',
        'Sewerage_system',
        'Quality_of_toilets_and_washrooms',
        'Equipment_maintenance',
        'Medical_gas_and_supplies',
        'Vacuum_suction_equipment',
        'ICT_equipment',

        // Support Services Section
        'Laundry_staff_orientation',
        'Laundry_area',
        'Awareness_of_infection_prevention_and_safety',
        'Cleaning_materials',
        'Waste_management'
    ];

    protected $casts = [
        'assessment_date' => 'datetime',
        'last_updated_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'compliance_percentage' => 'decimal:2',
        'total_questions' => 'integer',
        'fully_compliant_count' => 'integer',
        'partially_compliant_count' => 'integer',
        'not_compliant_count' => 'integer',
        'not_applicable_count' => 'integer',
        'year' => 'integer'
    ];

    /**
     * Relationship to the user who created the assessment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function safecarePeriod(): BelongsTo
    {
        return $this->belongsTo(SafecarePeriod::class, 'safecare_period_id');
    }

    /**
     * Check if assessment can be edited (within 7 days)
     */
    public function canEdit(): bool
    {
        return $this->created_at->gt(now()->subWeek());
    }

    public function getEditExpiresAtAttribute()
    {
        return $this->created_at->addWeek();
    }

    /**
     * Scope for getting assessments within a specific period
     */
    public function scopeInPeriod($query, $periodId)
    {
        return $query->where('safecare_period_id', $periodId);
    }

    /**
     * Scope for getting assessments by quarter and year
     */
    public function scopeByQuarter($query, $quarter, $year = null)
    {
        $query->where('quarter', $quarter);

        if ($year) {
            $query->where('year', $year);
        }

        return $query;
    }

    /**
     * Scope for getting assessments by year
     */
    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope for getting editable assessments (within 7 days)
     */
    public function scopeEditable($query)
    {
        return $query->where('created_at', '>', now()->subWeek());
    }

    /**
     * Relationship to the user who last updated the assessment
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relationship to the PHC
     */
    public function phc(): BelongsTo
    {
        return $this->belongsTo(Phc::class);
    }

    /**
     * Relationship to the LGA
     */
    public function lga(): BelongsTo
    {
        return $this->belongsTo(Lga::class);
    }

    /**
     * Relationship to the District
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Scope for date range queries
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('assessment_date', [$startDate, $endDate]);
    }

    /**
     * Scope for getting assessments for comparison
     */
    public function scopeGetForComparison($query, $phcIds, $startDate = null, $endDate = null)
    {
        $query = $query->whereIn('phc_id', $phcIds)
            ->with(['phc:id,name', 'user:id,full_name,name', 'updatedBy:id,full_name,name'])
            ->orderBy('assessment_date', 'desc');

        if ($startDate && $endDate) {
            $query->whereBetween('assessment_date', [$startDate, $endDate]);
        }

        return $query;
    }

    /**
     * Get the assessor's name (preferring full_name over name)
     */
    public function getAssessorNameAttribute()
    {
        return $this->user ? ($this->user->full_name ?? $this->user->name) : 'Unknown';
    }

    /**
     * Get the updater's name (preferring full_name over name)
     */
    public function getUpdatedByNameAttribute()
    {
        return $this->updatedBy ? ($this->updatedBy->full_name ?? $this->updatedBy->name) : null;
    }

    /**
     * Check if this assessment has been updated
     */
    public function getHasBeenUpdatedAttribute()
    {
        return !is_null($this->updated_by);
    }

    /**
     * Helper method to get all question columns
     */
    public function getQuestionColumns(): array
    {
        return [
            // Governance and Management
            'Organogram',
            'Strategic_plan_mission_operational_plan',
            'Healthcare_organization_leader',
            'Care_and_Services',
            'Qualified_supply_chain_manager',
            'Organizational_Q_structure',
            'Books_of_accounts',
            'Cash_management',
            'Fixed_asset_register_FAR',
            'Business_and_management_processes',

            // Human Resources Management
            'Staffing_plan',
            'Performance_review',
            'Staff_job_descriptions',
            'Staff_personnel_files',
            'Credentialling',
            'Staff_orientation',
            'Staff_education_and_training',

            // Patient and Family Rights and Access to Care
            'Patient_rights',
            'Patients_privacy',
            'Patient_and_family_health_education',
            'Patient_information',
            'Informed_consent',
            'Complaints_process',
            'Opening_hours_display',
            'Signage',
            'Information_about_services_and_related_fees',

            // Management of Information
            'HMIS',
            'Internal_data_analysis_meetings',
            'Patient_record_unique_ID',
            'Patient_record',
            'Health_record_checks',
            'Health_record_storage',

            // Risk Management
            'Qualified_risk_manager',
            'Risk_management_plan',
            'Occupational_Health_Safety_OHS',
            'Security_system',
            'Fire_fighting_equipment',
            'IPC_policies',
            'Healthcare_waste_collection_assets',

            // Primary Healthcare (Outpatient) Services
            'Number_of_staff',
            'OPD_layout',
            'Waiting_area_ventilation_and_cleanliness',
            'Sufficient_consultation_rooms',
            'Handwashing_facilities',
            'PPE',
            'Sterilization_equipment',
            'Processing_sterile_packs',
            'Triage_process',
            'Qualified_staff_for_conducting_assessments',
            'Guideline_knowledge_Sexual_Transmitted_Infections_STI',
            'Guideline_knowledge_Rapid_Diagnostic_Tests_RDT',
            'Malaria_diagnostics',
            'Minor_surgery_equipment',
            'Vital_signs_equipment',
            'Resuscitation_training',
            'Emergency_guidelines',
            'Emergency_tray_or_trolley',
            'Oxygen_supplies',
            'Referral_organizations_list',
            'Ambulance',
            'Contraceptive_methods',
            'ANC_guideline_and_checklist',
            'Delivery_room_and_delivery_bed',
            'Partograph',
            'Neonatal_resuscitation_equipment',
            'Postnatal_guidelines',
            'Immunization_vaccination_cards',
            'Child_growth_monitoring',
            'Health_education_ORS',
            'TB_treatment_guidelines',
            'VCT_PITC_materials',
            'Guidelines_for_ART',
            'Qualified_specialized_staff',
            'Guidelines_for_cleaning_and_disinfection',

            // Inpatient Care
            'Duty_rosters',
            'Ward_rounds_and_documentation',
            'Identification_of_patients',
            'Adequate_space_and_privacy',
            'Beds_mattresses_and_linen',
            'Sufficient_operational_handwashing_stations',
            'Guidelines_for_handling_infectious_waste',
            'Vital_signs_monitoring',
            'Management_of_pain',
            'Protocol_compliance',
            'Resuscitation_equipment',
            'Guidelines_for_administering_oxygen',
            'Patient_identification',
            'Guideline_compliance',
            'Patient_and_family_education',
            'Mobility_devices',
            'Discharge_instructions',
            'Policy_for_deceased_patients',

            // Laboratory Services
            'Qualified_laboratory_manager',
            'Sufficient_laboratory_staff',
            'Laboratory_design',
            'Sufficient_adequate_Personal_Protective_Equipment_PPE',
            'Guidelines_for_handling_infectious_waste_lab',
            'Supplies_for_specimen_collection',
            'Labelling_of_specimens',
            'Assay_SOPs',
            'Sufficient_laboratory_equipment',
            'Storage_and_labelling_of_reagents',
            'Internal_Quality_Controls_IQA',
            'Result_registration',
            'Referral_register',

            // Diagnostic Imaging Service
            'Request_forms',

            // Medication Management
            'Qualified_pharmacy_manager',
            'Availability_of_medication',
            'Guidelines_for_procurement_of_medication',
            'Storage_area_safety',
            'Medication_labelling',
            'Prescription_requirements',
            'Dispensing_area',
            'Medication_labelling_dispensed',
            'Medication_error_reporting',

            // Facility Management Services
            'Infrastructure_healthcare_organization',
            'Maintenance_backup_services',
            'Infrastructure_inspections',
            'Electrical_power',
            'Water_supply',
            'Sewerage_system',
            'Quality_of_toilets_and_washrooms',
            'Equipment_maintenance',
            'Medical_gas_and_supplies',
            'Vacuum_suction_equipment',
            'ICT_equipment',

            // Support Services
            'Laundry_staff_orientation',
            'Laundry_area',
            'Awareness_of_infection_prevention_and_safety',
            'Cleaning_materials',
            'Waste_management'
        ];
    }
}
<<<<<<< HEAD
=======
// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use Carbon\Carbon;

// class SafecareAssessment extends Model
// {
//     protected $table = 'safecare_assessments';

//     protected $fillable = [
//         'user_id',
//         'updated_by',
//         'district_id',
//         'lga_id',
//         'phc_id',
//         'assessment_date',
//         'last_updated_date',
//         'total_questions',
//         'fully_compliant_count',
//         'partially_compliant_count',
//         'not_compliant_count',
//         'not_applicable_count',
//         'compliance_percentage',
//         'safecare_period_id',
//         'quarter',
//         'year',
//         'q_1_1_1_1',
//         'q_1_1_2_1',
//         'q_1_2_1_1',
//         'q_1_2_2_1',
//         'q_1_2_3_1',
//         'q_1_2_4_1',
//         'q_1_2_5_1',
//         'q_1_2_6_1',
//         'q_1_2_7_1',
//         'q_1_2_8_1',
//         'q_2_1_1_1',
//         'q_2_1_2_1',
//         'q_2_2_1_1',
//         'q_2_2_2_1',
//         'q_2_2_3_1',
//         'q_2_3_1_1',
//         'q_2_3_2_1',
//         'q_3_1_1_1',
//         'q_3_1_3_1',
//         'q_3_1_5_1',
//         'q_3_1_6_1',
//         'q_3_2_1_1',
//         'q_3_1_2_1',
//         'q_3_1_4_1',
//         'q_3_2_3_1',
//         'q_3_2_2_1',
//         'q_4_1_2_1',
//         'q_4_2_4_1',
//         'q_4_2_3_1',
//         'q_4_1_1_1',
//         'q_4_2_2_1',
//         'q_4_2_1_1',
//         'q_5_1_1_1',
//         'q_5_1_2_1',
//         'q_5_1_3_1',
//         'q_5_2_2_1',
//         'q_5_3_1_1',
//         'q_5_3_2_1',
//         'q_5_2_1_1',
//         'q_6_1_1_1',
//         'q_6_3_3_1',
//         'q_6_3_4_1',
//         'q_6_4_1_1',
//         'q_6_4_3_1',
//         'q_6_4_4_1',
//         'q_6_4_6_1',
//         'q_6_5_2_1',
//         'q_6_5_5_1',
//         'q_6_7_2_1',
//         'q_6_2_1_1',
//         'q_6_2_2_1',
//         'q_6_2_3_1',
//         'q_6_3_1_1',
//         'q_6_3_2_1',
//         'q_6_4_2_1',
//         'q_6_4_5_1',
//         'q_6_4_7_1',
//         'q_6_5_1_1',
//         'q_6_5_3_1',
//         'q_6_5_4_1',
//         'q_6_6_1_1',
//         'q_6_6_2_1',
//         'q_6_5_6_1',
//         'q_6_6_5_1',
//         'q_6_6_3_1',
//         'q_6_6_4_1',
//         'q_6_6_7_1',
//         'q_6_6_8_1',
//         'q_6_6_6_1',
//         'q_6_6_9_1',
//         'q_6_7_3_1',
//         'q_6_7_1_1',
//         'q_6_9_1_1',
//         'q_6_9_2_1',
//         'q_7_1_1_1',
//         'q_7_1_3_1',
//         'q_7_2_1_1',
//         'q_7_1_2_1',
//         'q_7_2_2_1',
//         'q_7_3_2_1',
//         'q_7_3_1_1',
//         'q_7_4_1_1',
//         'q_7_4_10_1',
//         'q_7_4_11_1',
//         'q_7_4_2_1',
//         'q_7_4_3_1',
//         'q_7_4_4_1',
//         'q_7_4_5_1',
//         'q_7_4_6_1',
//         'q_7_4_7_1',
//         'q_7_4_8_1',
//         'q_7_4_9_1',
//         'q_9_1_1_1',
//         'q_9_1_2_1',
//         'q_9_2_1_1',
//         'q_9_2_3_1',
//         'q_9_2_2_1',
//         'q_9_3_7_1',
//         'q_9_3_8_1',
//         'q_9_3_2_1',
//         'q_9_3_1_1',
//         'q_9_3_3_1',
//         'q_9_3_4_1',
//         'q_9_3_5_1',
//         'q_9_3_6_1',
//         'q_10_1_3_1',
//         'q_11_1_1_1',
//         'q_11_3_1_1',
//         'q_11_4_3_1',
//         'q_11_2_1_1',
//         'q_11_2_2_1',
//         'q_11_3_2_1',
//         'q_11_4_1_1',
//         'q_11_4_2_1',
//         'q_11_5_1_1',
//         'q_12_1_3_1',
//         'q_12_1_4_1',
//         'q_12_2_1_1',
//         'q_12_2_3_1',
//         'q_12_1_1_1',
//         'q_12_1_2_1',
//         'q_12_1_5_1',
//         'q_12_1_6_1',
//         'q_12_1_7_1',
//         'q_12_2_2_1',
//         'q_12_2_4_1',
//         'q_13_2_2_1',
//         'q_13_3_2_1',
//         'q_13_2_1_1',
//         'q_13_3_3_1',
//         'q_13_3_1_1'
//     ];


//     protected $casts = [
//         'assessment_date' => 'datetime',
//         'last_updated_date' => 'datetime',
//         'created_at' => 'datetime',
//         'updated_at' => 'datetime',
//         'compliance_percentage' => 'decimal:2',
//         'total_questions' => 'integer',
//         'fully_compliant_count' => 'integer',
//         'partially_compliant_count' => 'integer',
//         'not_compliant_count' => 'integer',
//         'not_applicable_count' => 'integer',
//         'year' => 'integer'
//     ];

//     /**
//      * Relationship to the user who created the assessment
//      */
//     public function user(): BelongsTo
//     {
//         return $this->belongsTo(User::class, 'user_id');
//     }

//     public function safecarePeriod(): BelongsTo
//     {
//         return $this->belongsTo(SafecarePeriod::class, 'safecare_period_id');
//     }

//     /**
//      * Check if assessment can be edited (within 7 days)
//      */
//     public function canEdit(): bool
//     {
//         return $this->created_at->gt(now()->subWeek());
//     }

//     public function getEditExpiresAtAttribute()
//     {
//         return $this->created_at->addWeek();
//     }

//     /**
//      * Scope for getting assessments within a specific period
//      */
//     public function scopeInPeriod($query, $periodId)
//     {
//         return $query->where('safecare_period_id', $periodId);
//     }

//     /**
//      * Scope for getting assessments by quarter and year
//      */
//     public function scopeByQuarter($query, $quarter, $year = null)
//     {
//         $query->where('quarter', $quarter);

//         if ($year) {
//             $query->where('year', $year);
//         }

//         return $query;
//     }

//     /**
//      * Scope for getting assessments by year
//      */
//     public function scopeByYear($query, $year)
//     {
//         return $query->where('year', $year);
//     }

//     /**
//      * Scope for getting assessments within date range
//      */
//     // public function scopeDateRange($query, $startDate, $endDate)
//     // {
//     //     return $query->whereBetween('assessment_date', [$startDate, $endDate]);
//     // }

//     /**
//      * Scope for getting assessments for comparison
//      */
//     // public function scopeGetForComparison($query, $phcIds, $startDate = null, $endDate = null)
//     // {
//     //     $query->whereIn('phc_id', $phcIds)
//     //         ->with(['phc', 'district', 'lga', 'safecarePeriod'])
//     //         ->orderBy('assessment_date', 'desc');

//     //     if ($startDate && $endDate) {
//     //         $query->whereBetween('assessment_date', [$startDate, $endDate]);
//     //     }

//     //     return $query;
//     // }

//     /**
//      * Scope for getting editable assessments (within 7 days)
//      */
//     public function scopeEditable($query)
//     {
//         return $query->where('created_at', '>', now()->subWeek());
//     }

//     /**
//      * Relationship to the user who last updated the assessment
//      */
//     public function updatedBy(): BelongsTo
//     {
//         return $this->belongsTo(User::class, 'updated_by');
//     }

//     /**
//      * Relationship to the PHC
//      */
//     public function phc(): BelongsTo
//     {
//         return $this->belongsTo(Phc::class);
//     }

//     /**
//      * Relationship to the LGA
//      */
//     public function lga(): BelongsTo
//     {
//         return $this->belongsTo(Lga::class);
//     }

//     /**
//      * Relationship to the District
//      */
//     public function district(): BelongsTo
//     {
//         return $this->belongsTo(District::class);
//     }

//     /**
//      * Scope for date range queries
//      */
//     public function scopeDateRange($query, $startDate, $endDate)
//     {
//         return $query->whereBetween('assessment_date', [$startDate, $endDate]);
//     }

//     /**
//      * Scope for getting assessments for comparison
//      */
//     public function scopeGetForComparison($query, $phcIds, $startDate = null, $endDate = null)
//     {
//         $query = $query->whereIn('phc_id', $phcIds)
//             ->with(['phc:id,name', 'user:id,full_name,name', 'updatedBy:id,full_name,name'])
//             ->orderBy('assessment_date', 'desc');

//         if ($startDate && $endDate) {
//             $query->whereBetween('assessment_date', [$startDate, $endDate]);
//         }

//         return $query;
//     }

//     /**
//      * Get the assessor's name (preferring full_name over name)
//      */
//     public function getAssessorNameAttribute()
//     {
//         return $this->user ? ($this->user->full_name ?? $this->user->name) : 'Unknown';
//     }

//     /**
//      * Get the updater's name (preferring full_name over name)
//      */
//     public function getUpdatedByNameAttribute()
//     {
//         return $this->updatedBy ? ($this->updatedBy->full_name ?? $this->updatedBy->name) : null;
//     }

//     /**
//      * Check if this assessment has been updated
//      */
//     public function getHasBeenUpdatedAttribute()
//     {
//         return !is_null($this->updated_by);
//     }
// }
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
