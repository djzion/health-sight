<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Primary Health Care Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background-color: #f1f5f9;
            color: #333;
            font-family: 'Inter', sans-serif;
        }

        .sidebar {
            height: 100vh;
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: #fff;
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            transition: all 0.3s ease;
            box-shadow: 2px 0 6px rgba(0, 0, 0, 0.2);
        }

        .sidebar h2 {
            margin-bottom: 30px;
            font-weight: 700;
        }

        .sidebar a {
            color: #fff;
            text-decoration: none;
            padding: 12px 0;
            display: block;
            font-size: 1.1rem;
            border-radius: 6px;
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background-color: #0199dc;
        }

        .sidebar .logout-btn {
            margin-top: 30px;
        }

        .main-content {
            margin-left: 250px;
            padding: 30px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
            width: 1200px !important;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 2rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .header .profile {
            display: flex;
            align-items: center;
        }

        .profile img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 12px;
            border: 2px solid #2c3e50;
        }

        .metrics .card {
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #3498db, #6dd5ed);
            color: #fff;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s;
            cursor: pointer;
        }

        .metrics .card:hover {
            transform: translateY(-10px);
        }

        .metrics h3 {
            font-size: 2rem;
            margin-bottom: 0;
        }

        .table thead {
            background-color: #2c3e50;
            color: #fff;
        }

        .assessment-card {
            border-left: 6px solid #1abc9c;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
                padding: 15px;
            }

            .sidebar h2 {
                display: none;
            }

            .sidebar a {
                font-size: 0.9rem;
                text-align: center;
            }

            .main-content {
                margin-left: 60px;
            }
        }

        .container {
            max-width: 1100px !important;
            margin-right: 5rem !important;
        }

        @media (max-width: 576px) {}

        .bg-primary {
            background-color: #0199dc !important;
        }

        .btn-primary {
            background-color: #0199dc !important;
        }

        .form-input {
            width: 10%;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            display: block;
        }

        .form-check-input:checked {
            background-color: #0199dc;
            border-color: #0199dc;
        }

        .badge {
            font-size: 0.9rem;
            padding: 0.4rem 0.6rem;
        }

        .badge.bg-info {
            background-color: #17a2b8 !important;
        }

        .badge.bg-primary {
            background-color: #0199dc !important;
        }

        hr {
            margin: 1.5rem 0;
            border-color: #dee2e6;
        }

        .text-muted {
            color: #6c757d !important;
        }

        /* Animation for new content */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .assessment-details-row {
            animation: fadeIn 0.5s ease-out;
        }

        /* Add styling to make the previous assessment information stand out */
        .badge {
            font-size: 0.9rem;
            padding: 0.4rem 0.8rem;
            font-weight: 500;
            border-radius: 6px;
            display: inline-block;
        }

        .assessment-label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: #444;
            font-size: 0.9rem;
        }

        .badge.bg-info {
            background-color: #17a2b8 !important;
        }

        .badge.bg-primary {
            background-color: #17a2b8 !important;
        }

        .badge.bg-secondary {
            background-color: #17a2b8 !important;
        }

        .badge.bg-success {
            background-color: #17a2b8 !important;
        }

        .badge.bg-dark {
            background-color: #17a2b8 !important;
        }

        .previous-assessment-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            border-left: 4px solid #0199dc;
            margin-top: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .previous-assessment-info h6 {
            color: #343a40;
            font-weight: 600;
            margin-bottom: 15px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
        }

        select {
            width: 100%;
            max-width: 500px;
            padding: 10px 15px;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 8px;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            display: block;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
        }

        select:focus {
            border-color: #0199dc;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(1, 153, 220, 0.25);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }

        .form-group select {
            width: 100%;
            padding: 10px 15px;
            border-radius: 6px;
            border: 1px solid #ced4da;
            background-color: #fff;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }

        .form-row {
            margin-bottom: 1.5rem;
        }

        .alert-danger {
            border-left: 5px solid #dc3545;
            animation: fadeInDown 0.5s ease-in-out;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            border-radius: 6px 6px 0 0;
        }

        .modal-title {
            font-weight: 600;
        }

        .modal-body {
            padding: 25px;
        }

        .modal-footer {
            padding: 15px 25px;
            border-top: 1px solid #dee2e6;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn-close {
            color: white;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

</head>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2><i class="fas fa-hospital-user me-2"></i>Health Dashboard</h2>
            <a href="{{ route('dashboard') }}"><i class="fas fa-home me-2"></i> Dashboard Home</a>
            <a href="#"><i class="fas fa-user-injured me-2"></i> Patients</a>
            <a href="#"><i class="fas fa-calendar-alt me-2"></i> Appointments</a>
            <a href="{{ route('assessments.index') }}"><i class="fas fa-file-medical-alt me-2"></i> Assessment</a>
            <a href="#" data-bs-toggle="modal" data-bs-target="#qipModal"><i
                    class="fas fa-file-medical-alt me-2"></i> Quality Improvement</a>
            <a href="#"><i class="fas fa-tasks me-2"></i> Tasks</a>
            <a href="#"><i class="fas fa-chart-line me-2"></i> Reports</a>
            <a href="#"><i class="fas fa-envelope me-2"></i> Messages</a>
            <a href="#"><i class="fas fa-cog me-2"></i> Settings</a>

            <!-- Logout -->
            <div class="logout-btn">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-light w-100">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content p-4">
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Access Denied:</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Dashboard content would go here -->
            <div class="header">
                <div>
                    <h1>Welcome, {{ Auth::user()->full_name }}</h1>
                    <p class="text-muted">Here's an overview of your day.</p>
                </div>
                <div class="profile">
                    <img src="https://via.placeholder.com/50" alt="User Profile">
                    <span>{{ Auth::user()->full_name }}</span>
                </div>
            </div>

            <!-- QIP Modal -->
            <div class="modal fade" id="qipModal" tabindex="-1" aria-labelledby="qipModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="qipModalLabel">Quality Improvement Assessment for PHCs</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form id="qip-selection-form">
                            @csrf
                            <div class="modal-body">
                                {{-- <div class="form-row"> --}}
                                <div class="form-group">
                                    <label for="district_id">District</label>
                                    <select id="district_id" name="district_id" required>
                                        <option value="">Select District</option>
                                        @foreach ($districts as $district)
                                            <option value="{{ $district->id }}" data-name="{{ $district->name }}">
                                                {{ $district->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="lga_id">Local Government Area</label>
                                    <select id="lga_id" name="lga_id" required>
                                        <option value="">Select Local Government Area</option>
                                    </select>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="phc_id">Primary Health Care (PHC)</label>
                                        <select id="phc_id" name="phc_id" required>
                                            <option value="">Select Primary Health Care (PHC)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" id="submit-selection" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i> Load Assessment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <!-- Scripts -->
    <script>
        $(document).ready(function() {
            // Setup AJAX with CSRF token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Quality Improvement modal trigger
            $('#qip-modal-trigger').on('click', function(e) {
                e.preventDefault();
                var modal = new bootstrap.Modal(document.getElementById('qipModal'));
                modal.show();
            });

            // Dropdown cascade for District -> LGA -> PHC
            $('#district_id').on('change', function() {
                var districtId = this.value;
                $('#lga_id').html('<option value="">Select LGA</option>');
                $('#phc_id').html('<option value="">Select PHC</option>');
                if (districtId) {
                    $.ajax({
                        url: '/get-lgas/' + districtId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $.each(data, function(key, value) {
                                $('#lga_id').append('<option value="' + value.id +
                                    '" data-name="' + value.name + '">' + value
                                    .name + '</option>');
                            });
                        }
                    });
                }
            });

            $('#lga_id').on('change', function() {
                var lgaId = this.value;
                $('#phc_id').html('<option value="">Select PHC</option>');
                if (lgaId) {
                    $.ajax({
                        url: '/get-phcs/' + lgaId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $.each(data, function(key, value) {
                                $('#phc_id').append('<option value="' + value.id +
                                    '" data-name="' + value.name + '">' + value
                                    .name + '</option>');
                            });
                        }
                    });
                }
            });

            // Handle form submission - store values in localStorage and load assessment
            $('#submit-selection').on('click', function() {
                var district_id = $('#district_id').val();
                var lga_id = $('#lga_id').val();
                var phc_id = $('#phc_id').val();

                // Validate selections
                if (!district_id || !lga_id || !phc_id) {
                    alert('Please select District, LGA, and PHC');
                    return;
                }

                // Store selections in localStorage
                var selections = {
                    district_id: district_id,
                    district_name: $('#district_id option:selected').data('name'),
                    lga_id: lga_id,
                    lga_name: $('#lga_id option:selected').data('name'),
                    phc_id: phc_id,
                    phc_name: $('#phc_id option:selected').data('name')
                };

                localStorage.setItem('qip_selections', JSON.stringify(selections));

                // Close modal
                var modal = bootstrap.Modal.getInstance(document.getElementById('qipModal'));
                modal.hide();

                loadSafecareAssessment();
            });

            function loadSafecareAssessment() {
                var selections = JSON.parse(localStorage.getItem('qip_selections'));
                console.log("Loading assessment with selections:", selections);

                $('.main-content').append(
                    '<div id="loading" class="text-center mt-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading assessment questions...</p></div>'
                );

                $.ajax({
                    url: '/get-safecare-assessment',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        district_id: selections.district_id,
                        lga_id: selections.lga_id,
                        phc_id: selections.phc_id
                    },
                    success: function(response) {
                        $('#loading').remove();
                        console.log("Response received:", response);

                        if (!response.questions || response.questions.length === 0) {
                            $('.main-content').append(
                                '<div class="alert alert-warning">No assessment questions found. Please check your database.</div>'
                            );
                            return;
                        }

                        // Store response globally so we can access it in the displayAssessment function
                        window.response = response;
                        displayAssessment(response.questions, selections);
                    },
                    error: function(xhr, status, error) {
                        $('#loading').remove();
                        console.error("AJAX Error:", status, error);
                        console.error("Response:", xhr.responseText);
                        $('.main-content').append(
                            '<div class="alert alert-danger">Error loading assessment. Please try again.</div>'
                        );
                    }
                });
            }


            function displayAssessment(questions, selections) {
                // Clear any existing assessment
                $('#assessment-container').remove();

                console.log("Questions received:", questions);

                // First sort the questions by question_no
                questions.sort(function(a, b) {
                    // Convert question numbers to comparable format
                    var aNum = a.question_no.split('.').map(Number);
                    var bNum = b.question_no.split('.').map(Number);

                    // Compare each part of the question number
                    for (var i = 0; i < Math.max(aNum.length, bNum.length); i++) {
                        var aVal = aNum[i] || 0;
                        var bVal = bNum[i] || 0;
                        if (aVal !== bVal) {
                            return aVal - bVal;
                        }
                    }
                    return 0;
                });

                // Group questions by section
                var sectionGroups = {};
                questions.forEach(function(question) {
                    if (!sectionGroups[question.section]) {
                        sectionGroups[question.section] = [];
                    }
                    sectionGroups[question.section].push(question);
                });

                var html = `
<div id="assessment-container" class="mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quality Improvement Assessment</h2>
        <button id="save-assessment" class="btn btn-success">
            <i class="fas fa-save me-2"></i> Save Assessment
        </button>
    </div>

    <!-- Add status container for showing validation issues -->
    <div id="validation-status" class="mb-4" style="display: none;"></div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Assessment Details</h5>`;

                // Add previous assessment details if available
                if (window.response && window.response.safecare_result) {
                    const result = window.response.safecare_result;
                    html += `
            <div class="previous-assessment-info">
                <h6>Previous Assessment Information</h6>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <span class="assessment-label">District:</span>
                        <span class="badge bg-secondary">${selections.district_name}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="assessment-label">LGA:</span>
                        <span class="badge bg-secondary">${selections.lga_name}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="assessment-label">PHC:</span>
                        <span class="badge bg-secondary">${selections.phc_name}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="assessment-label">SafeCare Level:</span>
                        <span class="badge bg-info">${result.safecare_level || 'N/A'}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="assessment-label">SafeCare Score:</span>
                        <span class="badge bg-primary">${result.safecare_score || 'N/A'}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="assessment-label">Last Assessment:</span>
                        <span class="badge bg-success">${result.last_assessment ? new Date(result.last_assessment).toLocaleDateString() : 'N/A'}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="assessment-label">Assessor:</span>
                        <span class="badge bg-dark">${result.assessor_name || 'Unknown'}</span>
                    </div>
                </div>
            </div>`;
                } else {
                    html += `
            <div class="previous-assessment-info">
                <h6>Current Facility Information</h6>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <span class="assessment-label">District:</span>
                        <span class="badge bg-secondary">${selections.district_name}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="assessment-label">LGA:</span>
                        <span class="badge bg-secondary">${selections.lga_name}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="assessment-label">PHC:</span>
                        <span class="badge bg-secondary">${selections.phc_name}</span>
                    </div>
                    <div class="col-md-12">
                        <div class="alert alert-info mt-2">
                            <i class="fas fa-info-circle me-2"></i> No previous assessment data available for this facility.
                        </div>
                    </div>
                </div>
            </div>`;
                }

                html += `
        </div>
    </div>`;

                // For each section, create a separate card with its questions
                Object.keys(sectionGroups).forEach(function(section) {
                    html += `
    <div class="card mb-4 section-card" id="section-${section.replace(/\s+/g, '-').toLowerCase()}">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">${section}</h5>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="15%">Question Number</th>
                        <th width="50%">Description</th>
                        <th width="30%">Response</th>
                    </tr>
                </thead>
                <tbody>`;

                    // Add questions for this section
                    sectionGroups[section].forEach(function(question, index) {
                        html += `
            <tr class="question-row" id="question-${question.id}">
                <td>${index + 1}</td>
                <td>${question.question_no}</td>
                <td>${question.question_description}</td>
                <td>
                    <select class="form-select response-select" data-question-id="${question.id}" data-section="${section}">
                        <option value="">Select Response</option>
                        <option value="NC">NC - Non-Compliant</option>
                        <option value="PC">PC - Partially Compliant</option>
                        <option value="FC">FC - Fully Compliant</option>
                        <option value="NA">NA - Not Applicable</option>
                    </select>
                </td>
            </tr>`;
                    });

                    html += `
                </tbody>
            </table>
        </div>
    </div>`;
                });

                html += `</div>`;

                // Append the assessment to the main content
                $('.main-content').append(html);

                // Add event handlers
                $('#save-assessment').on('click', function() {
                    saveAssessment(questions, selections);
                });

                $('.response-select').on('change', function() {
                    $(this).removeClass('is-invalid');
                    $(this).parent().find('.question-error').remove();
                });
            }

            function getResponseText(code) {
                switch (code) {
                    case 'NC':
                        return 'NC - Non-Compliant';
                    case 'PC':
                        return 'PC - Partially Compliant';
                    case 'FC':
                        return 'FC - Fully Compliant';
                    case 'NA':
                        return 'NA - Not Applicable';
                    default:
                        return code;
                }
            }

            // Function to save assessment with improved validation
            function saveAssessment(questions, selections) {
                console.log("Save button clicked");

                // Clear any previous error highlights
                $('.response-select').removeClass('is-invalid');
                $('.question-row').removeClass('question-highlight');
                $('.section-card').removeClass('highlighted-section');
                $('.question-error').remove();
                $('#validation-status').hide().empty();

                var responses = [];
                var emptyResponses = [];
                var sectionsMissing = {};

                // Collect all responses and track empty ones
                $('.response-select').each(function() {
                    var questionId = $(this).data('question-id');
                    var section = $(this).data('section');
                    var response = $(this).val();

                    if (!response) {
                        emptyResponses.push($(this));
                        // Track which sections have missing responses
                        if (!sectionsMissing[section]) {
                            sectionsMissing[section] = 1;
                        } else {
                            sectionsMissing[section]++;
                        }
                    } else {
                        responses.push({
                            question_id: questionId,
                            response: response
                        });
                    }
                });

                console.log("Collected responses:", responses);
                console.log("Empty responses:", emptyResponses.length);

                if (emptyResponses.length > 0) {
                    var summaryHtml = `
                <div id="validation-summary" class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-triangle"></i> Please complete all questions</h5>
                    <p>You have ${emptyResponses.length} unanswered question(s) in the following sections:</p>
                    <ul>
            `;

                    // Add sections with missing responses to summary
                    Object.keys(sectionsMissing).forEach(function(section) {
                        var sectionId = section.replace(/\s+/g, '-').toLowerCase();
                        summaryHtml +=
                            `<li><a class="section-link" data-section="${sectionId}">${section} (${sectionsMissing[section]} question(s))</a></li>`;

                        // Highlight the section card
                        $(`#section-${sectionId}`).addClass('highlighted-section');
                    });

                    summaryHtml += `
                    </ul>
                </div>
            `;

                    // Show validation summary
                    $('#validation-status').html(summaryHtml).show();

                    // Add click handler for section links
                    $('.section-link').on('click', function() {
                        var sectionId = $(this).data('section');
                        scrollToSection(sectionId);
                    });

                    // Add highlighting to all empty responses
                    emptyResponses.forEach(function(element) {
                        element.addClass('is-invalid');
                        element.parent().append(
                            '<div class="question-error"><i class="fas fa-exclamation-circle"></i> This question requires a response</div>'
                        );

                        // Highlight the row
                        element.closest('tr').addClass('question-highlight');
                    });

                    // Scroll to the validation summary
                    $('html, body').animate({
                        scrollTop: $('#validation-status').offset().top - 100
                    }, 500);

                    return; // Stop the function here
                }
                // Show saving indicator
                $('#save-assessment').html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                ).prop('disabled', true);

                // AJAX request to save assessment
                $.ajax({
                    url: '/save-safecare-assessment',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        district_id: selections.district_id,
                        lga_id: selections.lga_id,
                        phc_id: selections.phc_id,
                        responses: responses
                    },
                    success: function(response) {
                        console.log("Save success:", response);
                        // Show success message
                        $('#assessment-container').replaceWith(`
                    <div class="alert alert-success mt-4">
                        <h4 class="alert-heading">SafeCare Assessment successfully completed!</h4>
                        <p>Your quality improvement assessment has been saved.</p>
                        <hr>
                        <button id="new-assessment" class="btn btn-primary mt-2">Start New Assessment</button>
                    </div>
                `);

                        // Handle new assessment button
                        $('#new-assessment').on('click', function() {
                            // Clear localStorage
                            localStorage.removeItem('qip_selections');

                            // Refresh the page
                            window.location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        // Show detailed error message
                        $('#save-assessment').html('Save Assessment').prop('disabled', false);
                        console.error("Status:", status);
                        console.error("Error:", error);
                        console.error("Response Text:", xhr.responseText);

                        try {
                            var jsonResponse = JSON.parse(xhr.responseText);
                            alert('Error saving assessment: ' + jsonResponse.message);
                        } catch (e) {
                            alert('Error saving assessment. Please check the console for details.');
                        }
                    }
                });
            }

            // Helper function to scroll to a specific section
            function scrollToSection(sectionId) {
                var section = $(`#section-${sectionId}`);

                // Find the first unanswered question in this section
                var firstEmptyInSection = section.find('.is-invalid').first();

                if (firstEmptyInSection.length) {
                    // Scroll to the first empty question in this section
                    $('html, body').animate({
                        scrollTop: firstEmptyInSection.closest('tr').offset().top - 100
                    }, 500);

                    setTimeout(function() {
                        firstEmptyInSection.focus();
                    }, 600);
                } else {
                    $('html, body').animate({
                        scrollTop: section.offset().top - 100
                    }, 500);
                }
            }
        });
    </script>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
