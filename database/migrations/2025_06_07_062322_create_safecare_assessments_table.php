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
        Schema::create('safecare_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('district_id');
            $table->unsignedBigInteger('lga_id');
            $table->unsignedBigInteger('phc_id');
            $table->timestamp('assessment_date');
            $table->integer('total_questions')->default(131);
            $table->integer('fully_compliant_count')->default(0);
            $table->integer('partially_compliant_count')->default(0);
            $table->integer('not_compliant_count')->default(0);
            $table->integer('not_applicable_count')->default(0);
            $table->decimal('compliance_percentage', 5, 2)->default(0.00);

            // All 131 question response columns
            $table->enum('q_1_1_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Organogram');
            $table->enum('q_1_1_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Strategic plan, mission and operational plan');
            $table->enum('q_1_2_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Healthcare organization leader');
            $table->enum('q_1_2_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Care and Services');
            $table->enum('q_1_2_3_1', ['NC','PC','FC','NA'])->nullable()->comment('Qualified supply chain manager');
            $table->enum('q_1_2_4_1', ['NC','PC','FC','NA'])->nullable()->comment('Organizational Q structure');
            $table->enum('q_1_2_5_1', ['NC','PC','FC','NA'])->nullable()->comment('Books of accounts');
            $table->enum('q_1_2_6_1', ['NC','PC','FC','NA'])->nullable()->comment('Cash management');
            $table->enum('q_1_2_7_1', ['NC','PC','FC','NA'])->nullable()->comment('Fixed asset register (FAR)');
            $table->enum('q_1_2_8_1', ['NC','PC','FC','NA'])->nullable()->comment('Business and management processes');
            $table->enum('q_2_1_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Staffing plan');
            $table->enum('q_2_1_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Performance review');
            $table->enum('q_2_2_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Staff job descriptions');
            $table->enum('q_2_2_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Staff personnel files');
            $table->enum('q_2_2_3_1', ['NC','PC','FC','NA'])->nullable()->comment('Credentialling');
            $table->enum('q_2_3_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Staff orientation');
            $table->enum('q_2_3_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Staff education and training');
            $table->enum('q_3_1_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Patient rights');
            $table->enum('q_3_1_3_1', ['NC','PC','FC','NA'])->nullable()->comment('Patient and family health education');
            $table->enum('q_3_1_5_1', ['NC','PC','FC','NA'])->nullable()->comment('Informed consent');
            $table->enum('q_3_1_6_1', ['NC','PC','FC','NA'])->nullable()->comment('Complaints process');
            $table->enum('q_3_2_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Opening hours display');
            $table->enum('q_3_1_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Patient\'s privacy');
            $table->enum('q_3_1_4_1', ['NC','PC','FC','NA'])->nullable()->comment('Patient information');
            $table->enum('q_3_2_3_1', ['NC','PC','FC','NA'])->nullable()->comment('Information about services and related fees');
            $table->enum('q_3_2_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Signage');
            $table->enum('q_4_1_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Internal data analysis meetings');
            $table->enum('q_4_2_4_1', ['NC','PC','FC','NA'])->nullable()->comment('Health record storage');
            $table->enum('q_4_2_3_1', ['NC','PC','FC','NA'])->nullable()->comment('Health record checks');
            $table->enum('q_4_1_1_1', ['NC','PC','FC','NA'])->nullable()->comment('HMIS');
            $table->enum('q_4_2_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Patient record');
            $table->enum('q_4_2_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Patient record unique ID');
            $table->enum('q_5_1_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Qualified risk manager');
            $table->enum('q_5_1_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Risk management plan');
            $table->enum('q_5_1_3_1', ['NC','PC','FC','NA'])->nullable()->comment('Occupational Health Safety (OHS)');
            $table->enum('q_5_2_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Fire fighting equipment');
            $table->enum('q_5_3_1_1', ['NC','PC','FC','NA'])->nullable()->comment('IPC policies');
            $table->enum('q_5_3_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Healthcare waste collection assets');
            $table->enum('q_5_2_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Security sytem');
            $table->enum('q_6_1_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Number of staff');
            $table->enum('q_6_3_3_1', ['NC','PC','FC','NA'])->nullable()->comment('Sterilization equipment');
            $table->enum('q_6_3_4_1', ['NC','PC','FC','NA'])->nullable()->comment('Processing sterile packs');
            $table->enum('q_6_4_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Triage process');
            $table->enum('q_6_4_3_1', ['NC','PC','FC','NA'])->nullable()->comment('Guideline and knowledge about Sexual Transmitted Infections (STI)');
            $table->enum('q_6_4_4_1', ['NC','PC','FC','NA'])->nullable()->comment('Guideline and knowledge about Rapid Diagnostic Tests (RDT)');
            $table->enum('q_6_4_6_1', ['NC','PC','FC','NA'])->nullable()->comment('Minor surgery equipment');
            $table->enum('q_6_5_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Emergency guidelines');
            $table->enum('q_6_5_5_1', ['NC','PC','FC','NA'])->nullable()->comment('Referral organzizations list');
            $table->enum('q_6_7_2_1', ['NC','PC','FC','NA'])->nullable()->comment('VCT/PITC materials');
            $table->enum('q_6_2_1_1', ['NC','PC','FC','NA'])->nullable()->comment('OPD lay-out');
            $table->enum('q_6_2_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Waiting area ventilation and cleanliness');
            $table->enum('q_6_2_3_1', ['NC','PC','FC','NA'])->nullable()->comment('Sufficient consultation rooms');
            $table->enum('q_6_3_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Handwashing facilities');
            $table->enum('q_6_3_2_1', ['NC','PC','FC','NA'])->nullable()->comment('PPE');
            $table->enum('q_6_4_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Qualified staff for conducting assessments');
            $table->enum('q_6_4_5_1', ['NC','PC','FC','NA'])->nullable()->comment('Malaria diagnostics');
            $table->enum('q_6_4_7_1', ['NC','PC','FC','NA'])->nullable()->comment('Vital signs equipment');
            $table->enum('q_6_5_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Resuscitation training');
            $table->enum('q_6_5_3_1', ['NC','PC','FC','NA'])->nullable()->comment('Emergency tray or trolley');
            $table->enum('q_6_5_4_1', ['NC','PC','FC','NA'])->nullable()->comment('Oxygen supplies');
            $table->enum('q_6_6_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Contraceptive methods');
            $table->enum('q_6_6_2_1', ['NC','PC','FC','NA'])->nullable()->comment('ANC guideline and checklist');
            $table->enum('q_6_5_6_1', ['NC','PC','FC','NA'])->nullable()->comment('Ambulance');
            $table->enum('q_6_6_5_1', ['NC','PC','FC','NA'])->nullable()->comment('Neonatal resuscitation equipment');
            $table->enum('q_6_6_3_1', ['NC','PC','FC','NA'])->nullable()->comment('Delivery room and delivery bed');
            $table->enum('q_6_6_4_1', ['NC','PC','FC','NA'])->nullable()->comment('Partograph');
            $table->enum('q_6_6_7_1', ['NC','PC','FC','NA'])->nullable()->comment('Immunization (vacciation cards)');
            $table->enum('q_6_6_8_1', ['NC','PC','FC','NA'])->nullable()->comment('Child growth monitoring');
            $table->enum('q_6_6_6_1', ['NC','PC','FC','NA'])->nullable()->comment('Postnatal guidelines');
            $table->enum('q_6_6_9_1', ['NC','PC','FC','NA'])->nullable()->comment('Health education (ORS)');
            $table->enum('q_6_7_3_1', ['NC','PC','FC','NA'])->nullable()->comment('Guidelines for ART');
            $table->enum('q_6_7_1_1', ['NC','PC','FC','NA'])->nullable()->comment('TB treatment guidelines');
            $table->enum('q_6_9_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Qualified specialized staff');
            $table->enum('q_6_9_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Guidelines for cleaning and disfection');
            $table->enum('q_7_1_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Duty rosters');
            $table->enum('q_7_1_3_1', ['NC','PC','FC','NA'])->nullable()->comment('Identification of patients');
            $table->enum('q_7_2_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Adequate space and privacy');
            $table->enum('q_7_1_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Ward rounds and documentation');
            $table->enum('q_7_2_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Beds, matresses and linen');
            $table->enum('q_7_3_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Guidelines for handling (infectious) waste');
            $table->enum('q_7_3_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Sufficient and operational handwashing stations');
            $table->enum('q_7_4_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Vital signs monitoring');
            $table->enum('q_7_4_10_1', ['NC','PC','FC','NA'])->nullable()->comment('Discharge instructions');
            $table->enum('q_7_4_11_1', ['NC','PC','FC','NA'])->nullable()->comment('Policy for deceased patients');
            $table->enum('q_7_4_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Management of pain');
            $table->enum('q_7_4_3_1', ['NC','PC','FC','NA'])->nullable()->comment('Protocol compliance');
            $table->enum('q_7_4_4_1', ['NC','PC','FC','NA'])->nullable()->comment('Resuscitation equipment');
            $table->enum('q_7_4_5_1', ['NC','PC','FC','NA'])->nullable()->comment('Guidelines for administering oxygen');
            $table->enum('q_7_4_6_1', ['NC','PC','FC','NA'])->nullable()->comment('Patient identification');
            $table->enum('q_7_4_7_1', ['NC','PC','FC','NA'])->nullable()->comment('Guideline compliance');
            $table->enum('q_7_4_8_1', ['NC','PC','FC','NA'])->nullable()->comment('Patient and family education');
            $table->enum('q_7_4_9_1', ['NC','PC','FC','NA'])->nullable()->comment('Mobility devices');
            $table->enum('q_9_1_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Qualified laboratory manager');
            $table->enum('q_9_1_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Sufficient laboratory staff');
            $table->enum('q_9_2_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Laboratory design');
            $table->enum('q_9_2_3_1', ['NC','PC','FC','NA'])->nullable()->comment('Guidelines for handling (infectious) waste');
            $table->enum('q_9_2_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Sufficient and adequeate Personal Protective Equipment (PPE)');
            $table->enum('q_9_3_7_1', ['NC','PC','FC','NA'])->nullable()->comment('Result registration');
            $table->enum('q_9_3_8_1', ['NC','PC','FC','NA'])->nullable()->comment('Referral register');
            $table->enum('q_9_3_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Labelling of specimens');
            $table->enum('q_9_3_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Supplies for specimen collection');
            $table->enum('q_9_3_3_1', ['NC','PC','FC','NA'])->nullable()->comment('Assay SOPs');
            $table->enum('q_9_3_4_1', ['NC','PC','FC','NA'])->nullable()->comment('Sufficient laboratory equipment');
            $table->enum('q_9_3_5_1', ['NC','PC','FC','NA'])->nullable()->comment('Storage and labelling of reagents');
            $table->enum('q_9_3_6_1', ['NC','PC','FC','NA'])->nullable()->comment('Internal Quality Controls (IQA)');
            $table->enum('q_10_1_3_1', ['NC','PC','FC','NA'])->nullable()->comment('Request forms');
            $table->enum('q_11_1_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Qualified pharmacy manager');
            $table->enum('q_11_3_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Storage area safety');
            $table->enum('q_11_4_3_1', ['NC','PC','FC','NA'])->nullable()->comment('Medication labelling');
            $table->enum('q_11_2_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Availability of medication');
            $table->enum('q_11_2_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Guidelines for procurement of medication');
            $table->enum('q_11_3_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Medication labelling');
            $table->enum('q_11_4_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Prescription requirements');
            $table->enum('q_11_4_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Dispensing area');
            $table->enum('q_11_5_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Medication error reporting');
            $table->enum('q_12_1_3_1', ['NC','PC','FC','NA'])->nullable()->comment('Infrastucture inspections');
            $table->enum('q_12_1_4_1', ['NC','PC','FC','NA'])->nullable()->comment('Electrical power');
            $table->enum('q_12_2_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Equipment maintenance');
            $table->enum('q_12_2_3_1', ['NC','PC','FC','NA'])->nullable()->comment('Vacuum suction equipment');
            $table->enum('q_12_1_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Infrastructure healthcare organization');
            $table->enum('q_12_1_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Maintenance (back-up) services');
            $table->enum('q_12_1_5_1', ['NC','PC','FC','NA'])->nullable()->comment('Water supply');
            $table->enum('q_12_1_6_1', ['NC','PC','FC','NA'])->nullable()->comment('Sewerage system');
            $table->enum('q_12_1_7_1', ['NC','PC','FC','NA'])->nullable()->comment('Quality of toilets and washrooms');
            $table->enum('q_12_2_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Medical gas and supplies');
            $table->enum('q_12_2_4_1', ['NC','PC','FC','NA'])->nullable()->comment('ICT equipment');
            $table->enum('q_13_2_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Laundry area');
            $table->enum('q_13_3_2_1', ['NC','PC','FC','NA'])->nullable()->comment('Cleaning materials');
            $table->enum('q_13_2_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Laundry staff orientation');
            $table->enum('q_13_3_3_1', ['NC','PC','FC','NA'])->nullable()->comment('Waste management');
            $table->enum('q_13_3_1_1', ['NC','PC','FC','NA'])->nullable()->comment('Awareness of infection prevention and safety');

            // Comment columns for each question (optional - add if you want to preserve comments)
            $table->text('comment_1_1_1_1')->nullable();
            $table->text('comment_1_1_2_1')->nullable();
            $table->text('comment_1_2_1_1')->nullable();
            $table->text('comment_1_2_2_1')->nullable();
            $table->text('comment_1_2_3_1')->nullable();
            // Add more comment columns as needed...

            $table->timestamps();

            // Indexes
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('district_id')->references('id')->on('districts');
            $table->foreign('lga_id')->references('id')->on('lgas');
            $table->foreign('phc_id')->references('id')->on('phcs');

            $table->index(['phc_id', 'assessment_date']);
            $table->index('assessment_date');
            $table->index('compliance_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('safecare_assessments');
    }
};
