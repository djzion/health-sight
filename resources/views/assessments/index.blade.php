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
            padding-top: 60px;
            /* Add padding for sticky header */
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
            z-index: 1030;
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
            padding-top: 80px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
            width: calc(100% - 250px) !important;
        }

        .steps-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            width: 100%;
        }

        .step {
            flex: 0 1 auto;
            min-width: 80px;
            max-width: 120px;
            margin: 5px;
        }

        .validation-error {
            border: 2px solid #dc3545 !important;
            background-color: rgba(220, 53, 69, 0.05);
            padding: 10px;
            border-radius: 5px;
        }

        style.textContent=` .border-danger {
            border: 2px solid #dc3545 !important;
        }

        .section-validation-error ul {
            margin-top: 10px;
            margin-bottom: 0;
        }

        .section-validation-error li {
            margin-bottom: 5px;
        }

        @keyframes flash {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .flash-error {
            animation: flash 0.5s 3;
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

        .btn-secondary {
            background-color: #6c757d !important;
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

        select {
            width: 30%;
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

        .label {
            display: block;
            font-weight: 500;
        }

        .formp {
            display: flex;
        }

        .formp input {
            width: 30%;
        }

        /* New styles for pagination */
        .section-page {
            display: none;
        }

        .section-page.active {
            display: block;
        }

        .progress {
            height: 8px;
            margin-bottom: 20px;
        }

        .navigation-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        /* Location banner - improved based on screenshots */
        .location-banner {
            position: fixed;
            top: 0;
            left: 250px;
            /* Align with sidebar width */
            right: 0;
            z-index: 1020;
            padding: 15px 20px;
            background-color: white;
            border-bottom: 2px solid #0199dc;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: left 0.3s ease;
        }

        .location-info {
            display: flex;
            align-items: center;
        }

        .location-info-text {
            margin-left: 10px;
        }

        .location-label {
            font-weight: normal;
            margin-right: 5px;
        }

        .location-value {
            color: #0199dc;
            font-weight: 600;
        }

        .location-map-icon {
            color: #0199dc;
            font-size: 24px;
            background-color: #e6f5ff;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-change-location {
            background-color: #0199dc;
            color: white;
            font-weight: 500;
            border: none;
            border-radius: 4px;
            padding: 8px 16px;
            display: flex;
            align-items: center;
        }

        .btn-change-location i {
            margin-right: 8px;
        }

        .btn-change-location:hover {
            background-color: #0181b8;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 60px;
                width: calc(100% - 60px) !important;
            }

            .location-banner {
                width: calc(100% - 60px);
            }
        }

        /* Enhanced Step indicator */
        .step-indicator {
            display: flex;
            width: 100%;
            position: relative;
            justify-content: center;
            align-items: center !important;
            margin-bottom: 30px;
            padding: 0 15px;
            overflow-x: visible !important;
            display: block;
            /* Allow horizontal scrolling for many steps */
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: #0199dc #f1f5f9;
        }

        .step-indicator::-webkit-scrollbar {
            height: 6px;
        }

        .step-indicator::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .step-indicator::-webkit-scrollbar-thumb {
            background-color: #0199dc;
            border-radius: 10px;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            margin: 0 20px;
            min-width: 100px;
            /* Ensure minimum width for each step */
            cursor: pointer;
        }

        .step-number {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 10px;
            font-weight: bold;
            color: #6c757d;
            transition: all 0.3s;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border: 2px solid #fff;
        }

        .step.active .step-number {
            background-color: #0199dc;
            color: white;
            transform: scale(1.1);
            box-shadow: 0 3px 8px rgba(1, 153, 220, 0.4);
        }

        .step.completed .step-number {
            background-color: #28a745;
            color: white;
        }

        .step.completed .step-number:after {
            content: '✓';
            font-weight: bold;
        }

        .step-title {
            font-size: 0.9rem;
            color: #6c757d;
            text-align: center;
            max-width: 120px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: all 0.3s;
        }

        .step.active .step-title {
            color: #0199dc;
            font-weight: 600;
            transform: scale(1.05);
        }

        .step-connector {
            position: absolute;
            top: 18px;
            height: 3px;
            background-color: #e9ecef;
            z-index: -1;
        }

        .step-connector.left {
            right: 50%;
            width: 80px;
            margin-right: 25px;
        }

        .step-connector.right {
            left: 50%;
            width: 80px;
            margin-left: 25px;
        }

        .step.completed .step-connector.left,
        .step.active .step-connector.left,
        .step-1 .step-connector.right {
            background-color: #28a745;
        }


        .save-progress-btn {
            margin-right: auto;
            /* This pushes the next/prev buttons to the right */
            border-color: #0199dc;
            color: #0199dc;
            transition: all 0.2s;
        }

        .save-progress-btn:hover {
            background-color: #e6f7ff;
            border-color: #0199dc;
            color: #0199dc;
        }

        .navigation-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            padding: 0 5px;
        }

        /* Toast styles */
        .toast {
            background-color: white;
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            max-width: 350px;
        }

        .toast-header {
            border-bottom: none;
        }

        .toast-body {
            padding: 0.75rem;
        }

        /* Resume banner styles */
        .resume-btn {
            background-color: #0199dc;
            border-color: #0199dc;
        }

        .resume-btn:hover {
            background-color: #0181b8;
            border-color: #0181b8;
        }
    </style>
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
            <a href="{{ route('qip.index') }}"><i class="fas fa-file-medical-alt me-2"></i> Quality Improvement</a>
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
            <!-- Location Banner styled like screenshot -->
            @if (session('assessment_location_selected'))
                <div class="location-banner">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="location-info">
                            <div class="location-map-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="location-info-text">
                                <strong>Currently Assessing:</strong>
                                <div class="mt-1">
                                    <span class="me-2">District: <span
                                            class="location-value">{{ $district }}</span></span>
                                    <span class="me-2">LGA: <span
                                            class="location-value">{{ $lga }}</span></span>
                                    <span>PHC: <span class="location-value">{{ $phc }}</span></span>
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('assessments.reset-location') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-change-location">
                                <i class="fas fa-exchange-alt"></i> Change Location
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <div class="container py-4" style="margin-top: 20px;">
                <div id="form-errors" class="alert alert-danger" style="display: none;"></div>

                <!-- Hidden input to track if this is an update or new submission -->
                <input type="hidden" id="form-action"
                    value="{{ isset($existingResponses) && count($existingResponses) > 0 ? 'update' : 'store' }}">

                @if (isset($isNewAssessmentPeriod) && $isNewAssessmentPeriod)
                    <div class="alert alert-info mb-4">
                        <div class="d-flex align-items-center">
                            <div>
                                <strong>New Assessment Period</strong>
                                <p class="mb-0">A new assessment period is available for this PHC</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="assessment-progress-container">
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 30%;" aria-valuenow="30"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress-sections">
                        <!-- These will be generated dynamically via JavaScript -->
                    </div>
                </div>

                <div class="step-indicator mb-4" id="step-indicator">
                </div>

                <div class="step-indicator mb-4" id="step-indicator">
                </div>

                <!-- Form with paginated sections -->
                <form id="assessment-form" action="{{ route('assessments.store') }}" method="POST" class="mb-5">
                    @csrf

                    @if (isset($isNewAssessmentPeriod) && $isNewAssessmentPeriod)
                        <input type="hidden" name="is_new_period" value="1">
                    @endif

                    @php
                        $globalCounter = 1;
                        $processedQuestions = [];
                        $sectionCount = $sections->count();
                        $currentSection = 1;
                    @endphp

                    <!-- Sections Container -->
                    <div id="sections-container">
                        @foreach ($sections as $section)
                            @php
                                $sectionQuestions = $assessments
                                    ->where('assessment_section_id', $section->id)
                                    ->sortBy('order');
                                if ($sectionQuestions->isEmpty()) {
                                    continue;
                                }
                            @endphp

                            <!-- Section Page -->
                            <div class="section-page {{ $currentSection == 1 ? 'active' : '' }}"
                                data-section-id="{{ $section->id }}" id="section-{{ $section->id }}">
                                <!-- Section Card -->
                                <div class="card mb-4 shadow-sm border-0 rounded">
                                    <!-- Section Header -->
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">{{ $section->section_name }}</h5>
                                    </div>

                                    <!-- Section Body with enhanced padding -->
                                    <div class="card-body p-4">
                                        @php $sectionProcessed = []; @endphp

                                        @foreach ($sectionQuestions as $assessment)
                                            @php
                                                $questionKey = $assessment->question . '_' . $assessment->response_type;
                                                if (in_array($questionKey, $sectionProcessed)) {
                                                    continue;
                                                }
                                                $sectionProcessed[] = $questionKey;
                                            @endphp

                                            <!-- Individual Question with left padding and improved spacing -->
                                            <div class="mb-4 pb-4 border-bottom" style="padding-left: 20px;">
                                                <label class="form-label fw-bold text-dark mb-3">
                                                    <span class="me-2">{{ $globalCounter }}.</span>
                                                    {{ $assessment->question }}
                                                </label>

                                                <!-- Input Controls -->
                                                @switch($assessment->response_type)
                                                    @case('text')
                                                        <textarea name="responses[{{ $assessment->id }}]" rows="3" class="form-control assessment-response"
                                                            data-assessment-id="{{ $assessment->id }}" placeholder="Enter your response here...">{{ $existingResponses[$assessment->id]->response ?? '' }}</textarea>
                                                    @break

                                                    @case('int')
                                                        <input type="number" class="form-input assessment-response"
                                                            data-assessment-id="{{ $assessment->id }}"
                                                            name="responses[{{ $assessment->id }}]"
                                                            value="{{ $existingResponses[$assessment->id]->response ?? '' }}">
                                                    @break

                                                    @case('form')
                                                        <div class="formp">

                                                            <div class="formea">
                                                                <label class="label" for="full-time">Full Time</label>
                                                                <input type="number" id="full-time"
                                                                    class="form-input assessment-response"
                                                                    data-assessment-id="{{ $assessment->id }}"
                                                                    name="responses[{{ $assessment->id }}]"
                                                                    value="{{ $existingResponses[$assessment->id]->response ?? '' }}">
                                                            </div>
                                                            <div class="formea">
                                                                <label class="label" for="cont">Contract</label>
                                                                <input type="number" id="cont"
                                                                    class="form-input assessment-response"
                                                                    data-assessment-id="{{ $assessment->id }}"
                                                                    name="responses[{{ $assessment->id }}]"
                                                                    value="{{ $existingResponses[$assessment->id]->response ?? '' }}">
                                                            </div>
                                                            <div class="formea">
                                                                <label class="label" for="nysc">NYSC/INTERN</label>
                                                                <input type="number" id="nysc"
                                                                    class="form-input assessment-response"
                                                                    data-assessment-id="{{ $assessment->id }}"
                                                                    name="responses[{{ $assessment->id }}]"
                                                                    value="{{ $existingResponses[$assessment->id]->response ?? '' }}">
                                                            </div>
                                                        </div>
                                                    @break

                                                    @case('select-multiple-services')
                                                        @php
                                                            $serviceOptions = [
                                                                'Inpatient Care',
                                                                'Maternity',
                                                                'Family Planning',
                                                                'Immunization',
                                                                'Laboratory',
                                                                'HIV Services',
                                                                'TB Services',
                                                                'Oral Health',
                                                                'Community Mental Health',
                                                                'Primary Eye Care',
                                                            ];
                                                            $existingValues = [];
                                                            if (isset($existingResponses[$assessment->id])) {
                                                                // Convert existing response to array if it's stored as JSON or comma-separated
    if (
        is_string(
            $existingResponses[$assessment->id]->response,
        )
    ) {
        if (
            strpos(
                $existingResponses[$assessment->id]
                    ->response,
                '[',
            ) === 0
        ) {
            // Looks like JSON
            $existingValues =
                json_decode(
                    $existingResponses[$assessment->id]
                        ->response,
                    true,
                ) ?? [];
        } else {
            // Assume comma-separated
            $existingValues = explode(
                ',',
                                                                            $existingResponses[$assessment->id]
                                                                                ->response,
                                                                        );
                                                                    }
                                                                } elseif (
                                                                    is_array(
                                                                        $existingResponses[$assessment->id]->response,
                                                                    )
                                                                ) {
                                                                    $existingValues =
                                                                        $existingResponses[$assessment->id]->response;
                                                                }
                                                            }
                                                        @endphp
                                                        <div class="checkbox-group">
                                                            <div class="row">
                                                                @foreach ($serviceOptions as $option)
                                                                    <div class="col-md-6 col-lg-4 mb-2">
                                                                        <div class="form-check">
                                                                            <input type="checkbox"
                                                                                class="form-check-input assessment-response"
                                                                                data-assessment-id="{{ $assessment->id }}"
                                                                                id="service-{{ $assessment->id }}-{{ $loop->index }}"
                                                                                name="responses[{{ $assessment->id }}][]"
                                                                                value="{{ $option }}"
                                                                                {{ in_array($option, $existingValues) ? 'checked' : '' }}>
                                                                            <label class="form-check-label"
                                                                                for="service-{{ $assessment->id }}-{{ $loop->index }}">
                                                                                {{ $option }}
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <div class="form-text mt-1">
                                                                Select all services that apply
                                                            </div>
                                                        </div>
                                                    @break

                                                    @case('select')
                                                        @php
                                                            $serviceOptions = $assessment->options;
                                                            $existingValues = [];
                                                            if (isset($existingResponses[$assessment->id])) {
                                                                // Convert existing response to array if it's stored as JSON or comma-separated
    if (
        is_string(
            $existingResponses[$assessment->id]->response,
        )
    ) {
        if (
            strpos(
                $existingResponses[$assessment->id]
                    ->response,
                '[',
            ) === 0
        ) {
            // Looks like JSON
            $existingValues =
                json_decode(
                    $existingResponses[$assessment->id]
                        ->response,
                    true,
                ) ?? [];
        } else {
            // Assume comma-separated
            $existingValues = explode(
                ',',
                                                                            $existingResponses[$assessment->id]
                                                                                ->response,
                                                                        );
                                                                    }
                                                                } elseif (
                                                                    is_array(
                                                                        $existingResponses[$assessment->id]->response,
                                                                    )
                                                                ) {
                                                                    $existingValues =
                                                                        $existingResponses[$assessment->id]->response;
                                                                }
                                                            }
                                                        @endphp

                                                        <select id="service-{{ $assessment->id }}-{{ $loop->index }}"
                                                            class="assessment-response"
                                                            data-assessment-id="{{ $assessment->id }}"
                                                            name="responses[{{ $assessment->id }}][]" required>
                                                            <option value="" disabled selected>Select One</option>

                                                            @foreach ($serviceOptions as $option)
                                                                <option value="{{ $option }}"> {{ $option }}
                                                                </option>
                                                            @endforeach

                                                        </select>
                                                    @break

                                                    @case('select-multiple-pharmacy')
                                                        @php
                                                            $pharmacyOptions = [
                                                                'Tab/Syr Paracetamol',
                                                                'Tab/Syr Ibuprofen',
                                                                'Tab Ferrous',
                                                                'Tab Folic Acid',
                                                                'Tab Vitamin Bco',
                                                                'Tab Mutivitamin',
                                                                'Tab/Syr Vitamin C',
                                                                'Tab Calcium',
                                                                'Tab/Susp Metronidazole',
                                                                'Tab/Syr Chlorpheniramine',
                                                                'Tab Albendazole',
                                                                'Inj Gentamycin',
                                                                '⁠Inj Artesunate',
                                                                'Artemisinin based Combination Therapy',
                                                                'Tab Sulphadoxine+ Pyrimethamine',
                                                                'Oral Rehydration Salt +Zinc',
                                                                'Tab/Susp Cotrimoxazole',
                                                                'Cap/Susp Amoxicillin',
                                                            ];
                                                            $existingValues = [];
                                                            if (isset($existingResponses[$assessment->id])) {
                                                                // Convert existing response to array if it's stored as JSON or comma-separated
    if (
        is_string(
            $existingResponses[$assessment->id]->response,
        )
    ) {
        if (
            strpos(
                $existingResponses[$assessment->id]
                    ->response,
                '[',
            ) === 0
        ) {
            // Looks like JSON
            $existingValues =
                json_decode(
                    $existingResponses[$assessment->id]
                        ->response,
                    true,
                ) ?? [];
        } else {
            // Assume comma-separated
            $existingValues = explode(
                ',',
                                                                            $existingResponses[$assessment->id]
                                                                                ->response,
                                                                        );
                                                                    }
                                                                } elseif (
                                                                    is_array(
                                                                        $existingResponses[$assessment->id]->response,
                                                                    )
                                                                ) {
                                                                    $existingValues =
                                                                        $existingResponses[$assessment->id]->response;
                                                                }
                                                            }
                                                        @endphp
                                                        <div class="checkbox-group">
                                                            <div class="row">
                                                                @foreach ($pharmacyOptions as $option)
                                                                    <div class="col-md-6 col-lg-4 mb-2">
                                                                        <div class="form-check">
                                                                            <input type="checkbox"
                                                                                class="form-check-input assessment-response"
                                                                                data-assessment-id="{{ $assessment->id }}"
                                                                                id="pharmacy-{{ $assessment->id }}-{{ $loop->index }}"
                                                                                name="responses[{{ $assessment->id }}][]"
                                                                                value="{{ $option }}"
                                                                                {{ in_array($option, $existingValues) ? 'checked' : '' }}>
                                                                            <label class="form-check-label"
                                                                                for="pharmacy-{{ $assessment->id }}-{{ $loop->index }}">
                                                                                {{ $option }}
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <div class="form-text mt-1">
                                                                Select all that apply
                                                            </div>
                                                        </div>
                                                    @break

                                                    @case('select-multiple-emergency')
                                                        @php
                                                            $emergencyOptions = [
                                                                'Inj Diazepam',
                                                                'Inj Adrenaline',
                                                                'Inj Hydrocortisone',
                                                                'Inj Lidocaine',
                                                                'Inj Paracetamol',
                                                                'Water for injection',
                                                                '2cc/5cc Needle and syringe',
                                                                'Normal Saline',
                                                                '50% Dextrose water',
                                                                'Cannula',
                                                                'Infusion giving set',
                                                            ];
                                                            $existingValues = [];
                                                            if (isset($existingResponses[$assessment->id])) {
                                                                // Convert existing response to array if it's stored as JSON or comma-separated
    if (
        is_string(
            $existingResponses[$assessment->id]->response,
        )
    ) {
        if (
            strpos(
                $existingResponses[$assessment->id]
                    ->response,
                '[',
            ) === 0
        ) {
            // Looks like JSON
            $existingValues =
                json_decode(
                    $existingResponses[$assessment->id]
                        ->response,
                    true,
                ) ?? [];
        } else {
            // Assume comma-separated
            $existingValues = explode(
                ',',
                                                                            $existingResponses[$assessment->id]
                                                                                ->response,
                                                                        );
                                                                    }
                                                                } elseif (
                                                                    is_array(
                                                                        $existingResponses[$assessment->id]->response,
                                                                    )
                                                                ) {
                                                                    $existingValues =
                                                                        $existingResponses[$assessment->id]->response;
                                                                }
                                                            }
                                                        @endphp
                                                        <div class="checkbox-group">
                                                            <div class="row">
                                                                @foreach ($emergencyOptions as $option)
                                                                    <div class="col-md-6 col-lg-4 mb-2">
                                                                        <div class="form-check">
                                                                            <input type="checkbox"
                                                                                class="form-check-input assessment-response"
                                                                                data-assessment-id="{{ $assessment->id }}"
                                                                                id="emergency-{{ $assessment->id }}-{{ $loop->index }}"
                                                                                name="responses[{{ $assessment->id }}][]"
                                                                                value="{{ $option }}"
                                                                                {{ in_array($option, $existingValues) ? 'checked' : '' }}>
                                                                            <label class="form-check-label"
                                                                                for="emergency-{{ $assessment->id }}-{{ $loop->index }}">
                                                                                {{ $option }}
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <div class="form-text mt-1">
                                                                Select all that apply
                                                            </div>
                                                        </div>
                                                    @break

                                                    @case('yes_no')
                                                        <div class="d-flex gap-3 mt-2">
                                                            @foreach (['yes' => 'Yes', 'no' => 'No', 'n/a' => 'Not Applicable'] as $value => $label)
                                                                <div class="form-check">
                                                                    <input type="radio"
                                                                        class="form-check-input assessment-response"
                                                                        data-assessment-id="{{ $assessment->id }}"
                                                                        name="responses[{{ $assessment->id }}]"
                                                                        value="{{ $value }}"
                                                                        {{ isset($existingResponses[$assessment->id]) && $existingResponses[$assessment->id]->response === $value ? 'checked' : '' }}
                                                                        onchange="toggleChildQuestions(this, '{{ $assessment->id }}', '{{ $value }}')">
                                                                    <label
                                                                        class="form-check-label">{{ $label }}</label>
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                        <div id="childQuestions-{{ $assessment->id }}"
                                                            class="child-questions mt-3" style="display: none;">
                                                            <!-- Child questions will be loaded here dynamically -->
                                                        </div>
                                                    @break

                                                    @case('good_bad')
                                                        <div class="d-flex gap-3 mt-2">
                                                            @foreach (['good' => 'Good', 'bad' => 'Bad'] as $value => $label)
                                                                <div class="form-check">
                                                                    <input type="radio"
                                                                        class="form-check-input assessment-response"
                                                                        data-assessment-id="{{ $assessment->id }}"
                                                                        name="responses[{{ $assessment->id }}]"
                                                                        value="{{ $value }}"
                                                                        {{ isset($existingResponses[$assessment->id]) && $existingResponses[$assessment->id]->response === $value ? 'checked' : '' }}>
                                                                    <label
                                                                        class="form-check-label">{{ $label }}</label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @break
                                                @endswitch

                                                <!-- Conditional Input -->
                                                @if (isset($assessment->conditional_logic))
                                                    <div class="mt-3 d-none conditional-input"
                                                        data-parent="{{ $assessment->id }}">
                                                        <textarea name="responses[{{ $assessment->id }}][additional]" rows="2" class="form-control"
                                                            placeholder="Please provide additional details...">{{ $existingResponses[$assessment->id]->comments ?? '' }}</textarea>
                                                    </div>
                                                @endif
                                            </div>

                                            @php $globalCounter++; @endphp
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Navigation Buttons -->
                                <div class="navigation-buttons">
                                    @if ($currentSection > 1)
                                        <button type="button" class="btn btn-secondary prev-btn"
                                            data-section="{{ $section->id }}">
                                            <i class="fas fa-arrow-left me-2"></i> Previous
                                        </button>
                                    @else
                                        <div></div> <!-- Empty div for flex spacing -->
                                    @endif

                                    @if ($currentSection < $sectionCount)
                                        <button type="button" class="btn btn-primary next-btn"
                                            data-section="{{ $section->id }}">
                                            Next <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    @else
                                        <button type="button" id="submit-btn" class="btn btn-primary px-5 py-2">
                                            Submit Assessment
                                        </button>
                                    @endif
                                </div>
                            </div>

                            @php $currentSection++; @endphp
                        @endforeach
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- jQuery -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Director Modal (conditionally included) -->
    @if (auth()->user()->role->name === 'director' &&
            isset($showDirectorModal) &&
            $showDirectorModal &&
            !session('assessment_location_selected'))
        @include('components.director-phc-modal')
    @endif

    <script>
        let currentSectionIndex = 0;
        let goToSection; // Forward declaration for function we'll define in first listener

        document.addEventListener('DOMContentLoaded', function() {
            @if (auth()->user()->role->name === 'director' && !session('assessment_location_selected'))
                var modalElement = document.getElementById('phcSelectionModal');
                if (modalElement) {
                    var phcModal = new bootstrap.Modal(modalElement);
                    phcModal.show();
                }
            @endif

            // Set up pagination and progress tracking
            const sections = document.querySelectorAll('.section-page');
            const totalSections = sections.length;
            const style = document.createElement('style');
            document.head.appendChild(style);

            // Initialize currentSectionIndex (now defined globally)
            currentSectionIndex = 0;

            // Create step indicators
            createStepIndicators(totalSections);
            updateProgressBar();

            // Set up navigation button event listeners
            document.querySelectorAll('.next-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    moveToNextSection();
                });
            });

            document.querySelectorAll('.prev-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    moveToPreviousSection();
                });
            });

            // Function to create step indicators - horizontal tab style
            function createStepIndicators(totalSteps) {
                const stepIndicator = document.getElementById('step-indicator');
                stepIndicator.innerHTML = '';

                // Get section names
                const sectionNames = [];
                sections.forEach(section => {
                    const headerElement = section.querySelector('.card-header h5');
                    sectionNames.push(headerElement ? headerElement.textContent :
                        `Section ${sectionNames.length + 1}`);
                });

                // Create step indicators - use flex layout with wrap
                const stepsContainer = document.createElement('div');
                stepsContainer.className = 'steps-container d-flex flex-wrap justify-content-center';

                for (let i = 0; i < totalSteps; i++) {
                    const step = document.createElement('div');
                    step.className = `step ${i === 0 ? 'active' : ''}`;
                    step.dataset.index = i;

                    const stepNumber = document.createElement('div');
                    stepNumber.className = 'step-number';
                    stepNumber.textContent = i + 1;

                    const stepTitle = document.createElement('div');
                    stepTitle.className = 'step-title';
                    stepTitle.textContent = sectionNames[i];

                    step.appendChild(stepNumber);
                    step.appendChild(stepTitle);
                    stepsContainer.appendChild(step);

                    // Add click event to jump to step
                    step.addEventListener('click', function() {
                        goToSection(parseInt(this.dataset.index));
                    });
                }

                stepIndicator.appendChild(stepsContainer);
            }


            function validateCurrentSection() {
                const currentSection = document.querySelectorAll('.section-page')[currentSectionIndex];

                // Variables to track validation status
                let isValid = true;
                let firstInvalidElement = null;
                let errorMessages = [];

                // Check all radio button groups in current section
                const radioGroups = new Map();

                // Find all radio button groups in the current section
                currentSection.querySelectorAll('input[type="radio"]').forEach(radio => {
                    const name = radio.getAttribute('name');
                    if (!radioGroups.has(name)) {
                        radioGroups.set(name, {
                            elements: [],
                            question: radio.closest('.mb-4').querySelector('.form-label')
                                .textContent.trim(),
                            checked: false
                        });
                    }

                    radioGroups.get(name).elements.push(radio);
                    if (radio.checked) {
                        radioGroups.get(name).checked = true;
                    }
                });

                // Check if at least one option is selected for each radio group
                radioGroups.forEach((group, name) => {
                    if (!group.checked) {
                        isValid = false;

                        // Add to error messages
                        errorMessages.push(`Please answer: ${group.question}`);

                        // Set first invalid element for scrolling
                        if (!firstInvalidElement) {
                            firstInvalidElement = group.elements[0];
                        }

                        // Mark the container as invalid
                        const container = group.elements[0].closest('.mb-4');
                        container.classList.add('border-danger', 'p-2', 'rounded');
                    } else {
                        // Remove validation styling
                        const container = group.elements[0].closest('.mb-4');
                        container.classList.remove('border-danger', 'p-2', 'rounded');
                    }
                });

                // Check required selects
                currentSection.querySelectorAll('select[required]').forEach(select => {
                    if (!select.value) {
                        isValid = false;

                        const label = select.closest('.mb-4').querySelector('.form-label').textContent
                            .trim();
                        errorMessages.push(`Please select an option for: ${label}`);

                        if (!firstInvalidElement) {
                            firstInvalidElement = select;
                        }

                        select.classList.add('border-danger');
                    } else {
                        select.classList.remove('border-danger');
                    }
                });

                // Show validation message if there are errors
                if (!isValid) {
                    // Check if there's already an error alert
                    let errorAlert = currentSection.querySelector('.section-validation-error');
                    if (!errorAlert) {
                        errorAlert = document.createElement('div');
                        errorAlert.className = 'alert alert-danger section-validation-error mb-4';

                        // Create error list
                        let errorHTML =
                            '<i class="fas fa-exclamation-circle me-2"></i>Please complete all questions in this section before proceeding:<ul>';
                        errorMessages.forEach(msg => {
                            errorHTML += `<li>${msg}</li>`;
                        });
                        errorHTML += '</ul>';

                        errorAlert.innerHTML = errorHTML;

                        // Insert at the top of the section
                        const cardHeader = currentSection.querySelector('.card-header');
                        cardHeader.parentNode.insertBefore(errorAlert, cardHeader.nextSibling);
                    } else {
                        // Update existing error message
                        let errorHTML =
                            '<i class="fas fa-exclamation-circle me-2"></i>Please complete all questions in this section before proceeding:<ul>';
                        errorMessages.forEach(msg => {
                            errorHTML += `<li>${msg}</li>`;
                        });
                        errorHTML += '</ul>';

                        errorAlert.innerHTML = errorHTML;
                    }

                    // Scroll to first invalid element
                    if (firstInvalidElement) {
                        firstInvalidElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                } else {
                    // Remove error alert if validation passes
                    const errorAlert = currentSection.querySelector('.section-validation-error');
                    if (errorAlert) {
                        errorAlert.remove();
                    }

                    // Remove any validation styling
                    currentSection.querySelectorAll('.border-danger').forEach(el => {
                        el.classList.remove('border-danger', 'p-2', 'rounded');
                    });
                }

                return isValid;
            }

            // Function to update the progress bar and step indicators
            function updateProgressBar() {
                const progressPercentage = ((currentSectionIndex + 1) / totalSections) * 100;
                const progressBar = document.querySelector('.progress-bar');

                progressBar.style.width = `${progressPercentage}%`;
                progressBar.setAttribute('aria-valuenow', progressPercentage);

                // Update step indicators
                const stepIndicators = document.querySelectorAll('.step');
                stepIndicators.forEach((step, index) => {
                    if (index < currentSectionIndex) {
                        step.className = 'step completed';
                    } else if (index === currentSectionIndex) {
                        step.className = 'step active';
                    } else {
                        step.className = 'step';
                    }
                });
            }

            // Function to move to the next section
            function moveToNextSection() {
                // First validate the current section
                if (!validateCurrentSection()) {
                    return; // Stop navigation if validation fails
                }

                // saveTemporaryResponses();

                if (currentSectionIndex < totalSections - 1) {
                    // Hide current section
                    sections[currentSectionIndex].classList.remove('active');
                    // Show next section
                    sections[++currentSectionIndex].classList.add('active');
                    // Update progress
                    updateProgressBar();
                    // Scroll to top of the form
                    document.querySelector('#assessment-form').scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            }


            // Function to move to the previous section
            function moveToPreviousSection() {
                // saveTemporaryResponses();
                if (currentSectionIndex > 0) {
                    // Hide current section
                    sections[currentSectionIndex].classList.remove('active');
                    // Show previous section
                    sections[--currentSectionIndex].classList.add('active');
                    // Update progress
                    updateProgressBar();
                    // Scroll to top of the form
                    document.querySelector('#assessment-form').scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            }

            // Function to go to a specific section - define as global function
            goToSection = function(index) {
                if (index < currentSectionIndex || validateCurrentSection()) {
                    sections[currentSectionIndex].classList.remove('active');
                    currentSectionIndex = index;
                    sections[currentSectionIndex].classList.add('active');
                    if (index >= 0 && index < totalSections) {
                        // Update progress
                        updateProgressBar();
                        // Scroll to top of the form
                        document.querySelector('#assessment-form').scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                }
            }

            // Function to toggle child questions based on parent response
            function toggleChildQuestions(element, assessmentId, value) {
                const childQuestionsContainer = document.getElementById(`childQuestions-${assessmentId}`);

                if (element.checked) {
                    // Show loading indicator
                    childQuestionsContainer.innerHTML =
                        '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
                    childQuestionsContainer.style.display = 'block';

                    // Fetch child questions via AJAX with the selected option
                    fetch(`/assessments/${assessmentId}/child-questions?selected_option=${value}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.childQuestions && data.childQuestions.length > 0) {
                                let html = '<div class="ps-4 border-start border-3 border-primary">';
                                data.childQuestions.forEach(question => {
                                    html += `
                    <div class="mb-3">
                        <label class="form-label fw-semibold">${question.question || 'N/A'}</label>
                        <!-- Render appropriate input based on question type -->
                        ${renderQuestionInput(question)}
                    </div>
                    `;
                                });
                                html += '</div>';
                                childQuestionsContainer.innerHTML = html;
                            } else {
                                // No child questions found for this condition
                                childQuestionsContainer.style.display = 'none';
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching child questions:', error);
                            childQuestionsContainer.innerHTML =
                                '<div class="alert alert-danger">Failed to load additional questions</div>';
                        });
                } else {
                    // Hide child questions when the radio button is unchecked
                    childQuestionsContainer.style.display = 'none';
                }
            }

            // Expose the function to global scope so it can be called from inline event handlers
            window.toggleChildQuestions = toggleChildQuestions;
        });

        function renderQuestionInput(question) {
            // Render different input types based on question.response_type
            switch (question.response_type) {
                case 'text':
                    return `<input type="text" class="form-control assessment-response" data-assessment-id="${question.id}"
            name="responses[${question.id}]" value="${question.existing_response || ''}">`;
                case 'textarea':
                    return `
        <textarea class="form-control assessment-response" data-assessment-id="${question.id}"
            name="responses[${question.id}]" rows="3">${question.existing_response || ''}</textarea>`;
                case 'yes_no':
                    return `
        <div class="d-flex gap-3 mt-2">
            <div class="form-check">
                <input type="radio" class="form-check-input assessment-response" data-assessment-id="${question.id}"
                    name="responses[${question.id}]" value="yes" ${question.existing_response === 'yes' ? 'checked' : '' }
                    onchange="toggleChildQuestions(this, '${question.id}', 'yes')">
                <label class="form-check-label">Yes</label>
            </div>
            <div class="form-check">
                <input type="radio" class="form-check-input assessment-response" data-assessment-id="${question.id}"
                    name="responses[${question.id}]" value="no" ${question.existing_response === 'no' ? 'checked' : '' }
                    onchange="toggleChildQuestions(this, '${question.id}', 'no')">
                <label class="form-check-label">No</label>
            </div>
            <div class="form-check">
                <input type="radio" class="form-check-input assessment-response" data-assessment-id="${question.id}"
                    name="responses[${question.id}]" value="n/a" ${question.existing_response === 'n/a' ? 'checked' : '' }
                    onchange="toggleChildQuestions(this, '${question.id}', 'n/a')">
                <label class="form-check-label">Not Applicable</label>
            </div>
        </div>
        <div id="childQuestions-${question.id}" class="child-questions mt-3" style="display: none;"></div>
        `;
                case 'multiple_choice':
                    if (!question.options || !Array.isArray(question.options)) {
                        return `<div class="alert alert-warning">No options available</div>`;
                    }
                    let optionsHtml = '';
                    question.options.forEach(option => {
                        optionsHtml += `
            <div class="form-check">
                <input type="radio" class="form-check-input assessment-response" data-assessment-id="${question.id}"
                    name="responses[${question.id}]" value="${option}" ${question.existing_response === option ? 'checked' : '' }>
                <label class="form-check-label">${option}</label>
            </div>
            `;
                    });
                    return optionsHtml;
                case 'checkbox':
                    if (!question.options || !Array.isArray(question.options)) {
                        return `<div class="alert alert-warning">No options available</div>`;
                    }
                    let checkboxHtml = '';
                    const existingResponses = question.existing_response ? question.existing_response.split(',') : [];
                    question.options.forEach(option => {
                        checkboxHtml += `
            <div class="form-check">
                <input type="checkbox" class="form-check-input assessment-response" data-assessment-id="${question.id}"
                    name="responses[${question.id}][]" value="${option}" ${existingResponses.includes(option) ? 'checked' : '' }>
                <label class="form-check-label">${option}</label>
            </div>
            `;
                    });
                    return checkboxHtml;
                case 'date':
                    return `<input type="date" class="form-control assessment-response" data-assessment-id="${question.id}"
            name="responses[${question.id}]" value="${question.existing_response || ''}">`;
                case 'number':
                    return `<input type="number" class="form-control assessment-response" data-assessment-id="${question.id}"
            name="responses[${question.id}]" value="${question.existing_response || ''}">`;
                case 'file':
                    let filePreview = '';
                    if (question.existing_response) {
                        filePreview = `
            <div class="mt-2">
                <small>Current file: <a href="${question.existing_response}" target="_blank">${getFileName(question.existing_response)}</a></small>
            </div>
            `;
                    }
                    return `
        <input type="file" class="form-control assessment-response" data-assessment-id="${question.id}"
            name="file_responses[${question.id}]">
        ${filePreview}
        `;
                default:
                    return `<input type="text" class="form-control assessment-response" data-assessment-id="${question.id}"
            name="responses[${question.id}]" value="${question.existing_response || ''}">`;
            }
        }

        // Helper function to extract file name from path
        function getFileName(path) {
            return path.split('/').pop();
        }

        /**
         * Submit assessment form via AJAX
         */
        function submitAssessmentForm(isUpdate = false) {
            // Collect form data
            const responses = {};

            // Handle text inputs, selects, textareas
            $('.assessment-response').each(function() {
                const assessmentId = $(this).data('assessment-id');

                // Skip if we've already processed this question
                if (responses[assessmentId]) return;

                // Different handling based on input type
                if ($(this).is(':radio') || $(this).is(':checkbox')) {
                    // For radio buttons, only collect if checked
                    if ($(this).is(':checked')) {
                        responses[assessmentId] = $(this).val();
                    }
                } else {
                    // For other inputs (text, select, etc.)
                    const response = $(this).val();
                    if (response) {
                        responses[assessmentId] = response;
                    }
                }
            });

            // Now we need to do a second pass to collect checkbox groups
            // which may have multiple selected values
            $('input[type="checkbox"]:checked').each(function() {
                const assessmentId = $(this).data('assessment-id');
                // If this is part of a checkbox group
                if ($(this).attr('name').includes('[]')) {
                    if (!responses[assessmentId]) {
                        responses[assessmentId] = [];
                    }
                    if (Array.isArray(responses[assessmentId])) {
                        responses[assessmentId].push($(this).val());
                    } else {
                        // Convert to array if it wasn't already
                        responses[assessmentId] = [responses[assessmentId], $(this).val()];
                    }
                }
            });

            console.log('Submitting responses:', responses);

            // Set the correct method and URL
            const method = isUpdate ? 'PUT' : 'POST';
            const url = '/assessments';

            // Show loading state
            $('#submit-btn').html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                )
                .prop('disabled', true);

            // AJAX request to save assessment
            $.ajax({
                url: url,
                type: method,
                dataType: 'json',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    _method: method, // For Laravel to recognize PUT method
                    responses: responses
                },
                success: function(response) {
                    if (response.success) {
                        // Normal success case
                        $('#assessment-form').replaceWith(`
            <div class="alert alert-success mt-4">
                <h4 class="alert-heading">Success!
                <h4 class="alert-heading">Success!</h4>
                <p>${response.message}</p>
            </div>
            `);

                        // Redirect after showing the message
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 2000);
                    } else if (response.create_new) {
                        // Special case: Outside editable window but open for new submissions
                        $('#assessment-form').replaceWith(`
            <div class="alert alert-info mt-4">
                <h4 class="alert-heading">Notice</h4>
                <p>${response.message}</p>
                <hr>
                <p class="mb-0">Redirecting you to submit a new assessment...</p>
            </div>
            `);

                        // Redirect to create new assessment
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 2000);
                    } else {
                        // Other non-success case
                        $('#assessment-form').replaceWith(`
            <div class="alert alert-info mt-4">
                <h4 class="alert-heading">Notice</h4>
                <p>${response.message}</p>
            </div>
            `);

                        // Redirect
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 2000);
                    }
                },
                error: function(xhr, status, error) {
                    // Restore button state
                    $('#submit-btn').html('Submit Assessment').prop('disabled', false);

                    // Show error message
                    let errorMessage =
                        'An error occurred while saving your assessment. Please try again.';

                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        console.error('Error parsing error response:', e);
                    }

                    $('#form-errors').html(`
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> ${errorMessage}
        </div>
        `).show();

                    // Scroll to error message
                    $('html, body').animate({
                        scrollTop: $('#form-errors').offset().top - 100
                    }, 200);
                }
            });
        }

        // Add event handler for submit button
        $(document).ready(function() {
            $('#submit-btn').on('click', function(e) {
                e.preventDefault();
                // Determine if this is an update or new submission
                const isUpdate = $('#form-action').val() === 'update';
                submitAssessmentForm(isUpdate);
            });
        });
    </script>

    <!-- Debug information - only include modal if location not selected -->
    @if (
        (auth()->user()->role->name === 'director' || auth()->user()->role->name === 'Director') &&
            !session('assessment_location_selected'))
        <div class="alert alert-info">Debug: Director role detected (No location selected)</div>
        @include('components.director-phc-modal')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                console.log('Director detected, attempting to show modal');
                var modalElement = document.getElementById('phcSelectionModal');
                console.log('Modal element:', modalElement);
                if (modalElement) {
                    var myModal = new bootstrap.Modal(modalElement);
                    console.log('Modal created, showing now');
                    myModal.show();
                } else {
                    console.error('Modal element not found!');
                }
            });
        </script>
    @elseif (auth()->user()->role->name === 'director' || auth()->user()->role->name === 'Director')
        <div class="alert alert-info">Debug: Director role detected (Location already selected:
            {{ $district ?? 'Unknown' }} / {{ $lga ?? 'Unknown' }} / {{ $phc ?? 'Unknown' }})</div>
    @else
        <div class="alert alert-info">Debug: Not a director ({{ auth()->user()->role->name }})</div>
    @endif

    <script>
        // Global fix for missing Question #48
        document.addEventListener('DOMContentLoaded', function() {
            // Override the submit button click
            $('#submit-btn').off('click').on('click', function(e) {
                e.preventDefault();

                // Force add question #48 first
                let hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'responses[48]';
                hiddenInput.value = 'yes';
                document.querySelector('form').appendChild(hiddenInput);

                console.log('Added hidden field for question #48');

                // Now proceed with normal form submission
                const isUpdate = $('#form-action').val() === 'update';
                submitAssessmentForm(isUpdate);
            });

            const assessmentForm = document.getElementById('assessment-form');
            const phcId = "{{ session('assessment_phc_id') ?? '' }}"; // Get current PHC ID from session

            // Load any existing temporary responses when the page loads
            loadTemporaryResponses();

            // Set up autosave on page navigation
            document.querySelectorAll('.next-btn, .prev-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    // Save responses before navigating
                    saveTemporaryResponses();
                });
            });

            // Also add autosave on step indicator clicks
            document.addEventListener('click', function(e) {
                if (e.target.closest('.step')) {
                    saveTemporaryResponses();
                }
            });

            // Add a "Save Progress" button to each page
            document.querySelectorAll('.navigation-buttons').forEach(navButtons => {
                const saveBtn = document.createElement('button');
                saveBtn.type = 'button';
                saveBtn.className = 'btn btn-outline-primary save-progress-btn';
                saveBtn.innerHTML = '<i class="fas fa-save me-2"></i> Save Progress';
                saveBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    saveTemporaryResponses(true); // true = show confirmation
                });

                // Insert before the next/submit button
                const nextBtn = navButtons.querySelector('.next-btn, #submit-btn');
                if (nextBtn) {
                    navButtons.insertBefore(saveBtn, nextBtn);
                } else {
                    navButtons.appendChild(saveBtn);
                }
            });



            /**
             * Save responses temporarily in the database
             */
            function saveTemporaryResponses(showConfirmation = false) {
                if (!phcId) {
                    console.warn('No PHC ID found. Temporary save is not available.');
                    return;
                }

                const responses = collectFormResponses();

                const currentPage = currentSectionIndex;
                console.log('Current section for saving:', currentPage);

                // Create the data to be sent
                const saveData = {
                    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    phc_id: phcId,
                    responses: responses,
                    current_page: currentPage
                };

                // Show a saving indicator if requested
                let saveIndicator = null;
                if (showConfirmation) {
                    saveIndicator = document.createElement('div');
                    saveIndicator.className = 'position-fixed top-0 end-0 p-3';
                    saveIndicator.style.zIndex = '9999';
                    saveIndicator.innerHTML = `
            <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-primary text-white">
                    <i class="fas fa-save me-2"></i>
                    <strong class="me-auto">Saving Progress</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    <div class="d-flex align-items-center">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                            <span class="visually-hidden">Saving...</span>
                        </div>
                        <span>Saving your progress...</span>
                    </div>
                </div>
            </div>
        `;
                    document.body.appendChild(saveIndicator);
                }

                // Send the AJAX request to save temporary data
                $.ajax({
                    url: '/assessments/save-temporary',
                    type: 'POST',
                    dataType: 'json',
                    data: saveData,
                    success: function(response) {
                        if (showConfirmation) {
                            // Update the toast to show success
                            if (saveIndicator) {
                                const toastBody = saveIndicator.querySelector('.toast-body');
                                if (toastBody) {
                                    toastBody.innerHTML = `
                            <div class="d-flex align-items-center text-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <span>Progress saved successfully!</span>
                            </div>
                        `;

                                    // Remove the toast after a delay
                                    setTimeout(function() {
                                        saveIndicator.remove();
                                    }, 3000);
                                }
                            }

                            console.log('Progress saved successfully');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error saving temporary responses:', error);
                        console.error('Response:', xhr.responseText);

                        if (showConfirmation && saveIndicator) {
                            const toastBody = saveIndicator.querySelector('.toast-body');
                            if (toastBody) {
                                toastBody.innerHTML = `
                        <div class="d-flex align-items-center text-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <span>Failed to save progress. Please try again.</span>
                        </div>
                    `;

                                // Remove the toast after a delay
                                setTimeout(function() {
                                    saveIndicator.remove();
                                }, 3000);
                            }
                        }
                    }
                });
            }

            /**
             * Load temporary responses from the database
             */
            function loadTemporaryResponses() {
                if (!phcId) {
                    return;
                }

                $.ajax({
                    url: `/assessments/load-temporary/${phcId}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.data) {
                            // Display resume banner if there's saved data
                            showResumeBanner(response.data.current_page || 0);

                            // Fill the form with the saved responses
                            if (response.data.responses) {
                                fillFormWithResponses(response.data.responses);
                            }

                            console.log('Temporary responses loaded successfully');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading temporary responses:', error);
                    }
                });
            }

            /**
             * Show resume banner with option to continue from where they left off
             */
            function showResumeBanner(savedPage) {
                if (savedPage <= 0) return;

                const banner = document.createElement('div');
                banner.className = 'alert alert-info alert-dismissible fade show mb-4';
                banner.innerHTML = `
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-info-circle me-2"></i>
                <strong>Resume Assessment</strong>
                <p class="mb-0">You have a partially completed assessment. Would you like to resume from where you left off?</p>
            </div>
            <div>
                <button type="button" class="btn btn-sm btn-primary resume-btn me-2">
                    <i class="fas fa-play me-1"></i> Resume
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="alert">
                    Start Over
                </button>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

                // Insert the banner at the top of the form
                const container = document.querySelector('.container.py-4');
                const firstElement = container.firstChild;
                container.insertBefore(banner, firstElement);

                // Add event listener to resume button
                banner.querySelector('.resume-btn').addEventListener('click', function() {
                    goToSection(savedPage);
                    banner.remove();
                });
            }

            /**
             * Collect all form responses
             */
            function collectFormResponses() {
                const responses = {};

                // Handle text inputs, selects, textareas
                $('.assessment-response').each(function() {
                    const assessmentId = $(this).data('assessment-id');

                    // Skip if we've already processed this question
                    if (responses[assessmentId]) return;

                    // Different handling based on input type
                    if ($(this).is(':radio') || $(this).is(':checkbox')) {
                        // For radio buttons, only collect if checked
                        if ($(this).is(':checked')) {
                            responses[assessmentId] = $(this).val();
                        }
                    } else {
                        // For other inputs (text, select, etc.)
                        const response = $(this).val();
                        if (response) {
                            responses[assessmentId] = response;
                        }
                    }
                });

                // Now collect checkbox groups which may have multiple selected values
                $('input[type="checkbox"]:checked').each(function() {
                    const assessmentId = $(this).data('assessment-id');
                    // If this is part of a checkbox group
                    if ($(this).attr('name').includes('[]')) {
                        if (!responses[assessmentId]) {
                            responses[assessmentId] = [];
                        }
                        if (Array.isArray(responses[assessmentId])) {
                            responses[assessmentId].push($(this).val());
                        } else {
                            // Convert to array if it wasn't already
                            responses[assessmentId] = [responses[assessmentId], $(this).val()];
                        }
                    }
                });

                return responses;
            }

            /**
             * Fill the form with saved responses
             */

            function fillFormWithResponses(responses) {
                // Process each saved response
                Object.keys(responses).forEach(assessmentId => {
                    const value = responses[assessmentId];

                    // Find all elements for this assessment ID
                    const elements = document.querySelectorAll(
                        `.assessment-response[data-assessment-id="${assessmentId}"]`);

                    elements.forEach(element => {
                        if (element.type === 'radio') {
                            // For radio buttons, check if value matches
                            if (element.value === value) {
                                element.checked = true;

                                // Trigger change event for any conditional logic
                                if (element.hasAttribute('onchange')) {
                                    const event = new Event('change');
                                    element.dispatchEvent(event);
                                }
                            }
                        } else if (element.type === 'checkbox') {
                            // For checkboxes, check if value is in the array
                            if (Array.isArray(value) && value.includes(element.value)) {
                                element.checked = true;
                            } else if (value === element.value) {
                                element.checked = true;
                            }
                        } else {
                            // For text inputs, selects, and textareas
                            element.value = value;
                        }
                    });
                });
            }
        });
    </script>
</body>

</html>
