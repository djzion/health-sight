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

        .border-danger {
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

        .form-check-input:checked {
            background-color: #0199dc;
            border-color: #0199dc;
        }

        .form-select,
        select {
            min-width: 200px;
            max-width: 400px;
            width: auto;
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
            cursor: pointer;
        }

        .label {
            display: block;
            font-weight: 500;
        }

        .formp {
            display: flex;
            gap: 20px;
        }

        .formea {
            flex: 1;
        }

        .formp input {
            width: 100%;
        }

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

        .location-banner {
            position: fixed;
            top: 0;
            left: 250px;
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

        .save-progress-btn {
            margin-right: auto;
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

        .resume-btn {
            background-color: #0199dc;
            border-color: #0199dc;
        }

        .resume-btn:hover {
            background-color: #0181b8;
            border-color: #0181b8;
        }

        @media (max-width: 768px) {
            .container {
                max-width: 100% !important;
                margin-right: 0 !important;
                padding-left: 10px !important;
                padding-right: 10px !important;
            }

            .main-content {
                width: calc(100% - 60px) !important;
                padding: 15px !important;
                padding-top: 70px !important;
            }

            .location-banner {
                left: 60px;
                width: calc(100% - 60px);
                padding: 10px;
            }

            .location-info-text {
                font-size: 0.9rem;
            }

            .form-input,
            select {
                width: 100% !important;
                max-width: 100%;
            }

            .card-body {
                padding: 15px !important;
            }

            .border-bottom {
                padding-left: 5px !important;
            }

            .formp {
                flex-direction: column;
            }

            .formp .formea {
                width: 100%;
                margin-bottom: 10px;
            }

            .formp input {
                width: 100% !important;
            }
        }

        @media (max-width: 576px) {
            .step-indicator {
                overflow-x: auto !important;
            }

            .steps-container {
                flex-wrap: nowrap !important;
                justify-content: flex-start !important;
                min-width: max-content !important;
            }

            .step {
                min-width: 80px !important;
                flex: 0 0 auto !important;
            }

            .step-title {
                font-size: 0.8rem;
            }

            .location-banner {
                padding: 8px;
            }

            .location-info-text {
                font-size: 0.8rem;
            }

            .location-map-icon {
                width: 30px;
                height: 30px;
                font-size: 16px;
            }

            .btn-change-location {
                padding: 5px 10px;
                font-size: 0.9rem;
            }

            .navigation-buttons {
                padding: 0;
            }

            .navigation-buttons .btn {
                padding: 8px 12px;
                font-size: 0.9rem;
            }

            .form-check-label {
                font-size: 0.9rem;
            }

            .checkbox-group .row {
                margin-left: -5px;
                margin-right: -5px;
            }

            .checkbox-group .col-md-6 {
                padding-left: 5px;
                padding-right: 5px;
            }
        }

        @media (max-width: 768px) {
            .step-indicator:nth-of-type(2) {
                display: none !important;
            }

            .checkbox-group .col-md-6,
            .checkbox-group .col-lg-4 {
                width: 100%;
            }

            .section-validation-error {
                font-size: 0.9rem;
            }

            .toast {
                max-width: 90%;
            }
        }

        @media (max-width: 576px) {
            .mb-4.pb-4.border-bottom {
                margin-bottom: 1rem !important;
                padding-bottom: 1rem !important;
            }
        }

        @media (max-width: 400px) {
            .d-flex.gap-3.mt-2 {
                flex-direction: column;
                gap: 0.5rem !important;
            }
        }

        .staff-count-container {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-top: 0.5rem;
        }

        .staff-count-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0.75rem;
        }

        .staff-count-row:last-child {
            margin-bottom: 0;
        }

        .staff-count-label {
            font-weight: 500;
            min-width: 120px;
            color: #495057;
        }

        .staff-count-input {
            width: 80px;
            padding: 0.375rem 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            font-size: 0.875rem;
        }

        .required-field::after {
            content: " *";
            color: #dc3545;
        }

        .section-progress {
            background-color: #e9ecef;
            border-radius: 0.25rem;
            padding: 0.5rem;
            margin-bottom: 1rem;
        }

        .validation-summary {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        /* Enhanced error message styling */
        .alert.border-0 {
            border: none !important;
        }

        .alert .badge {
            font-size: 0.75rem;
            font-weight: 500;
        }

        .alert .card {
            box-shadow: none;
        }

        .alert .card-header {
            font-weight: 600;
        }

        .unanswered-questions-list {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            background-color: #fff;
        }

        .unanswered-questions-list ul {
            margin-bottom: 0;
        }

        .unanswered-questions-list .badge {
            min-width: 40px;
            text-align: center;
        }

        .section-group {
            margin-bottom: 1.5rem;
        }

        .section-group:last-child {
            margin-bottom: 0;
        }

        .question-item {
            padding: 0.5rem;
            border-left: 3px solid #ffc107;
            background-color: #fffbf0;
            margin-bottom: 0.5rem;
            border-radius: 0 0.25rem 0.25rem 0;
        }

        .question-item:last-child {
            margin-bottom: 0;
        }

        /* Improved loading states */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        .alert .spinner-border {
            color: inherit;
        }

        /* Better button spacing in alerts */
        .alert .btn+.btn {
            margin-left: 0.5rem;
        }

        /* Enhanced alert icons */
        .alert .fa-2x {
            font-size: 1.75rem;
        }

        .alert-heading .fas {
            margin-right: 0.5rem;
        }

        /* Responsive improvements for error messages */
        @media (max-width: 768px) {
            .alert .d-flex {
                flex-direction: column;
            }

            .alert .flex-shrink-0 {
                flex-shrink: 1;
                margin-bottom: 1rem;
                text-align: center;
            }

            .alert .flex-grow-1 {
                margin-left: 0 !important;
            }

            .unanswered-questions-list {
                max-height: 200px;
                font-size: 0.9rem;
            }

            .alert .btn {
                width: 100%;
                margin-bottom: 0.5rem;
                margin-left: 0 !important;
            }
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
            <a href="#" id="qip-modal-trigger"><i class="fas fa-file-medical-alt me-2"></i> Quality Improvement</a>
            <a href="#"><i class="fas fa-tasks me-2"></i> Tasks</a>
            <a href="#" id="reports-trigger"><i class="fas fa-chart-line me-2"></i> Reports</a>
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
            <!-- Location Banner -->
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

                                                    @case('year')
                                                        @php
                                                            $currentYear = date('Y');
                                                            $startYear = 1960;
                                                            $selectedYear =
                                                                $existingResponses[$assessment->id]->response ?? '';
                                                        @endphp

                                                        <div style="margin: 15px 0;">
                                                            <select name="responses[{{ $assessment->id }}]"
                                                                class="form-control assessment-response"
                                                                data-assessment-id="{{ $assessment->id }}"
                                                                data-validation-type="year"
                                                                style="width: 200px; height: 40px; font-size: 16px; border: 2px solid #0199dc;"
                                                                required>

                                                                <option value="">Select Year</option>
                                                                @for ($year = $currentYear; $year >= $startYear; $year--)
                                                                    <option value="{{ $year }}"
                                                                        {{ $selectedYear == $year ? 'selected' : '' }}>
                                                                        {{ $year }}
                                                                    </option>
                                                                @endfor
                                                            </select>
                                                        </div>

                                                        <div class="invalid-feedback"></div>
                                                    @break

                                                    @case('form')
                                                        @php
                                                            $existingData = [];
                                                            if (isset($existingResponses[$assessment->id])) {
                                                                $response =
                                                                    $existingResponses[$assessment->id]->response;
                                                                if (is_string($response)) {
                                                                    $existingData = json_decode($response, true) ?? [];
                                                                } elseif (is_array($response)) {
                                                                    $existingData = $response;
                                                                }
                                                            }
                                                        @endphp
                                                        <div class="formp" data-assessment-id="{{ $assessment->id }}">
                                                            <div class="formea">
                                                                <label class="label"
                                                                    for="full-time-{{ $assessment->id }}">Full Time</label>
                                                                <input type="number" id="full-time-{{ $assessment->id }}"
                                                                    class="form-input assessment-response"
                                                                    data-assessment-id="{{ $assessment->id }}"
                                                                    data-type="full_time"
                                                                    name="staff_responses[{{ $assessment->id }}][full_time]"
                                                                    value="{{ $existingData['full_time'] ?? '' }}">
                                                            </div>
                                                            <div class="formea">
                                                                <label class="label"
                                                                    for="contract-{{ $assessment->id }}">Contract</label>
                                                                <input type="number" id="contract-{{ $assessment->id }}"
                                                                    class="form-input assessment-response"
                                                                    data-assessment-id="{{ $assessment->id }}"
                                                                    data-type="contract"
                                                                    name="staff_responses[{{ $assessment->id }}][contract]"
                                                                    value="{{ $existingData['contract'] ?? '' }}">
                                                            </div>
                                                            <div class="formea">
                                                                <label class="label"
                                                                    for="nysc-{{ $assessment->id }}">NYSC/INTERN</label>
                                                                <input type="number" id="nysc-{{ $assessment->id }}"
                                                                    class="form-input assessment-response"
                                                                    data-assessment-id="{{ $assessment->id }}"
                                                                    data-type="nysc_intern"
                                                                    name="staff_responses[{{ $assessment->id }}][nysc_intern]"
                                                                    value="{{ $existingData['nysc_intern'] ?? '' }}">
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
                                                                $response =
                                                                    $existingResponses[$assessment->id]->response;
                                                                if (is_string($response)) {
                                                                    if (strpos($response, '[') === 0) {
                                                                        $existingValues =
                                                                            json_decode($response, true) ?? [];
                                                                    } else {
                                                                        $existingValues = explode(',', $response);
                                                                    }
                                                                } elseif (is_array($response)) {
                                                                    $existingValues = $response;
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
                                                                $response =
                                                                    $existingResponses[$assessment->id]->response;
                                                                if (is_string($response)) {
                                                                    if (strpos($response, '[') === 0) {
                                                                        $existingValues =
                                                                            json_decode($response, true) ?? [];
                                                                    } else {
                                                                        $existingValues = explode(',', $response);
                                                                    }
                                                                } elseif (is_array($response)) {
                                                                    $existingValues = $response;
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
                                                                $response =
                                                                    $existingResponses[$assessment->id]->response;
                                                                if (is_string($response)) {
                                                                    if (strpos($response, '[') === 0) {
                                                                        $existingValues =
                                                                            json_decode($response, true) ?? [];
                                                                    } else {
                                                                        $existingValues = explode(',', $response);
                                                                    }
                                                                } elseif (is_array($response)) {
                                                                    $existingValues = $response;
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
                                                                $response =
                                                                    $existingResponses[$assessment->id]->response;
                                                                if (is_string($response)) {
                                                                    if (strpos($response, '[') === 0) {
                                                                        $existingValues =
                                                                            json_decode($response, true) ?? [];
                                                                    } else {
                                                                        $existingValues = explode(',', $response);
                                                                    }
                                                                } elseif (is_array($response)) {
                                                                    $existingValues = $response;
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Director Modal -->
    @if (auth()->user()->role->name === 'director' &&
            isset($showDirectorModal) &&
            $showDirectorModal &&
            !session('assessment_location_selected'))
        @include('components.director-phc-modal')
    @endif

    <script>
        // Global variables
        let currentSectionIndex = 0;
        let goToSection;
        let sections;
        let totalSections;

        // Validation rules from working version
        const ValidationRules = {
            text: (value) => {
                if (!value || value.trim() === '') return 'This field is required';
                if (value.length > 1000) return 'Response cannot exceed 1000 characters';
                return null;
            },
            int: (value) => {
                if (!value && value !== '0') return 'This field is required';
                const num = parseInt(value);
                if (isNaN(num) || num < 0) return 'Must be a non-negative number';
                return null;
            },
            year: (value) => {
                if (!value) return 'Please select a year';
                const year = parseInt(value);
                if (year < 1960 || year > new Date().getFullYear()) {
                    return `Must be between 1960 and ${new Date().getFullYear()}`;
                }
                return null;
            },
            yes_no: (value) => {
                if (!value) return 'Please select Yes, No, or Not Applicable';
                if (!['yes', 'no', 'n/a'].includes(value)) return 'Invalid selection';
                return null;
            },
            good_bad: (value) => {
                if (!value) return 'Please select Good or Bad';
                if (!['good', 'bad'].includes(value)) return 'Invalid selection';
                return null;
            },
            'select-multiple': (values) => {
                if (!values || values.length === 0) return 'Please select at least one option';
                return null;
            },
            form: (formData) => {
                if (!formData || typeof formData !== 'object') return 'Staff data is required';
                const hasValue = Object.values(formData).some(val => val !== '' && val !== null && val !==
                    undefined);
                if (!hasValue) return 'Please provide at least one staff count';

                for (const [key, value] of Object.entries(formData)) {
                    if (value !== '' && (isNaN(value) || parseInt(value) < 0)) {
                        return 'All staff counts must be non-negative numbers';
                    }
                }
                return null;
            }
        };

        // Global configuration
        const phcId = "{{ session('assessment_phc_id') ?? '' }}";

        // GLOBAL FUNCTIONS (available throughout the script)

        /**
         * Collect form responses - GLOBAL VERSION
         */
        function collectFormResponses() {
            const responses = {};
            const staffResponses = {};

            // Handle regular assessment responses (not staff counts)
            $('.assessment-response').not('.staff-count-input').each(function() {
                const assessmentId = $(this).data('assessment-id');

                if ($(this).is(':radio') && $(this).is(':checked')) {
                    responses[assessmentId] = $(this).val();
                } else if ($(this).is(':checkbox') && $(this).is(':checked')) {
                    if (!responses[assessmentId]) {
                        responses[assessmentId] = [];
                    }
                    if ($(this).attr('name').includes('[]')) {
                        responses[assessmentId].push($(this).val());
                    }
                } else if ($(this).is('input[type="number"], input[type="text"], textarea, select') && $(this)
                .val()) {
                    if ($(this).is('input[type="number"]')) {
                        const value = parseInt($(this).val());
                        if (!isNaN(value) && value >= 0) {
                            responses[assessmentId] = value;
                        }
                    } else {
                        responses[assessmentId] = $(this).val();
                    }
                }
            });

            // Handle staff count inputs - allow zeros
            $('.formp').each(function() {
                const assessmentId = $(this).data('assessment-id');

                // Skip if no assessment ID is found
                if (!assessmentId || assessmentId === 'undefined') {
                    console.warn('Found staff form without assessment ID:', this);
                    return;
                }

                const fullTimeInput = $(this).find('input[data-type="full_time"]');
                const contractInput = $(this).find('input[data-type="contract"]');
                const nyscInput = $(this).find('input[data-type="nysc_intern"]');

                const fullTime = parseInt(fullTimeInput.val()) || 0;
                const contract = parseInt(contractInput.val()) || 0;
                const nyscIntern = parseInt(nyscInput.val()) || 0;

                const hasAnyInput = fullTimeInput.val() !== '' || contractInput.val() !== '' || nyscInput.val() !==
                    '';

                if (hasAnyInput) {
                    staffResponses[assessmentId] = {
                        full_time: fullTime,
                        contract: contract,
                        nysc_intern: nyscIntern
                    };
                }
            });

            // Also handle staff responses from name attribute (backup method)
            $('input[name*="staff_responses"]').each(function() {
                const name = $(this).attr('name');
                const matches = name.match(/staff_responses\[(\d+)\]\[(\w+)\]/);

                if (matches && $(this).val()) {
                    const assessmentId = matches[1];
                    const staffType = matches[2];
                    const value = parseInt($(this).val()) || 0;

                    // Skip if assessmentId is not a valid number
                    if (isNaN(parseInt(assessmentId))) {
                        console.warn('Invalid assessment ID in staff response:', assessmentId);
                        return;
                    }

                    if (!staffResponses[assessmentId]) {
                        staffResponses[assessmentId] = {};
                    }
                    staffResponses[assessmentId][staffType] = value;
                }
            });

            // Clean up any undefined keys
            if (staffResponses.hasOwnProperty('undefined')) {
                delete staffResponses['undefined'];
            }

            console.log('Collected responses:', responses);
            console.log('Collected staff responses:', staffResponses);

            return {
                responses,
                staffResponses
            };
        }

        /**
         * Submit assessment form - GLOBAL VERSION
         */
        function submitAssessmentForm(isUpdate = false) {
            console.log('submitAssessmentForm called, isUpdate:', isUpdate);

            try {
                const {
                    responses,
                    staffResponses
                } = collectFormResponses();

                // Fix URL and method for updates
                let method, url;
                if (isUpdate) {
                    // For updates, we need to get the assessment ID or use the form's action
                    const form = document.getElementById('assessment-form');
                    const formAction = form ? form.getAttribute('action') : null;

                    if (formAction && formAction !== '/assessments') {
                        // Use the form's action URL for updates
                        url = formAction;
                        method = 'PUT';
                    } else {
                        // Fallback: try to get assessment ID from somewhere else
                        const assessmentId = document.querySelector('input[name="assessment_id"]')?.value ||
                            document.querySelector('[data-assessment-id]')?.dataset.assessmentId ||
                            phcId; // Use PHC ID as fallback

                        url = assessmentId ? `/assessments/${assessmentId}` : '/assessments';
                        method = 'PUT';
                    }
                } else {
                    // For new submissions
                    url = '/assessments';
                    method = 'POST';
                }

                console.log('Using URL:', url, 'Method:', method);

                // Show loading state
                $('#submit-btn').html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...')
                    .prop('disabled', true);

                // AJAX request to save assessment
                $.ajax({
                    url: url,
                    type: 'POST', // Always use POST, let Laravel handle the method override
                    dataType: 'json',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        _method: method, // Laravel will use this for method override
                        responses: responses,
                        staff_responses: staffResponses
                    },
                    success: function(response) {
                        console.log('Success response:', response);

                        if (response.success) {
                            $('#assessment-form').replaceWith(`
                            <div class="alert alert-success mt-4">
                                <h4 class="alert-heading"><i class="fas fa-check-circle me-2"></i>Success!</h4>
                                <p>${response.message}</p>
                                <div class="mt-3">
                                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                    <small class="text-muted">Redirecting you back to the dashboard...</small>
                                </div>
                            </div>
                        `);
                            setTimeout(() => window.location.href = response.redirect, 2000);
                        } else if (response.create_new) {
                            $('#assessment-form').replaceWith(`
                            <div class="alert alert-info mt-4">
                                <h4 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Notice</h4>
                                <p>${response.message}</p>
                                <hr>
                                <div class="mt-3">
                                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                    <small class="text-muted">Redirecting you to submit a new assessment...</small>
                                </div>
                            </div>
                        `);
                            setTimeout(() => window.location.href = response.redirect, 2000);
                        } else {
                            $('#assessment-form').replaceWith(`
                            <div class="alert alert-info mt-4">
                                <h4 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Notice</h4>
                                <p>${response.message}</p>
                                <div class="mt-3">
                                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                    <small class="text-muted">Redirecting you back...</small>
                                </div>
                            </div>
                        `);
                            setTimeout(() => window.location.href = response.redirect, 2000);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        console.error('Response:', xhr.responseText);

                        // Restore button state
                        $('#submit-btn').html('Submit Assessment').prop('disabled', false);

                        let errorHTML = '';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.error_type === 'edit_window_expired' && response
                                .unanswered_questions) {
                                errorHTML = createUnansweredQuestionsError(response);
                            } else if (response.message) {
                                errorHTML = `
                                <div class="alert alert-danger">
                                    <h5 class="alert-heading"><i class="fas fa-exclamation-circle me-2"></i>Error</h5>
                                    <p class="mb-0">${response.message}</p>
                                </div>
                            `;
                            } else {
                                errorHTML = `
                                <div class="alert alert-danger">
                                    <h5 class="alert-heading"><i class="fas fa-exclamation-circle me-2"></i>Error</h5>
                                    <p class="mb-0">An error occurred while saving your assessment. Please try again.</p>
                                </div>
                            `;
                            }
                        } catch (e) {
                            console.error('Error parsing error response:', e);
                            errorHTML = `
                            <div class="alert alert-danger">
                                <h5 class="alert-heading"><i class="fas fa-exclamation-circle me-2"></i>Error</h5>
                                <p class="mb-0">An unexpected error occurred. Please try again.</p>
                            </div>
                        `;
                        }

                        $('#form-errors').html(errorHTML).show();
                        $('html, body').animate({
                            scrollTop: $('#form-errors').offset().top - 100
                        }, 200);
                    }
                });
            } catch (error) {
                console.error('Error in submitAssessmentForm:', error);
                alert('An error occurred. Please check the console for details.');
                $('#submit-btn').html('Submit Assessment').prop('disabled', false);
            }
        }

        /**
         * Create detailed error message for unanswered questions - GLOBAL VERSION
         */
        function createUnansweredQuestionsError(response) {
            const {
                unanswered_questions,
                unanswered_count,
                edit_window_closed,
                can_create_new
            } = response;

            let questionsHTML = '';
            let sectionGroups = {};

            // Group questions by section
            unanswered_questions.forEach(q => {
                if (!sectionGroups[q.section]) {
                    sectionGroups[q.section] = [];
                }
                sectionGroups[q.section].push(q);
            });

            // Build the questions list grouped by section
            Object.keys(sectionGroups).forEach(sectionName => {
                questionsHTML += `
                <div class="mb-3">
                    <h6 class="fw-bold text-primary mb-2">
                        <i class="fas fa-folder me-1"></i>${sectionName}
                    </h6>
                    <ul class="list-unstyled ps-3">
            `;

                sectionGroups[sectionName].forEach(q => {
                    const questionText = q.question.length > 80 ?
                        q.question.substring(0, 80) + '...' :
                        q.question;

                    questionsHTML += `
                    <li class="mb-2">
                        <span class="badge bg-warning text-dark me-2">#${q.question_number}</span>
                        <span class="text-muted">${questionText}</span>
                    </li>
                `;
                });

                questionsHTML += `
                    </ul>
                </div>
            `;
            });

            let actionButtons = '';
            if (can_create_new) {
                actionButtons = `
                <div class="mt-4 pt-3 border-top">
                    <p class="mb-3">
                        <i class="fas fa-lightbulb text-warning me-2"></i>
                        <strong>Good news!</strong> You can still submit a new assessment for this month.
                    </p>
                    <button type="button" class="btn btn-primary" onclick="window.location.href='${response.redirect}'">
                        <i class="fas fa-plus me-2"></i>Start New Assessment
                    </button>
                    <button type="button" class="btn btn-outline-secondary ms-2" onclick="window.location.href='${response.redirect}'">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </button>
                </div>
            `;
            } else {
                actionButtons = `
                <div class="mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-outline-primary" onclick="window.location.href='${response.redirect}'">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </button>
                </div>
            `;
            }

            return `
            <div class="alert alert-warning border-0 shadow-sm">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-warning fa-2x"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="alert-heading mb-3">Assessment Editing Period Has Expired</h5>
                        <div class="mb-3">
                            <p class="mb-2">
                                <strong>Edit window closed:</strong>
                                <span class="text-muted">${edit_window_closed}</span>
                            </p>
                            <p class="mb-0">
                                Unfortunately, you can no longer edit this assessment, and there are still
                                <strong class="text-danger">${unanswered_count} unanswered question${unanswered_count !== 1 ? 's' : ''}</strong>
                                that need to be completed.
                            </p>
                        </div>
                        <div class="card border-warning mb-3">
                            <div class="card-header bg-warning bg-opacity-10 border-warning">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Unanswered Questions (${unanswered_count})
                                </h6>
                            </div>
                            <div class="card-body">
                                <div style="max-height: 300px; overflow-y: auto;">
                                    ${questionsHTML}
                                </div>
                            </div>
                        </div>
                        ${actionButtons}
                    </div>
                </div>
            </div>
        `;
        }

        /**
         * Validation functions from working version
         */
        function validateElement(element) {
            const container = element.closest('.question-container') || element.closest('.mb-4');
            const assessmentId = element.dataset.assessmentId;
            const validationType = element.dataset.validationType ||
                element.closest('[data-validation-type]')?.dataset.validationType;

            let value;
            let errorMessage = null;

            // Clear previous validation state
            element.classList.remove('is-invalid');
            container.classList.remove('has-error', 'border-danger');
            const feedback = container.querySelector('.invalid-feedback');
            if (feedback) feedback.textContent = '';

            // Get value based on element type
            if (element.type === 'radio') {
                const radioGroup = container.querySelectorAll(`input[name="${element.name}"]`);
                value = Array.from(radioGroup).find(radio => radio.checked)?.value;
            } else if (element.type === 'checkbox') {
                const checkboxGroup = container.querySelectorAll(`input[name="${element.name}"]`);
                value = Array.from(checkboxGroup).filter(cb => cb.checked).map(cb => cb.value);
            } else if (validationType === 'form') {
                const formContainer = container.querySelector('.formp');
                if (formContainer) {
                    const fullTime = formContainer.querySelector('[data-type="full_time"]')?.value || '';
                    const contract = formContainer.querySelector('[data-type="contract"]')?.value || '';
                    const nyscIntern = formContainer.querySelector('[data-type="nysc_intern"]')?.value || '';
                    value = {
                        full_time: fullTime,
                        contract: contract,
                        nysc_intern: nyscIntern
                    };
                }
            } else {
                value = element.value;
            }

            // Validate using appropriate rule
            if (ValidationRules[validationType]) {
                errorMessage = ValidationRules[validationType](value);
            }

            // Display error if validation failed
            if (errorMessage) {
                element.classList.add('is-invalid');
                container.classList.add('has-error', 'border-danger');
                if (feedback) feedback.textContent = errorMessage;
                return false;
            }

            return true;
        }

        function validateCurrentSection() {
            const currentSection = sections[currentSectionIndex];
            let isValid = true;
            let firstInvalidElement = null;
            let errorMessages = [];

            // Clear previous validation states
            currentSection.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            currentSection.querySelectorAll('.has-error').forEach(el => {
                el.classList.remove('has-error', 'border-danger');
            });

            // Check all radio button groups in current section
            const radioGroups = new Map();

            currentSection.querySelectorAll('input[type="radio"]').forEach(radio => {
                const name = radio.getAttribute('name');
                if (!radioGroups.has(name)) {
                    radioGroups.set(name, {
                        elements: [],
                        question: radio.closest('.mb-4').querySelector('.form-label')?.textContent.trim() ||
                            'Unknown question',
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
                    errorMessages.push(`Please answer: ${group.question}`);

                    if (!firstInvalidElement) {
                        firstInvalidElement = group.elements[0];
                    }

                    const container = group.elements[0].closest('.mb-4');
                    container.classList.add('border-danger', 'p-2', 'rounded');
                } else {
                    const container = group.elements[0].closest('.mb-4');
                    container.classList.remove('border-danger', 'p-2', 'rounded');
                }
            });

            // Check required selects
            currentSection.querySelectorAll('select[required]').forEach(select => {
                if (!select.value) {
                    isValid = false;

                    const label = select.closest('.mb-4').querySelector('.form-label')?.textContent.trim() ||
                        'Unknown field';
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
                showValidationErrors(currentSection, errorMessages);
                if (firstInvalidElement) {
                    firstInvalidElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            } else {
                clearValidationErrors(currentSection);
            }

            return isValid;
        }

        function showValidationErrors(section, errorMessages) {
            let errorAlert = section.querySelector('.section-validation-error');
            if (!errorAlert) {
                errorAlert = document.createElement('div');
                errorAlert.className = 'alert alert-danger section-validation-error mb-4';
                const cardHeader = section.querySelector('.card-header');
                if (cardHeader) {
                    cardHeader.parentNode.insertBefore(errorAlert, cardHeader.nextSibling);
                } else {
                    section.insertBefore(errorAlert, section.firstChild);
                }
            }

            let errorHTML =
                '<i class="fas fa-exclamation-circle me-2"></i>Please complete all questions in this section before proceeding:<ul>';
            errorMessages.forEach(msg => {
                errorHTML += `<li>${msg}</li>`;
            });
            errorHTML += '</ul>';

            errorAlert.innerHTML = errorHTML;
            errorAlert.classList.add('flash-error');
            setTimeout(() => errorAlert.classList.remove('flash-error'), 1500);
        }

        function clearValidationErrors(section) {
            const errorAlert = section.querySelector('.section-validation-error');
            if (errorAlert) {
                errorAlert.remove();
            }
        }

        /**
         * Navigation functions
         */
        function updateProgressBar() {
            const progressPercentage = ((currentSectionIndex + 1) / totalSections) * 100;
            const progressBar = document.querySelector('.progress-bar');

            if (progressBar) {
                progressBar.style.width = `${progressPercentage}%`;
                progressBar.setAttribute('aria-valuenow', progressPercentage);
            }

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

        function moveToNextSection() {
            if (!validateCurrentSection()) {
                return;
            }

            saveTemporaryResponses();

            if (currentSectionIndex < totalSections - 1) {
                sections[currentSectionIndex].classList.remove('active');
                sections[++currentSectionIndex].classList.add('active');
                updateProgressBar();
                document.querySelector('#assessment-form')?.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        }

        function moveToPreviousSection() {
            saveTemporaryResponses();
            if (currentSectionIndex > 0) {
                sections[currentSectionIndex].classList.remove('active');
                sections[--currentSectionIndex].classList.add('active');
                updateProgressBar();
                document.querySelector('#assessment-form')?.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        }

        /**
         * Child questions functionality
         */
        function toggleChildQuestions(element, assessmentId, value) {
            const childQuestionsContainer = document.getElementById(`childQuestions-${assessmentId}`);

            if (element.checked) {
                childQuestionsContainer.innerHTML =
                    '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
                childQuestionsContainer.style.display = 'block';

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
                                    ${renderQuestionInput(question)}
                                </div>
                            `;
                            });
                            html += '</div>';
                            childQuestionsContainer.innerHTML = html;
                        } else {
                            childQuestionsContainer.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching child questions:', error);
                        childQuestionsContainer.innerHTML =
                            '<div class="alert alert-danger">Failed to load additional questions</div>';
                    });
            } else {
                childQuestionsContainer.style.display = 'none';
            }
        }

        function renderQuestionInput(question) {
            switch (question.response_type) {
                case 'text':
                    return `<input type="text" class="form-control assessment-response" data-assessment-id="${question.id}"
                    name="responses[${question.id}]" value="${question.existing_response || ''}">`;
                case 'textarea':
                    return `<textarea class="form-control assessment-response" data-assessment-id="${question.id}"
                    name="responses[${question.id}]" rows="3">${question.existing_response || ''}</textarea>`;
                case 'yes_no':
                    return `
                    <div class="d-flex gap-3 mt-2">
                        <div class="form-check">
                            <input type="radio" class="form-check-input assessment-response" data-assessment-id="${question.id}"
                                name="responses[${question.id}]" value="yes" ${question.existing_response === 'yes' ? 'checked' : ''}
                                onchange="toggleChildQuestions(this, '${question.id}', 'yes')">
                            <label class="form-check-label">Yes</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input assessment-response" data-assessment-id="${question.id}"
                                name="responses[${question.id}]" value="no" ${question.existing_response === 'no' ? 'checked' : ''}
                                onchange="toggleChildQuestions(this, '${question.id}', 'no')">
                            <label class="form-check-label">No</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input assessment-response" data-assessment-id="${question.id}"
                                name="responses[${question.id}]" value="n/a" ${question.existing_response === 'n/a' ? 'checked' : ''}
                                onchange="toggleChildQuestions(this, '${question.id}', 'n/a')">
                            <label class="form-check-label">Not Applicable</label>
                        </div>
                    </div>
                    <div id="childQuestions-${question.id}" class="child-questions mt-3" style="display: none;"></div>
                `;
                default:
                    return `<input type="text" class="form-control assessment-response" data-assessment-id="${question.id}"
                    name="responses[${question.id}]" value="${question.existing_response || ''}">`;
            }
        }

        /**
         * Temporary save functionality
         */
        function saveTemporaryResponses(showConfirmation = false) {
            if (!phcId) {
                console.warn('No PHC ID found. Temporary save is not available.');
                return;
            }

            const {
                responses,
                staffResponses
            } = collectFormResponses();
            const currentPage = currentSectionIndex;

            const saveData = {
                _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                phc_id: phcId,
                responses: responses,
                staff_responses: staffResponses,
                current_page: currentPage
            };

            $.ajax({
                url: '/assessments/save-temporary',
                type: 'POST',
                dataType: 'json',
                data: saveData,
                success: function(response) {
                    if (showConfirmation) {
                        console.log('Progress saved successfully');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error saving temporary responses:', error);
                }
            });
        }

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

        function fillFormWithResponses(responses) {
            Object.keys(responses).forEach(assessmentId => {
                const value = responses[assessmentId];
                const elements = document.querySelectorAll(
                    `.assessment-response[data-assessment-id="${assessmentId}"]`);

                elements.forEach(element => {
                    if (element.type === 'radio') {
                        if (element.value === value) {
                            element.checked = true;
                            const event = new Event('change');
                            element.dispatchEvent(event);
                        }
                    } else if (element.type === 'checkbox') {
                        if (Array.isArray(value) && value.includes(element.value)) {
                            element.checked = true;
                        } else if (value === element.value) {
                            element.checked = true;
                        }
                    } else {
                        element.value = value;
                    }
                });
            });
        }

        // Make functions globally available
        window.toggleChildQuestions = toggleChildQuestions;
        window.handleWaterSourceChange = function(selectElement, assessmentId) {
            const childContainer = document.getElementById(`child-question-${assessmentId}`);
            const childSelect = childContainer ? childContainer.querySelector('select') : null;

            if (childContainer) {
                if (selectElement.value === 'Borehole') {
                    childContainer.style.display = 'block';
                    if (childSelect) {
                        childSelect.setAttribute('required', 'required');
                    }
                } else {
                    childContainer.style.display = 'none';
                    if (childSelect) {
                        childSelect.removeAttribute('required');
                        childSelect.value = '';
                    }
                }
            }
        };

        // DOCUMENT READY INITIALIZATION
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== INITIALIZING ASSESSMENT FORM ===');

            @if (auth()->user()->role->name === 'director' && !session('assessment_location_selected'))
                var modalElement = document.getElementById('phcSelectionModal');
                if (modalElement) {
                    var phcModal = new bootstrap.Modal(modalElement);
                    phcModal.show();
                }
            @endif

            // Initialize global variables
            sections = document.querySelectorAll('.section-page');
            totalSections = sections.length;
            currentSectionIndex = 0;

            console.log('Sections found:', totalSections);
            console.log('Assessment responses found:', document.querySelectorAll('.assessment-response').length);

            if (totalSections === 0) {
                console.error('No sections found! Check your HTML structure.');
                return;
            }

            // Initialize components
            createStepIndicators(totalSections);
            updateProgressBar();
            setupRealTimeValidation();
            loadTemporaryResponses();

            // Set up navigation button event listeners
            document.querySelectorAll('.next-btn').forEach(btn => {
                btn.addEventListener('click', moveToNextSection);
            });

            document.querySelectorAll('.prev-btn').forEach(btn => {
                btn.addEventListener('click', moveToPreviousSection);
            });

            // Add "Save Progress" buttons
            document.querySelectorAll('.navigation-buttons').forEach(navButtons => {
                const saveBtn = document.createElement('button');
                saveBtn.type = 'button';
                saveBtn.className = 'btn btn-outline-primary save-progress-btn';
                saveBtn.innerHTML = '<i class="fas fa-save me-2"></i> Save Progress';
                saveBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    saveTemporaryResponses(true);
                });

                const nextBtn = navButtons.querySelector('.next-btn, #submit-btn');
                if (nextBtn) {
                    navButtons.insertBefore(saveBtn, nextBtn);
                } else {
                    navButtons.appendChild(saveBtn);
                }
            });

            // INTERNAL FUNCTIONS (inside DOMContentLoaded)
            function createStepIndicators(totalSteps) {
                const stepIndicator = document.getElementById('step-indicator');
                if (!stepIndicator) {
                    console.error('Step indicator element not found!');
                    return;
                }

                stepIndicator.innerHTML = '';

                const sectionNames = [];
                sections.forEach(section => {
                    const headerElement = section.querySelector('.card-header h5');
                    sectionNames.push(headerElement ? headerElement.textContent :
                        `Section ${sectionNames.length + 1}`);
                });

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

                    step.addEventListener('click', function() {
                        goToSection(parseInt(this.dataset.index));
                    });
                }

                stepIndicator.appendChild(stepsContainer);
            }

            // Define goToSection inside DOMContentLoaded
            goToSection = function(index) {
                if (index < 0 || index >= totalSections) {
                    console.error('Invalid section index:', index);
                    return;
                }

                if (index < currentSectionIndex || validateCurrentSection()) {
                    sections[currentSectionIndex].classList.remove('active');
                    currentSectionIndex = index;
                    sections[currentSectionIndex].classList.add('active');
                    updateProgressBar();
                    document.querySelector('#assessment-form')?.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            };

            function setupRealTimeValidation() {
                // Add real-time validation for form elements
                $(document).on('change input', '.assessment-response, .staff-count-input', function() {
                    validateElement(this);
                });

                $(document).on('change', 'input[type="checkbox"]', function() {
                    const container = $(this).closest('.question-container, .mb-4')[0];
                    if (container) {
                        const checkboxGroup = container.querySelectorAll('input[type="checkbox"]');
                        if (checkboxGroup.length > 0) {
                            validateElement(checkboxGroup[0]);
                        }
                    }
                });
            }

            // Set up autosave on navigation
            document.querySelectorAll('.next-btn, .prev-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    saveTemporaryResponses();
                });
            });

            console.log('=== INITIALIZATION COMPLETE ===');
        });

        // JQUERY READY FOR SUBMIT FUNCTIONALITY
        $(document).ready(function() {
            console.log('jQuery ready - setting up submit handler');

            $('#submit-btn').on('click', function(e) {
                e.preventDefault();
                console.log('Submit button clicked!');

                // Final validation before submit
                if (!validateCurrentSection()) {
                    $('#form-errors').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i> Please correct all validation errors before submitting.
                    </div>
                `).show();
                    return;
                }

                // Determine if this is an update or new submission
                const isUpdate = $('#form-action').val() === 'update';
                console.log('Submitting form, isUpdate:', isUpdate);

                // Call the global submit function
                submitAssessmentForm(isUpdate);
            });

            // Debug: Log that submit handler is attached
            console.log('Submit button handler attached');
            console.log('Submit button element:', document.getElementById('submit-btn'));
        });
    </script>

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
          $('#qip-modal-trigger').on('click', function(e) {
                e.preventDefault();
                var modal = new bootstrap.Modal(document.getElementById('qipModal'));
                modal.show();
            });
    </script>

</body>

</html>
