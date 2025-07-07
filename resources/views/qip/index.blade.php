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

        .form-group select,
        .form-group input {
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

        .progress {
            background-color: #e9ecef;
            border-radius: 6px;
            overflow: hidden;
        }

        .progress-bar {
            transition: width 0.6s ease;
        }

        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .alert-success {
            border-left: 5px solid #28a745;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

<<<<<<< HEAD
        /* Enhanced Modal Styles */
        .modal-xl {
            max-width: 95%;
        }

        @media (max-width: 768px) {
            .modal-xl {
                max-width: 98%;
                margin: 10px;
            }

            .modal-dialog {
                margin: 10px;
            }
        }

        .enhanced-form-group {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .enhanced-form-group.highlighted {
            border-color: #0199dc;
            background: rgba(1, 153, 220, 0.05);
        }

        .form-section-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.1rem;
            border-bottom: 2px solid #0199dc;
            padding-bottom: 5px;
        }

        .quarter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
        }

        @media (max-width: 576px) {
            .quarter-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
        }

        .quarter-option {
            padding: 12px 8px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #fff;
            user-select: none;
            min-height: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .quarter-option:hover {
            border-color: #0199dc;
            background: rgba(1, 153, 220, 0.1);
            transform: translateY(-2px);
        }

        .quarter-option.selected {
            border-color: #0199dc;
            background: #0199dc;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(1, 153, 220, 0.3);
        }

        .quarter-option strong {
            font-size: 1.1rem;
            display: block;
        }

        .quarter-option small {
            font-size: 0.8rem;
            opacity: 0.8;
        }

        .year-selector {
            display: flex;
            align-items: center;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        @media (max-width: 576px) {
            .year-selector {
                gap: 10px;
            }
        }

        .year-btn {
            padding: 10px 15px;
            border: 1px solid #dee2e6;
            background: #fff;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            user-select: none;
            min-width: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .year-btn:hover {
            background: #f8f9fa;
            border-color: #0199dc;
        }

        .year-btn:active {
            transform: scale(0.95);
        }

        #current-year {
            font-size: 1.2rem;
            font-weight: 600;
            padding: 10px 20px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            min-width: 80px;
            text-align: center;
        }

        @media (max-width: 576px) {
            #current-year {
                font-size: 1.1rem;
                padding: 8px 15px;
                min-width: 70px;
            }
        }

        .date-info {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 6px;
            padding: 10px;
            margin-top: 10px;
            font-size: 0.9rem;
            color: #1976d2;
        }

        @media (max-width: 576px) {
            .date-info {
                font-size: 0.8rem;
                padding: 8px;
            }
        }

        .validation-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 5px;
            display: none;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .form-control.is-valid {
            border-color: #28a745;
        }

        /* Mobile responsive adjustments */
        @media (max-width: 768px) {
            .modal-body {
                padding: 15px;
            }

            .enhanced-form-group {
                padding: 12px;
                margin-bottom: 15px;
            }

            .form-section-title {
                font-size: 1rem;
            }

            .row>div {
                margin-bottom: 15px;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 60px;
                padding: 10px 5px;
            }

            .sidebar h2 {
                display: none;
            }

            .sidebar a {
                font-size: 0.8rem;
                text-align: center;
                padding: 8px 0;
            }

            .main-content {
                margin-left: 60px;
                padding: 15px;
            }

            .header h1 {
                font-size: 1.5rem;
            }

            .modal-footer {
                padding: 10px 15px;
                flex-direction: column;
                gap: 10px;
            }

            .modal-footer .btn {
                width: 100%;
                margin: 0;
            }
        }

=======
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
        /* New Styles for Enhanced Features */
        .progress-container {
            position: sticky;
            top: 0;
            z-index: 1020;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #dee2e6;
            padding: 15px 20px;
            margin: -30px -30px 20px -30px;
        }

        .auto-save-indicator {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1030;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .auto-save-indicator.saving {
            background-color: #ffc107;
            color: #212529;
        }

        .auto-save-indicator.saved {
            background-color: #28a745;
            color: white;
        }

        .auto-save-indicator.error {
            background-color: #dc3545;
            color: white;
        }

<<<<<<< HEAD
=======
        .section-progress {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .section-progress-bar {
            flex: 1;
            height: 6px;
            background-color: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
        }

        .section-progress-fill {
            height: 100%;
            background-color: #28a745;
            transition: width 0.3s ease;
        }

>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
        .floating-save-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1025;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-radius: 50px;
            padding: 15px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .floating-save-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

<<<<<<< HEAD
=======
        .question-row.answered {
            background-color: #f8f9fa;
        }

        .question-row.highlighted {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
        }

        .section-card.highlighted-section {
            border: 2px solid #ffc107;
            box-shadow: 0 0 15px rgba(255, 193, 7, 0.3);
        }

        .validation-summary-section {
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background-color 0.2s ease;
        }

        .validation-summary-section:hover {
            background-color: rgba(220, 53, 69, 0.1);
        }

        .question-counter {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .section-completion-badge {
            font-size: 0.75rem;
            padding: 2px 8px;
        }

        .question-row.reviewing {
            background-color: #fff3cd !important;
            border-left: 4px solid #ffc107 !important;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.4);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
            }
        }

        /* Assessment Mode Styles */
        .mode-option {
            margin-bottom: 15px;
        }

        .mode-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .mode-option input[type="radio"]:checked+label .mode-card {
            border-color: #0199dc !important;
            background-color: rgba(1, 153, 220, 0.05);
            box-shadow: 0 0 0 2px rgba(1, 153, 220, 0.2);
        }

        .facility-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            border-left: 4px solid #28a745;
            margin-top: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .facility-info h6 {
            color: #343a40;
            font-weight: 600;
            margin-bottom: 15px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
        }

>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
        /* Mobile responsiveness for floating button */
        @media (max-width: 768px) {
            .floating-save-button {
                bottom: 20px;
                right: 20px;
                padding: 12px 20px;
                font-size: 0.875rem;
            }
        }
<<<<<<< HEAD
=======

        .mode-option input[type="radio"]:checked+label .mode-card {
            border-color: #0199dc !important;
            background-color: rgba(1, 153, 220, 0.05);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(1, 153, 220, 0.15);
        }
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Immediate cleanup of any existing modals before DOM ready
        (function() {
            console.log('Immediate cleanup starting...');

            // Remove any modals that might exist from previous page loads
            document.addEventListener('DOMContentLoaded', function() {
                // Find and remove any duplicate modals
                const modals = document.querySelectorAll('.modal');
                let enhancedModalFound = false;

                modals.forEach(function(modal, index) {
                    if (modal.id === 'qipModal' && !enhancedModalFound) {
                        enhancedModalFound = true;
                        console.log('Keeping enhanced modal');
                    } else {
                        console.log('Removing duplicate/old modal:', modal.id || 'unnamed');
                        modal.remove();
                    }
                });

                // Clean up any leftover backdrops
                document.querySelectorAll('.modal-backdrop').forEach(function(backdrop) {
                    backdrop.remove();
                });

                // Ensure body classes are clean
                document.body.classList.remove('modal-open');
                document.body.style.paddingRight = '';
            });
        })();

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
                    class="fas fa-file-medical-alt me-2"></i> SafeCare</a>
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

            <!-- Auto-save indicator -->
            <div id="auto-save-indicator" class="auto-save-indicator" style="display: none;">
                <i class="fas fa-spinner fa-spin me-2"></i>
                <span class="indicator-text">Saving...</span>
            </div>

            <!-- Dashboard content would go here -->
            <div class="header">
                <div>
                    <h1>Welcome, {{ Auth::user()->full_name }}</h1>
                    <p class="text-muted">Here's an overview of your day.</p>
                </div>
                <div class="profile">
                    <div
                        style="width: 50px; height: 50px; border-radius: 50%; background: #0199dc; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; margin-right: 12px; border: 2px solid #2c3e50;">
                        {{ substr(Auth::user()->full_name, 0, 1) }}
                    </div>
                    <span>{{ Auth::user()->full_name }}</span>
                </div>
            </div>

            <!-- Enhanced QIP Modal -->
            <div class="modal fade" id="qipModal" tabindex="-1" aria-labelledby="qipModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="qipModalLabel">
                                <i class="fas fa-file-medical-alt me-2"></i>
                                Quality Improvement Assessment Setup
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form id="qip-selection-form">
                            @csrf
                            <div class="modal-body">
<<<<<<< HEAD
                                <!-- Facility Selection Section -->
                                <div class="enhanced-form-group">
                                    <h6 class="form-section-title">
                                        <i class="fas fa-hospital me-2"></i>Facility Selection
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="district_id">
                                                    <i class="fas fa-map-marked-alt me-1"></i>District
                                                </label>
                                                <select id="district_id" name="district_id" class="form-control"
                                                    required>
                                                    <option value="">Select District</option>
                                                    @foreach ($districts as $district)
                                                        <option value="{{ $district->id }}"
                                                            data-name="{{ $district->name }}">
                                                            {{ $district->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="validation-message">Please select a district</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="lga_id">
                                                    <i class="fas fa-map-marker-alt me-1"></i>Local Government Area
                                                </label>
                                                <select id="lga_id" name="lga_id" class="form-control" required>
                                                    <option value="">Select Local Government Area</option>
                                                </select>
                                                <div class="validation-message">Please select an LGA</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="phc_id">
                                                    <i class="fas fa-clinic-medical me-1"></i>Primary Health Care (PHC)
                                                </label>
                                                <select id="phc_id" name="phc_id" class="form-control" required>
                                                    <option value="">Select Primary Health Care (PHC)</option>
                                                </select>
                                                <div class="validation-message">Please select a PHC</div>
                                            </div>
                                        </div>
                                    </div>
=======
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
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
                                </div>

                                <!-- Assessment Period Selection -->
                                <div class="enhanced-form-group">
                                    <h6 class="form-section-title">
                                        <i class="fas fa-calendar me-2"></i>Assessment Period
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>
                                                <i class="fas fa-calendar-alt me-1"></i>Assessment Quarter
                                            </label>
                                            <div class="quarter-grid mt-2">
                                                <div class="quarter-option" data-quarter="Q1">
                                                    <strong>Q1</strong><br>
                                                    <small>Jan - Mar</small>
                                                </div>
                                                <div class="quarter-option" data-quarter="Q2">
                                                    <strong>Q2</strong><br>
                                                    <small>Apr - Jun</small>
                                                </div>
                                                <div class="quarter-option" data-quarter="Q3">
                                                    <strong>Q3</strong><br>
                                                    <small>Jul - Sep</small>
                                                </div>
                                                <div class="quarter-option" data-quarter="Q4">
                                                    <strong>Q4</strong><br>
                                                    <small>Oct - Dec</small>
                                                </div>
                                            </div>
                                            <input type="hidden" id="selected_quarter" name="quarter" required>
                                            <div class="validation-message">Please select a quarter</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label>
                                                <i class="fas fa-calendar-year me-1"></i>Assessment Year
                                            </label>
                                            <div class="year-selector mt-2">
                                                <button type="button" class="year-btn" id="prev-year">
                                                    <i class="fas fa-chevron-left"></i>
                                                </button>
                                                <span id="current-year" class="px-3 py-2 bg-light rounded">2024</span>
                                                <button type="button" class="year-btn" id="next-year">
                                                    <i class="fas fa-chevron-right"></i>
                                                </button>
                                            </div>
                                            <input type="hidden" id="selected_year" name="year" required>
                                            <div class="validation-message">Please select a year</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Assessment Date Selection -->
                                <div class="enhanced-form-group">
                                    <h6 class="form-section-title">
                                        <i class="fas fa-calendar-day me-2"></i>Assessment Date
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="assessment_date">
                                                    <i class="fas fa-calendar-check me-1"></i>Date of Assessment
                                                </label>
                                                <input type="date" id="assessment_date" name="assessment_date"
                                                    class="form-control" required>
                                                <div class="validation-message">Please select an assessment date</div>
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-6">
                                            <div class="date-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <strong>Assessment Date Guidelines:</strong><br>
                                                • Date cannot be in the future<br>
                                                • Should align with selected quarter<br>
                                                • Consider operational dates of the facility
                                            </div>
                                        </div> --}}
                                    </div>
                                </div>

                                <!-- Assessment Summary -->
                                <div class="enhanced-form-group" id="assessment-summary" style="display: none;">
                                    <h6 class="form-section-title">
                                        <i class="fas fa-clipboard-check me-2"></i>Assessment Summary
                                    </h6>
                                    <div class="row" id="summary-content">
                                        <!-- Summary will be populated by JavaScript -->
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-2"></i>Close
                                </button>
                                <button type="button" id="submit-selection" class="btn btn-primary" disabled>
                                    <i class="fas fa-play me-2"></i>Start Assessment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Global variables
            let autoSaveTimer;
            let currentAssessmentData = {};
            let totalQuestions = 0;
            let answeredQuestions = 0;
            let existingAssessmentId = null;
            let currentAssessmentMode = 'new';
<<<<<<< HEAD
            let currentYear = new Date().getFullYear();

            // Initialize current year
            $('#current-year').text(currentYear);
            $('#selected_year').val(currentYear);
=======
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee

            // Setup AJAX with CSRF token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Load saved progress on page load
            loadSavedProgress();

<<<<<<< HEAD
            // Auto-open modal if coming from dashboard or URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const autoOpen = urlParams.get('auto_open') === 'true' ||
                document.referrer.includes('/dashboard') ||
                localStorage.getItem('auto_open_qip_modal') === 'true';

            if (autoOpen) {
                console.log('Auto-opening QIP modal...');
                // Clear the flag
                localStorage.removeItem('auto_open_qip_modal');

                // Wait for DOM to be fully ready, then open modal
                setTimeout(() => {
                    if ($('#qipModal').length > 0) {
                        var modal = new bootstrap.Modal(document.getElementById('qipModal'), {
                            backdrop: 'static',
                            keyboard: false
                        });
                        modal.show();
                    }
                }, 500);
            }

            // Ensure only one modal exists and properly initialize
            $(document).ready(function() {
                // Remove any existing old modals first
                $('div[id*="Modal"]:not(#qipModal)').remove();
                $('.modal-backdrop').remove();

                console.log('DOM ready, checking for modals...');
                console.log('Enhanced modal exists:', $('#qipModal').length > 0);
=======
            // Quality Improvement modal trigger
            $('#qip-modal-trigger').on('click', function(e) {
                e.preventDefault();
                var modal = new bootstrap.Modal(document.getElementById('qipModal'));
                modal.show();
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
            });

            // Quality Improvement modal trigger - Updated to handle both old and new triggers
            $(document).on('click', 'a[href*="quality"], a[href*="Quality"], [data-bs-target="#qipModal"]',
                function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    console.log('Quality Improvement clicked');

                    // Remove any existing modals and backdrops
                    $('.modal').modal('hide');
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');

                    // Ensure the enhanced modal exists
                    if ($('#qipModal').length === 0) {
                        console.error('Enhanced modal not found in DOM');
                        alert('Modal not found. Please refresh the page.');
                        return;
                    }

                    // Wait a bit then show the modal
                    setTimeout(() => {
                        var modal = new bootstrap.Modal(document.getElementById('qipModal'), {
                            backdrop: 'static',
                            keyboard: false
                        });
                        modal.show();
                    }, 100);

                    return false;
                });

            // Dropdown cascade for District -> LGA -> PHC
            $('#district_id').on('change', function() {
                var districtId = this.value;
                $('#lga_id').html('<option value="">Select LGA</option>').removeClass(
                'is-valid is-invalid');
                $('#phc_id').html('<option value="">Select PHC</option>').removeClass(
                'is-valid is-invalid');
                clearFieldError('#lga_id');
                clearFieldError('#phc_id');
                validateForm();

                if (districtId) {
                    // Show loading state
                    $('#lga_id').html('<option value="">Loading LGAs...</option>');

                    $.ajax({
                        url: '/get-lgas/' + districtId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#lga_id').html('<option value="">Select LGA</option>');
                            $.each(data, function(key, value) {
                                $('#lga_id').append('<option value="' + value.id +
                                    '" data-name="' + value.name + '">' + value
                                    .name + '</option>');
                            });
                            $('#lga_id').addClass('is-valid');
                        },
                        error: function() {
                            $('#lga_id').html('<option value="">Error loading LGAs</option>');
                            showFieldError('#lga_id', 'Failed to load LGAs');
                        }
                    });
                }
            });

            $('#lga_id').on('change', function() {
                var lgaId = this.value;
                $('#phc_id').html('<option value="">Select PHC</option>').removeClass(
                'is-valid is-invalid');
                clearFieldError('#phc_id');
                validateForm();

                if (lgaId) {
                    // Show loading state
                    $('#phc_id').html('<option value="">Loading PHCs...</option>');

                    $.ajax({
                        url: '/get-phcs/' + lgaId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#phc_id').html('<option value="">Select PHC</option>');
                            $.each(data, function(key, value) {
                                $('#phc_id').append('<option value="' + value.id +
                                    '" data-name="' + value.name + '">' + value
                                    .name + '</option>');
                            });
                            $('#phc_id').addClass('is-valid');
                            validateForm();
                        },
                        error: function() {
                            $('#phc_id').html('<option value="">Error loading PHCs</option>');
                            showFieldError('#phc_id', 'Failed to load PHCs');
                        }
                    });
                }
            });

            $('#phc_id').on('change', function() {
                if ($(this).val()) {
                    $(this).addClass('is-valid').removeClass('is-invalid');
                    clearFieldError('#phc_id');
                } else {
                    $(this).removeClass('is-valid');
                }
                validateForm();
            });

            // Quarter selection - Fixed click handler
            $(document).on('click', '.quarter-option', function(e) {
                e.preventDefault();
                e.stopPropagation();

                $('.quarter-option').removeClass('selected');
                $(this).addClass('selected');

                const selectedQuarter = $(this).data('quarter');
                $('#selected_quarter').val(selectedQuarter);

                console.log('Quarter selected:', selectedQuarter);
                validateForm();
                updateAssessmentDateConstraints();
            });

            // Year selection - Fixed click handlers
            $(document).on('click', '#prev-year', function(e) {
                e.preventDefault();
                e.stopPropagation();

                if (currentYear > 2020) {
                    currentYear--;
                    $('#current-year').text(currentYear);
                    $('#selected_year').val(currentYear);
                    console.log('Year changed to:', currentYear);
                    validateForm();
                    updateAssessmentDateConstraints();
                }
            });

            $(document).on('click', '#next-year', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const maxYear = new Date().getFullYear() + 1;
                if (currentYear < maxYear) {
                    currentYear++;
                    $('#current-year').text(currentYear);
                    $('#selected_year').val(currentYear);
                    console.log('Year changed to:', currentYear);
                    validateForm();
                    updateAssessmentDateConstraints();
                }
            });

            // Assessment date change
            $('#assessment_date').on('change', function() {
                if ($(this).val()) {
                    $(this).addClass('is-valid').removeClass('is-invalid');
                    clearFieldError('#assessment_date');
                } else {
                    $(this).removeClass('is-valid');
                }
                validateForm();
                validateAssessmentDate();
            });

            // Form validation with better error handling
            function validateForm() {
                const district = $('#district_id').val();
                const lga = $('#lga_id').val();
                const phc = $('#phc_id').val();
                const quarter = $('#selected_quarter').val();
                const year = $('#selected_year').val();
                const assessmentDate = $('#assessment_date').val();

                console.log('Form validation:', {
                    district,
                    lga,
                    phc,
                    quarter,
                    year,
                    assessmentDate
                });

                // Visual feedback for each field
                if (district) {
                    $('#district_id').addClass('is-valid').removeClass('is-invalid');
                    clearFieldError('#district_id');
                } else {
                    $('#district_id').removeClass('is-valid');
                }

                if (lga) {
                    $('#lga_id').addClass('is-valid').removeClass('is-invalid');
                    clearFieldError('#lga_id');
                } else {
                    $('#lga_id').removeClass('is-valid');
                }

                if (phc) {
                    $('#phc_id').addClass('is-valid').removeClass('is-invalid');
                    clearFieldError('#phc_id');
                } else {
                    $('#phc_id').removeClass('is-valid');
                }

                if (quarter) {
                    clearFieldError('#selected_quarter');
                    $('.quarter-grid').removeClass('border border-danger');
                } else {
                    $('.quarter-grid').addClass('border border-danger');
                }

                if (year) {
                    clearFieldError('#selected_year');
                } else {
                    showFieldError('#selected_year', 'Please select a year');
                }

                if (assessmentDate) {
                    $('#assessment_date').addClass('is-valid').removeClass('is-invalid');
                    clearFieldError('#assessment_date');
                } else {
                    $('#assessment_date').removeClass('is-valid');
                }

                const isValid = district && lga && phc && quarter && year && assessmentDate;

                $('#submit-selection').prop('disabled', !isValid);

                if (isValid) {
                    updateAssessmentSummary();
                } else {
                    $('#assessment-summary').hide();
                }

                return isValid;
            }

            function validateAssessmentDate() {
                const assessmentDate = $('#assessment_date').val();
                const quarter = $('#selected_quarter').val();
                const year = parseInt($('#selected_year').val());

                if (!assessmentDate || !quarter || !year) return;

                const selectedDate = new Date(assessmentDate);
                const today = new Date();

                // Only check if date is in future - remove quarter month restrictions
                if (selectedDate > today) {
                    showFieldError('#assessment_date', 'Assessment date cannot be in the future');
                    return false;
                }

                // Optional: Check if date is in selected year (you can remove this too if needed)
                const selectedYear = selectedDate.getFullYear();
                if (selectedYear !== year) {
                    showFieldError('#assessment_date', 'Assessment date must be in the selected year (' + year +
                        ')');
                    return false;
                }

                // Remove all quarter-specific month validation
                // The quarter selection is now just for reporting/categorization purposes
                // Users can select any date within the year for any quarter

                clearFieldError('#assessment_date');
                return true;
            }

            function updateAssessmentDateConstraints() {
    const quarter = $('#selected_quarter').val();
    const year = parseInt($('#selected_year').val());

    if (!quarter || !year) return;

    // Set flexible date range - entire year is allowed
    const minDate = year + '-01-01';

    // Don't allow future dates
    const today = new Date();
    const todayStr = today.toISOString().split('T')[0];
    const maxDate = year <= today.getFullYear() ?
        (year < today.getFullYear() ? year + '-12-31' : todayStr) :
        year + '-12-31';

    $('#assessment_date').attr('min', minDate).attr('max', maxDate);
}


            function showFieldError(fieldSelector, message) {
                const field = $(fieldSelector);
                field.addClass('is-invalid').removeClass('is-valid');

                // Handle different field types
                if (fieldSelector === '#selected_quarter') {
                    $('.quarter-grid').addClass('border border-danger');
                    $('.quarter-grid').after(
                        '<div class="validation-message text-danger" style="display: block;">' + message +
                        '</div>');
                } else if (fieldSelector === '#selected_year') {
                    $('.year-selector').addClass('border border-danger rounded p-2');
                    $('.year-selector').after(
                        '<div class="validation-message text-danger" style="display: block;">' + message +
                        '</div>');
                } else {
                    field.siblings('.validation-message').text(message).show();
                }
            }

            function clearFieldError(fieldSelector) {
                const field = $(fieldSelector);
                field.removeClass('is-invalid');

                // Handle different field types
                if (fieldSelector === '#selected_quarter') {
                    $('.quarter-grid').removeClass('border border-danger');
                    $('.quarter-grid').next('.validation-message').remove();
                } else if (fieldSelector === '#selected_year') {
                    $('.year-selector').removeClass('border border-danger rounded p-2');
                    $('.year-selector').next('.validation-message').remove();
                } else {
                    field.siblings('.validation-message').hide();
                }
            }

            function updateAssessmentSummary() {
                const district = $('#district_id option:selected').data('name');
                const lga = $('#lga_id option:selected').data('name');
                const phc = $('#phc_id option:selected').data('name');
                const quarter = $('#selected_quarter').val();
                const year = $('#selected_year').val();
                const assessmentDate = $('#assessment_date').val();

                if (!district || !lga || !phc || !quarter || !year || !assessmentDate) {
                    $('#assessment-summary').hide();
                    return;
                }

                const formattedDate = new Date(assessmentDate).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                const summaryHtml = `
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">Facility Information</h6>
                        <div class="mb-2"><strong>District:</strong> <span class="badge bg-secondary">${district}</span></div>
                        <div class="mb-2"><strong>LGA:</strong> <span class="badge bg-secondary">${lga}</span></div>
                        <div class="mb-2"><strong>PHC:</strong> <span class="badge bg-secondary">${phc}</span></div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-success mb-3">Assessment Period</h6>
                        <div class="mb-2"><strong>Quarter:</strong> <span class="badge bg-primary">${quarter}</span></div>
                        <div class="mb-2"><strong>Year:</strong> <span class="badge bg-primary">${year}</span></div>
                        <div class="mb-2"><strong>Assessment Date:</strong> <span class="badge bg-success">${formattedDate}</span></div>
                    </div>
                `;

                $('#summary-content').html(summaryHtml);
                $('#assessment-summary').show();
            }

            // Handle form submission - store values in localStorage and load assessment
            $('#submit-selection').on('click', function() {
                if (!validateForm()) {
                    alert('Please complete all required fields');
                    return;
                }

                if (!validateAssessmentDate()) {
                    alert('Please correct the assessment date');
                    return;
                }

                var district_id = $('#district_id').val();
                var lga_id = $('#lga_id').val();
                var phc_id = $('#phc_id').val();
                var quarter = $('#selected_quarter').val();
                var year = $('#selected_year').val();
                var assessment_date = $('#assessment_date').val();

                // Store selections in localStorage
                var selections = {
                    district_id: district_id,
                    district_name: $('#district_id option:selected').data('name'),
                    lga_id: lga_id,
                    lga_name: $('#lga_id option:selected').data('name'),
                    phc_id: phc_id,
                    phc_name: $('#phc_id option:selected').data('name'),
                    quarter: quarter,
                    year: year,
                    assessment_date: assessment_date
                };

                localStorage.setItem('qip_selections', JSON.stringify(selections));

                // Close modal
                var modal = bootstrap.Modal.getInstance(document.getElementById('qipModal'));
                modal.hide();

                loadSafecareAssessment();
            });

            function loadSavedProgress() {
                // Check if there's a saved assessment in progress
                const savedProgress = localStorage.getItem('assessment_progress');
                const savedSelections = localStorage.getItem('qip_selections');

                if (savedProgress && savedSelections) {
                    showProgressRestoreOption();
                }
            }

            function showProgressRestoreOption() {
                const savedSelections = JSON.parse(localStorage.getItem('qip_selections'));
                const savedTime = localStorage.getItem('assessment_save_time');

                if (savedSelections && savedTime) {
                    const timeAgo = new Date(parseInt(savedTime));
                    const html = `
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-clock me-2"></i>
                    <strong>Resume Assessment:</strong> You have an unfinished assessment for
<<<<<<< HEAD
                    <strong>${savedSelections.phc_name}</strong> (${savedSelections.quarter} ${savedSelections.year}) saved on ${timeAgo.toLocaleDateString()} at ${timeAgo.toLocaleTimeString()}.
=======
                    <strong>${savedSelections.phc_name}</strong> saved on ${timeAgo.toLocaleDateString()} at ${timeAgo.toLocaleTimeString()}.
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
                    <div class="mt-2">
                        <button id="resume-assessment" class="btn btn-sm btn-success me-2">
                            <i class="fas fa-play me-1"></i> Resume Assessment
                        </button>
                        <button id="start-fresh" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-plus me-1"></i> Start New Assessment
                        </button>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;

                    $('.main-content').prepend(html);

                    $('#resume-assessment').on('click', function() {
                        $('.alert').remove();
                        loadSafecareAssessment(true); // true = restore progress
                    });

                    $('#start-fresh').on('click', function() {
                        clearSavedProgress();
                        $('.alert').remove();
                    });
                }
            }

<<<<<<< HEAD
=======
            // function loadSafecareAssessment(restoreProgress = false) {
            //     var selections = JSON.parse(localStorage.getItem('qip_selections'));
            //     console.log("Loading assessment with selections:", selections);

            //     $('.main-content').append(
            //         '<div id="loading" class="text-center mt-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading assessment questions...</p></div>'
            //     );

            //     $.ajax({
            //         url: '/get-safecare-assessment',
            //         type: 'GET',
            //         dataType: 'json',
            //         data: {
            //             district_id: selections.district_id,
            //             lga_id: selections.lga_id,
            //             phc_id: selections.phc_id
            //         },
            //         success: function(response) {
            //             $('#loading').remove();
            //             console.log("Response received:", response);

            //             if (!response.questions || response.questions.length === 0) {
            //                 $('.main-content').append(
            //                     '<div class="alert alert-warning">No assessment questions found. Please check your database.</div>'
            //                 );
            //                 return;
            //             }

            //             // Store response globally
            //             window.response = response;
            //             totalQuestions = response.questions.length;

            //             // Check if there's existing assessment data
            //             if (response.safecare_result && response.safecare_result.id) {
            //                 existingAssessmentId = response.safecare_result.id;
            //                 currentAssessmentData = response.safecare_result;
            //             } else {
            //                 existingAssessmentId = null;
            //                 currentAssessmentData = {};
            //             }

            //             displayAssessment(response.questions, selections, restoreProgress);
            //         },
            //         error: function(xhr, status, error) {
            //             $('#loading').remove();
            //             console.error("AJAX Error:", status, error);
            //             console.error("Response:", xhr.responseText);
            //             $('.main-content').append(
            //                 '<div class="alert alert-danger">Error loading assessment. Please try again.</div>'
            //             );
            //         }
            //     });
            // }

            // ADD THIS DEBUG VERSION TO YOUR JAVASCRIPT
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
            function loadSafecareAssessment(restoreProgress = false) {
                var selections = JSON.parse(localStorage.getItem('qip_selections'));
                console.log("Loading assessment with selections:", selections);

                // ENHANCED ERROR CHECKING
<<<<<<< HEAD
                if (!selections || !selections.district_id || !selections.lga_id || !selections.phc_id || !
                    selections.quarter || !selections.year || !selections.assessment_date) {
                    console.error("Invalid selections:", selections);
                    $('.main-content').append(
                        '<div class="alert alert-danger">Invalid selections. Please select District, LGA, PHC, Quarter, Year, and Assessment Date again.</div>'
=======
                if (!selections || !selections.district_id || !selections.lga_id || !selections.phc_id) {
                    console.error("Invalid selections:", selections);
                    $('.main-content').append(
                        '<div class="alert alert-danger">Invalid selections. Please select District, LGA, and PHC again.</div>'
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
                    );
                    return;
                }

                $('.main-content').append(
                    '<div id="loading" class="text-center mt-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading assessment questions...</p></div>'
                );

                // ADD DETAILED LOGGING
                console.log("Making AJAX request with params:", {
                    district_id: selections.district_id,
                    lga_id: selections.lga_id,
<<<<<<< HEAD
                    phc_id: selections.phc_id,
                    quarter: selections.quarter,
                    year: selections.year,
                    assessment_date: selections.assessment_date
=======
                    phc_id: selections.phc_id
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
                });

                $.ajax({
                    url: '/get-safecare-assessment',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        district_id: selections.district_id,
                        lga_id: selections.lga_id,
                        phc_id: selections.phc_id,
                        quarter: selections.quarter,
                        year: selections.year,
                        assessment_date: selections.assessment_date
                    },
                    success: function(response) {
                        $('#loading').remove();
                        console.log("AJAX SUCCESS - Full response received:", response);

                        // DETAILED RESPONSE CHECKING
                        if (!response) {
                            console.error("Empty response received");
                            $('.main-content').append(
                                '<div class="alert alert-danger">Empty response from server.</div>'
                            );
                            return;
                        }

                        // Check if response has error property
                        if (response.error) {
                            console.error("Server returned error:", response.error);
                            $('.main-content').append(
                                '<div class="alert alert-danger">Server Error: ' + response.error +
                                '</div>'
                            );
                            return;
                        }

                        // Check questions array
                        if (!response.questions) {
                            console.error("No questions property in response");
                            $('.main-content').append(
                                '<div class="alert alert-warning">No questions property found in response.</div>'
                            );
                            return;
                        }

                        if (!Array.isArray(response.questions)) {
                            console.error("Questions is not an array:", typeof response.questions);
                            $('.main-content').append(
                                '<div class="alert alert-warning">Questions data is not in expected format.</div>'
                            );
                            return;
                        }

                        if (response.questions.length === 0) {
                            console.warn("Questions array is empty");
                            $('.main-content').append(
                                '<div class="alert alert-warning">No assessment questions found. Please check your database.</div>'
                            );
                            return;
                        }

                        console.log("Questions validation passed. Count:", response.questions.length);
                        console.log("Sample question:", response.questions[0]);

                        // Store response globally
                        window.response = response;
                        totalQuestions = response.questions.length;

                        // Check if there's existing assessment data
                        if (response.safecare_result && response.safecare_result.id) {
                            existingAssessmentId = response.safecare_result.id;
                            currentAssessmentData = response.safecare_result;
                            console.log("Existing assessment found:", existingAssessmentId);
                        } else {
                            existingAssessmentId = null;
                            currentAssessmentData = {};
                            console.log("No existing assessment");
                        }

                        // Call display function
                        console.log("Calling displayAssessment...");
                        displayAssessment(response.questions, selections, restoreProgress);
                    },
                    error: function(xhr, status, error) {
                        $('#loading').remove();
                        console.error("AJAX Error Details:");
                        console.error("Status:", status);
                        console.error("Error:", error);
                        console.error("XHR Status:", xhr.status);
                        console.error("XHR Response Text:", xhr.responseText);

                        // Try to parse error response
                        let errorMessage = 'Unknown error occurred';
                        try {
                            const errorResponse = JSON.parse(xhr.responseText);
                            errorMessage = errorResponse.error || errorResponse.message || errorMessage;
                        } catch (e) {
                            console.error("Could not parse error response:", e);
                            errorMessage = 'Server error: ' + xhr.status + ' ' + error;
                        }

                        $('.main-content').append(
                            '<div class="alert alert-danger">' +
                            '<h5>Error Loading Assessment</h5>' +
                            '<p><strong>Error:</strong> ' + errorMessage + '</p>' +
                            '<p><strong>Status:</strong> ' + xhr.status + '</p>' +
                            '<div class="mt-2">' +
                            '<button class="btn btn-primary" onclick="location.reload()">Reload Page</button>' +
                            '</div>' +
                            '</div>'
                        );
                    }
                });
            }

<<<<<<< HEAD
=======
            // ALSO ADD THIS ENHANCED DISPLAYASSESSMENT FUNCTION CHECK
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
            function displayAssessment(questions, selections, restoreProgress = false) {
                console.log("displayAssessment called with:");
                console.log("- Questions count:", questions.length);
                console.log("- Selections:", selections);
                console.log("- Restore progress:", restoreProgress);

<<<<<<< HEAD
                // Clear any existing assessment
                $('#assessment-container').remove();

                if (!questions || questions.length === 0) {
                    console.error("No questions provided to displayAssessment");
                    $('.main-content').append(
                        '<div class="alert alert-danger">No questions available to display.</div>'
                    );
                    return;
                }

                console.log("Questions received:", questions);
                console.log("Full response object:", window.response);

=======
                // Clear any existing assessment
                $('#assessment-container').remove();

                if (!questions || questions.length === 0) {
                    console.error("No questions provided to displayAssessment");
                    $('.main-content').append(
                        '<div class="alert alert-danger">No questions available to display.</div>'
                    );
                    return;
                }

                console.log("Questions received:", questions);
                console.log("Full response object:", window.response);

                // Continue with your existing displayAssessment logic...
                // Sort questions
                questions.sort(function(a, b) {
                    var aNum = a.question_no.split('.').map(Number);
                    var bNum = b.question_no.split('.').map(Number);
                    for (var i = 0; i < Math.max(aNum.length, bNum.length); i++) {
                        var aVal = aNum[i] || 0;
                        var bVal = bNum[i] || 0;
                        if (aVal !== bVal) {
                            return aVal - bVal;
                        }
                    }
                    return 0;
                });

                console.log("Questions sorted successfully");

                // Group questions by section
                var sectionGroups = {};
                questions.forEach(function(question) {
                    if (!sectionGroups[question.section]) {
                        sectionGroups[question.section] = [];
                    }
                    sectionGroups[question.section].push(question);
                });

                console.log("Section groups created:", Object.keys(sectionGroups));

                // Continue with the rest of your displayAssessment function...
                // Rest of your existing code here
            }

            function displayAssessment(questions, selections, restoreProgress = false) {
                // Clear any existing assessment
                $('#assessment-container').remove();

                console.log("Questions received:", questions);
                console.log("Full response object:", window.response);

>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
                // Sort questions
                questions.sort(function(a, b) {
                    var aNum = a.question_no.split('.').map(Number);
                    var bNum = b.question_no.split('.').map(Number);
                    for (var i = 0; i < Math.max(aNum.length, bNum.length); i++) {
                        var aVal = aNum[i] || 0;
                        var bVal = bNum[i] || 0;
                        if (aVal !== bVal) {
                            return aVal - bVal;
                        }
                    }
                    return 0;
                });

                console.log("Questions sorted successfully");

                // Group questions by section
                var sectionGroups = {};
                questions.forEach(function(question) {
                    if (!sectionGroups[question.section]) {
                        sectionGroups[question.section] = [];
                    }
                    sectionGroups[question.section].push(question);
                });

                console.log("Section groups created:", Object.keys(sectionGroups));

                var html = `
<div id="assessment-container" class="mt-4">
    <!-- Auto-save Indicator -->
    <div id="auto-save-indicator" class="auto-save-indicator" style="display: none;">
        <i class="fas fa-save me-2"></i>
        <span class="indicator-text">Saving...</span>
    </div>

    <!-- Progress Container (Sticky) -->
    <div class="progress-container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Quality Improvement Assessment</h5>
            <div class="d-flex align-items-center gap-3">
                <span class="question-counter">
                    <span id="answered-count">0</span> / <span id="total-count">${totalQuestions}</span> questions
                </span>
                <button id="save-assessment-top" class="btn btn-success btn-sm">
                    <i class="fas fa-save me-1"></i> Save Assessment
                </button>
            </div>
        </div>
        <div class="progress" style="height: 8px;">
            <div id="overall-progress" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
        </div>
    </div>

    <!-- Validation Status -->
    <div id="validation-status" class="mb-4" style="display: none;"></div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Assessment Details</h5>`;

                // Check for existing assessments
                const hasExistingAssessment = (
                    window.response &&
                    window.response.safecare_result &&
                    window.response.safecare_result.id
                ) || (
                    window.response &&
                    window.response.has_previous_assessment === true
                ) || (
                    existingAssessmentId && existingAssessmentId > 0
                );

                console.log("Has existing assessment:", hasExistingAssessment);
                console.log("Existing assessment ID:", existingAssessmentId);
                console.log("SafeCare result:", window.response ? window.response.safecare_result : null);

                // Assessment Mode Selector - Only show if there's existing data
                if (hasExistingAssessment) {
<<<<<<< HEAD
                    const existingAssessmentData = window.response.safecare_result;
=======
                    const assessment = window.response.safecare_result;
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee

                    html += `
        <!-- Assessment Mode Selector -->
        <div class="assessment-mode-selector mb-4">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-cogs me-2"></i>Assessment Mode Selection</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Previous Assessment Found!</strong> Choose how you want to proceed:
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mode-option">
                                <input class="form-check-input" type="radio" name="assessmentMode" id="newAssessmentMode" value="new" checked>
                                <label class="form-check-label" for="newAssessmentMode">
                                    <div class="mode-card border rounded p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-plus-circle text-success fa-2x me-3"></i>
                                            <div>
                                                <h6 class="mb-0 text-success">New Assessment</h6>
                                                <small class="text-muted">Recommended</small>
                                            </div>
                                        </div>
<<<<<<< HEAD
                                        <p class="small mb-0">Create a fresh assessment for the selected period (${selections.quarter} ${selections.year}). This is the recommended option for regular quality improvement tracking.</p>
=======
                                        <p class="small mb-0">Create a fresh assessment with a new ID. This is the recommended option for regular quality improvement tracking.</p>
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
                                        <div class="mt-2">
                                            <span class="badge bg-success">✓ Creates new record</span>
                                            <span class="badge bg-info">✓ Archives old data</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mode-option">
                                <input class="form-check-input" type="radio" name="assessmentMode" id="updateAssessmentMode" value="update">
                                <label class="form-check-label" for="updateAssessmentMode">
                                    <div class="mode-card border rounded p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-edit text-warning fa-2x me-3"></i>
                                            <div>
                                                <h6 class="mb-0 text-warning">Update Existing</h6>
                                                <small class="text-muted">Modify current</small>
                                            </div>
                                        </div>
<<<<<<< HEAD
                                        <p class="small mb-0">Modify the existing assessment (ID: #${existingAssessmentId || 'N/A'}) for ${selections.quarter} ${selections.year}. Use this if you need to correct or add to the current assessment data.</p>
=======
                                        <p class="small mb-0">Modify the existing assessment (ID: #${existingAssessmentId || 'N/A'}). Use this if you need to correct or add to the current assessment data.</p>
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
                                        <div class="mt-2">
                                            <span class="badge bg-warning">✓ Modifies existing</span>
                                            <span class="badge bg-info">✓ Preserves dates</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="mode-description" class="alert alert-success mt-3">
                        <i class="fas fa-lightbulb me-2"></i>
<<<<<<< HEAD
                        <strong>New Assessment Mode:</strong> You will create a fresh assessment for ${selections.quarter} ${selections.year} with a new ID. The previous assessment will be preserved for historical reference.
=======
                        <strong>New Assessment Mode:</strong> You will create a fresh assessment with a new ID. The previous assessment will be preserved for historical reference.
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
                    </div>
                </div>
            </div>
        </div>`;

                    // Show existing assessment information with enhanced date handling
<<<<<<< HEAD
                    const currentAssessmentData = window.response.safecare_result;
                    const assessmentDate = currentAssessmentData.assessment_date ? new Date(currentAssessmentData
                        .assessment_date) : null;
                    const createdDate = currentAssessmentData.created_at ? new Date(currentAssessmentData
                        .created_at) : null;
                    const lastUpdatedDate = currentAssessmentData.last_updated_date ? new Date(currentAssessmentData
                            .last_updated_date) :
                        null;
                    const hasBeenUpdated = currentAssessmentData.has_been_updated || currentAssessmentData
                        .updated_by_name;

                    html += `
        <div class="previous-assessment-info">
            <h6><i class="fas fa-history me-2"></i>Previous Assessment Information (${currentAssessmentData.quarter} ${currentAssessmentData.year})</h6>
=======
                    const assessmentDate = assessment.assessment_date ? new Date(assessment.assessment_date) : null;
                    const createdDate = assessment.created_at ? new Date(assessment.created_at) : null;
                    const lastUpdatedDate = assessment.last_updated_date ? new Date(assessment.last_updated_date) :
                        null;
                    const hasBeenUpdated = assessment.has_been_updated || assessment.updated_by_name;

                    html += `
        <div class="previous-assessment-info">
            <h6><i class="fas fa-history me-2"></i>Previous Assessment Information</h6>
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
            <div class="row">
                <!-- Facility Information -->
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

                <!-- Assessment Results -->
                <div class="col-md-4 mb-3">
                    <span class="assessment-label">SafeCare Level:</span>
<<<<<<< HEAD
                    <span class="badge bg-info">${currentAssessmentData.safecare_level || 'Not Assessed'}</span>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="assessment-label">SafeCare Score:</span>
                    <span class="badge bg-primary">${currentAssessmentData.safecare_score || currentAssessmentData.compliance_percentage || 'N/A'}${(currentAssessmentData.safecare_score || currentAssessmentData.compliance_percentage) ? '%' : ''}</span>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="assessment-label">Assessment ID:</span>
                    <span class="badge bg-info">#${currentAssessmentData.id || 'N/A'}</span>
=======
                    <span class="badge bg-info">${assessment.safecare_level || 'Not Assessed'}</span>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="assessment-label">SafeCare Score:</span>
                    <span class="badge bg-primary">${assessment.safecare_score || assessment.compliance_percentage || 'N/A'}${(assessment.safecare_score || assessment.compliance_percentage) ? '%' : ''}</span>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="assessment-label">Assessment ID:</span>
                    <span class="badge bg-info">#${assessment.id || 'N/A'}</span>
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
                </div>

                <!-- Date Information -->
                <div class="col-md-4 mb-3">
<<<<<<< HEAD
                    <span class="assessment-label">Assessment Date:</span>
=======
                    <span class="assessment-label">Original Assessment Date:</span>
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
                    <span class="badge bg-success">${assessmentDate ? assessmentDate.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    }) : 'Never Assessed'}</span>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="assessment-label">Created On:</span>
                    <span class="badge bg-secondary">${createdDate ? createdDate.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    }) + ' at ' + createdDate.toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit'
                    }) : 'Unknown'}</span>
                </div>
                ${hasBeenUpdated ? `
<<<<<<< HEAD
                                    <div class="col-md-4 mb-3">
                                        <span class="assessment-label">Last Updated:</span>
                                        <span class="badge bg-warning text-dark">${lastUpdatedDate ? lastUpdatedDate.toLocaleDateString('en-US', {
                                            year: 'numeric',
                                            month: 'short',
                                            day: 'numeric'
                                        }) + ' at ' + lastUpdatedDate.toLocaleTimeString('en-US', {
                                            hour: '2-digit',
                                            minute: '2-digit'
                                        }) : 'Unknown'}</span>
                                    </div>
                                    ` : ''}
=======
                                <div class="col-md-4 mb-3">
                                    <span class="assessment-label">Last Updated:</span>
                                    <span class="badge bg-warning text-dark">${lastUpdatedDate ? lastUpdatedDate.toLocaleDateString('en-US', {
                                        year: 'numeric',
                                        month: 'short',
                                        day: 'numeric'
                                    }) + ' at ' + lastUpdatedDate.toLocaleTimeString('en-US', {
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    }) : 'Unknown'}</span>
                                </div>
                                ` : ''}
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee

                <!-- User Information -->
                <div class="col-md-4 mb-3">
                    <span class="assessment-label">Original Assessor:</span>
<<<<<<< HEAD
                    <span class="badge bg-dark">${currentAssessmentData.assessor_name || currentAssessmentData.user_name || 'Unknown'}</span>
                </div>
                ${hasBeenUpdated ? `
                                    <div class="col-md-4 mb-3">
                                        <span class="assessment-label">Last Updated By:</span>
                                        <span class="badge bg-warning text-dark">${currentAssessmentData.updated_by_name || 'Unknown'}</span>
                                    </div>
                                    ` : ''}
            </div>

            ${hasBeenUpdated ? `
                                <div class="alert alert-warning mt-3 mb-0">
                                    <i class="fas fa-edit me-2"></i>
                                    <strong>Update History:</strong> This assessment has been previously modified.
                                    The original assessment date (${assessmentDate ? assessmentDate.toLocaleDateString('en-US', {
                                        year: 'numeric',
                                        month: 'long',
                                        day: 'numeric'
                                    }) : 'N/A'}) is preserved for audit purposes.
                                </div>
                                ` : `
                                <div class="alert alert-info mt-3 mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Original Assessment:</strong> This assessment has not been modified since its creation.
                                </div>
                                `}
        </div>`;
                } else {
                    // No existing assessment - First time assessment for this period
                    html += `
        <div class="facility-info">
            <h6><i class="fas fa-info-circle me-2"></i>Assessment Information</h6>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <span class="assessment-label">District:</span>
                    <span class="badge bg-secondary">${selections.district_name}</span>
                </div>
                <div class="col-md-3 mb-3">
                    <span class="assessment-label">LGA:</span>
                    <span class="badge bg-secondary">${selections.lga_name}</span>
                </div>
                <div class="col-md-3 mb-3">
                    <span class="assessment-label">PHC:</span>
                    <span class="badge bg-secondary">${selections.phc_name}</span>
                </div>
                <div class="col-md-3 mb-3">
                    <span class="assessment-label">Period:</span>
                    <span class="badge bg-primary">${selections.quarter} ${selections.year}</span>
                </div>
                <div class="col-md-12">
                    <div class="alert alert-info mt-2">
                        <i class="fas fa-star me-2"></i>
                        <strong>New Assessment:</strong> No previous SafeCare assessment data found for this facility in ${selections.quarter} ${selections.year}.
                        Assessment Date: ${new Date(selections.assessment_date).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        })}
=======
                    <span class="badge bg-dark">${assessment.assessor_name || assessment.user_name || 'Unknown'}</span>
                </div>
                ${hasBeenUpdated ? `
                                <div class="col-md-4 mb-3">
                                    <span class="assessment-label">Last Updated By:</span>
                                    <span class="badge bg-warning text-dark">${assessment.updated_by_name || 'Unknown'}</span>
                                </div>
                                ` : ''}
            </div>

            ${hasBeenUpdated ? `
                            <div class="alert alert-warning mt-3 mb-0">
                                <i class="fas fa-edit me-2"></i>
                                <strong>Update History:</strong> This assessment has been previously modified.
                                The original assessment date (${assessmentDate ? assessmentDate.toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                }) : 'N/A'}) is preserved for audit purposes.
                            </div>
                            ` : `
                            <div class="alert alert-info mt-3 mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Original Assessment:</strong> This assessment has not been modified since its creation.
                            </div>
                            `}
        </div>`;
                } else {
                    // No existing assessment - First time assessment
                    html += `
        <div class="facility-info">
            <h6><i class="fas fa-info-circle me-2"></i>Current Facility Information</h6>
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
                        <i class="fas fa-star me-2"></i>
                        <strong>First Assessment:</strong> No previous SafeCare assessment data found for this facility. This will be the first assessment.
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
                    </div>
                </div>
            </div>
        </div>`;
                }

                html += `
        </div>
    </div>`;

                // Create sections with progress indicators
                Object.keys(sectionGroups).forEach(function(section) {
                    const sectionId = section.replace(/\s+/g, '-').toLowerCase();
                    const sectionQuestions = sectionGroups[section];

                    html += `
    <div class="card mb-4 section-card" id="section-${sectionId}">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">${section}</h5>
                <div class="d-flex align-items-center gap-2">
                    <span class="section-completion-badge badge bg-light text-dark" id="section-badge-${sectionId}">
                        0/${sectionQuestions.length}
                    </span>
                    <div class="section-progress">
                        <div class="section-progress-bar" style="width: 100px;">
                            <div class="section-progress-fill" id="section-progress-${sectionId}" style="width: 0%;"></div>
                        </div>
                    </div>
                </div>
            </div>
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
            <tr class="question-row" id="question-${question.id}" data-section="${sectionId}">
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

                // Add floating save button and bottom action bar
                html += `
    <!-- Bottom Action Bar -->
    <div class="card mt-4 mb-5">
        <div class="card-body text-center">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h6 class="mb-2">Assessment Progress</h6>
                    <div class="progress mb-2" style="height: 10px;">
                        <div id="bottom-progress" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                    </div>
                    <small class="text-muted">
                        Complete all <span id="total-questions-bottom">${totalQuestions}</span> questions to submit your assessment
                    </small>
                </div>
                <div class="col-md-4">
                    <button id="save-assessment-bottom" class="btn btn-success btn-lg">
                        <i class="fas fa-save me-2"></i> Save Assessment
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Save Button -->
<button id="floating-save-btn" class="floating-save-button btn btn-success" style="display: none;">
    <i class="fas fa-save me-2"></i> Save Assessment
</button>`;

                // Append the assessment to the main content
                $('.main-content').append(html);

                // Restore progress if needed
                if (restoreProgress) {
                    restoreSavedProgress();
                }

                // Setup event handlers
                setupAssessmentHandlers(questions, selections);
                updateProgress();
            }

            function populatePreviousResponses() {
                if (!window.response || !window.response.previousAssessment) {
                    console.log("No previous assessment data to populate");
                    return;
                }

                const previousResponses = window.response.previousAssessment;
                console.log("Populating previous responses:", previousResponses);

                // Populate each response
                Object.keys(previousResponses).forEach(questionId => {
                    const responseData = previousResponses[questionId];
                    const select = $(`.response-select[data-question-id="${questionId}"]`);

                    if (select.length && responseData.response) {
                        select.val(responseData.response);
                        select.closest('.question-row').addClass('answered');

                        // Add comment functionality if you have it
                        if (responseData.comment) {
                            console.log(`Comment for question ${questionId}:`, responseData.comment);
                        }
                    }
                });

                // Update all progress indicators after populating
                updateProgress();
                updateAllSectionProgress();

                console.log("Previous responses populated successfully");
            }

            function setupAssessmentHandlers(questions, selections) {
                // Assessment mode change handler
                $('input[name="assessmentMode"]').on('change', function() {
                    currentAssessmentMode = $(this).val();
                    const mode = $(this).val();
                    const description = $('#mode-description');

                    if (mode === 'new') {
                        description.removeClass('alert-warning').addClass('alert-success');
                        description.html(`
                    <i class="fas fa-lightbulb me-2"></i>
<<<<<<< HEAD
                    <strong>New Assessment Mode:</strong> You will create a fresh assessment for ${selections.quarter} ${selections.year} with a new ID.
=======
                    <strong>New Assessment Mode:</strong> You will create a fresh assessment with a new ID.
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
                    The previous assessment will be preserved for historical reference.
                `);

                        // Clear all responses for new assessment
                        $('.response-select').val('').removeClass('is-invalid');
                        $('.question-row').removeClass('answered');
                        updateProgress();
                        updateAllSectionProgress();

                    } else if (mode === 'update') {
                        description.removeClass('alert-success').addClass('alert-warning');
                        description.html(`
                    <i class="fas fa-edit me-2"></i>
<<<<<<< HEAD
                    <strong>Update Mode:</strong> You will modify the existing assessment (ID: #${existingAssessmentId || 'N/A'}) for ${selections.quarter} ${selections.year}.
                    Changes will be saved to the current assessment record.
=======
                    <strong>Update Mode:</strong> You will modify the existing assessment (ID: #${existingAssessmentId || 'N/A'}).
                    Changes will be saved to the current assessment record. The original assessment date will be preserved.
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
                `);

                        // Populate previous responses for update mode
                        populatePreviousResponses();
                    }
                });

                // Save button handlers
                $('#save-assessment-top, #save-assessment-bottom, #floating-save-btn').on('click', function() {
                    saveAssessment(questions, selections);
                });

                // Response change handlers with auto-save
                $('.response-select').on('change', function() {
                    const questionRow = $(this).closest('.question-row');
                    const sectionId = questionRow.data('section');

                    // Visual feedback
                    $(this).removeClass('is-invalid');
                    $(this).parent().find('.question-error').remove();
                    questionRow.addClass('answered');

                    // Update progress
                    updateProgress();
                    updateSectionProgress(sectionId);

                    // Auto-save with debouncing
                    clearTimeout(autoSaveTimer);
                    autoSaveTimer = setTimeout(() => {
                        autoSaveProgress();
                    }, 2000); // Save 2 seconds after last change
                });

                // Show floating button when scrolling
                $(window).on('scroll', function() {
                    const scrollTop = $(window).scrollTop();

                    // Show floating button if scrolled past the top save button
                    if (scrollTop > 200) {
                        $('#floating-save-btn').fadeIn();
                    } else {
                        $('#floating-save-btn').fadeOut();
                    }
                });
            }

            function updateProgress() {
                answeredQuestions = $('.response-select').filter(function() {
                    return $(this).val() !== '';
                }).length;

                const percentage = totalQuestions > 0 ? (answeredQuestions / totalQuestions) * 100 : 0;

                $('#answered-count').text(answeredQuestions);
                $('#total-count').text(totalQuestions);
                $('#overall-progress, #bottom-progress').css('width', percentage + '%');
            }

            function updateSectionProgress(sectionId) {
                const section = $(`#section-${sectionId}`);
                const sectionSelects = section.find('.response-select');
                const sectionAnswered = sectionSelects.filter(function() {
                    return $(this).val() !== '';
                }).length;
                const sectionTotal = sectionSelects.length;
                const sectionPercentage = sectionTotal > 0 ? (sectionAnswered / sectionTotal) * 100 : 0;

                $(`#section-badge-${sectionId}`).text(`${sectionAnswered}/${sectionTotal}`);
                $(`#section-progress-${sectionId}`).css('width', sectionPercentage + '%');
            }

            function updateAllSectionProgress() {
                $('.section-card').each(function() {
                    const sectionId = $(this).attr('id').replace('section-', '');
                    updateSectionProgress(sectionId);
                });
            }

            function autoSaveProgress() {
                const selections = JSON.parse(localStorage.getItem('qip_selections'));
                if (!selections) return;

                showAutoSaveIndicator('saving');

                const responses = {};
                $('.response-select').each(function() {
                    const questionId = $(this).data('question-id');
                    const response = $(this).val();
                    if (response) {
                        responses[questionId] = response;
                    }
                });

                // Save to localStorage
                const progressData = {
                    selections: selections,
                    responses: responses,
                    assessmentMode: currentAssessmentMode,
                    timestamp: Date.now()
                };

                localStorage.setItem('assessment_progress', JSON.stringify(progressData));
                localStorage.setItem('assessment_save_time', Date.now().toString());

                // Simulate server save (you can implement actual server-side temporary save here)
                setTimeout(() => {
                    showAutoSaveIndicator('saved');
                    setTimeout(() => {
                        hideAutoSaveIndicator();
                    }, 2000);
                }, 500);
            }

            function restoreSavedProgress() {
                const savedProgress = localStorage.getItem('assessment_progress');
                if (!savedProgress) return;

                try {
                    const progressData = JSON.parse(savedProgress);
                    const responses = progressData.responses;

                    // Restore assessment mode if available
                    if (progressData.assessmentMode && progressData.assessmentMode !== 'new') {
                        currentAssessmentMode = progressData.assessmentMode;
                        $(`input[name="assessmentMode"][value="${progressData.assessmentMode}"]`).prop('checked',
                                true)
                            .trigger('change');
                    }

                    // Restore responses
                    Object.keys(responses).forEach(questionId => {
                        const select = $(`.response-select[data-question-id="${questionId}"]`);
                        if (select.length) {
                            select.val(responses[questionId]);
                            select.closest('.question-row').addClass('answered');
                        }
                    });

                    // Update all progress indicators
                    updateProgress();
                    updateAllSectionProgress();

                    // Show restoration message
                    showAutoSaveIndicator('saved');
                    $('.auto-save-indicator .indicator-text').text('Progress restored');
                    setTimeout(() => {
                        hideAutoSaveIndicator();
                    }, 3000);

                } catch (error) {
                    console.error('Error restoring progress:', error);
                }
            }

            function showAutoSaveIndicator(status) {
                const indicator = $('#auto-save-indicator');
                const icon = indicator.find('i');
                const text = indicator.find('.indicator-text');

                indicator.removeClass('saving saved error').addClass(status);

                switch (status) {
                    case 'saving':
                        icon.attr('class', 'fas fa-spinner fa-spin me-2');
                        text.text('Saving progress...');
                        break;
                    case 'saved':
                        icon.attr('class', 'fas fa-check me-2');
                        text.text('Progress saved');
                        break;
                    case 'error':
                        icon.attr('class', 'fas fa-exclamation-triangle me-2');
                        text.text('Save failed');
                        break;
                }

                indicator.fadeIn();
            }

            function hideAutoSaveIndicator() {
                $('#auto-save-indicator').fadeOut();
            }

            function clearSavedProgress() {
                localStorage.removeItem('assessment_progress');
                localStorage.removeItem('assessment_save_time');
            }

            // Save function with proper endpoint handling
            function saveAssessment(questions, selections) {
                console.log("Save button clicked");

                // Determine if this is an update operation
                const isUpdateMode = currentAssessmentMode === 'update' && existingAssessmentId;

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
                console.log("Update mode:", isUpdateMode);
                console.log("Existing assessment ID:", existingAssessmentId);

                if (emptyResponses.length > 0) {
                    showValidationErrors(emptyResponses, sectionsMissing);
                    return;
                }

                // Update button text based on mode
                const actionText = isUpdateMode ? 'Updating...' : 'Saving...';
                const iconClass = isUpdateMode ? 'fa-edit' : 'fa-save';

                // Show saving indicator
                $('#save-assessment-top, #save-assessment-bottom, #floating-save-btn')
                    .html(
                        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ${actionText}`
                    )
                    .prop('disabled', true);

                // Choose the correct endpoint based on mode
                const endpoint = isUpdateMode ? '/update-safecare-assessment' : '/save-safecare-assessment';

<<<<<<< HEAD
                // Prepare request data with enhanced parameters
=======
                // Prepare request data
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
                const requestData = {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    district_id: selections.district_id,
                    lga_id: selections.lga_id,
                    phc_id: selections.phc_id,
<<<<<<< HEAD
                    responses: responses,
                    quarter: selections.quarter,
                    year: selections.year,
                    assessment_date: selections.assessment_date
=======
                    responses: responses
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
                };

                // Add assessment_id if updating
                if (isUpdateMode && existingAssessmentId) {
                    requestData.assessment_id = existingAssessmentId;
                }

                console.log("Request data:", requestData);
                console.log("Using endpoint:", endpoint);

                // AJAX request to save/update assessment
                $.ajax({
                    url: endpoint,
                    type: 'POST',
                    dataType: 'json',
                    data: requestData,
                    success: function(response) {
                        console.log("Save success:", response);
                        clearSavedProgress();
                        showSuccessMessage(response, isUpdateMode);
                    },
                    error: function(xhr, status, error) {
                        // Reset save buttons
                        const resetText = isUpdateMode ? 'Update Assessment' : 'Save Assessment';
                        $('#save-assessment-top, #save-assessment-bottom, #floating-save-btn')
                            .html(`<i class="fas ${iconClass} me-2"></i>${resetText}`)
                            .prop('disabled', false);

                        console.error("Error:", error);
                        console.error("Response Text:", xhr.responseText);

                        try {
                            var jsonResponse = JSON.parse(xhr.responseText);
                            alert(
                                `Error ${isUpdateMode ? 'updating' : 'saving'} assessment: ${jsonResponse.message}`
                            );
                        } catch (e) {
                            alert(
                                `Error ${isUpdateMode ? 'updating' : 'saving'} assessment. Please check the console for details.`
                            );
                        }
                    }
                });
            }

            function showValidationErrors(emptyResponses, sectionsMissing) {
                var summaryHtml = `
            <div id="validation-summary" class="alert alert-danger">
                <h5><i class="fas fa-exclamation-triangle"></i> Please complete all questions</h5>
                <p>You have ${emptyResponses.length} unanswered question(s) in the following sections:</p>
                <ul>
            `;

                // Add sections with missing responses to summary
                Object.keys(sectionsMissing).forEach(function(section) {
                    var sectionId = section.replace(/\s+/g, '-').toLowerCase();
                    summaryHtml += `<li class="validation-summary-section" data-section="${sectionId}">
                    <i class="fas fa-arrow-right me-2"></i>${section} (${sectionsMissing[section]} question(s))
                </li>`;

                    // Highlight the section card
                    $(`#section-${sectionId}`).addClass('highlighted-section');
                });

                summaryHtml += `
                </ul>
                <div class="mt-3">
                    <button id="review-incomplete" class="btn btn-warning btn-sm">
                        <i class="fas fa-search me-1"></i> Review Incomplete Questions
                    </button>
                </div>
            </div>
            `;

                // Show validation summary
                $('#validation-status').html(summaryHtml).show();

                // Add click handler for section links
                $('.validation-summary-section').on('click', function() {
                    var sectionId = $(this).data('section');
                    scrollToSection(sectionId);
                });

                // Review incomplete button
                $('#review-incomplete').on('click', function() {
                    reviewIncompleteQuestions(emptyResponses);
                });

                // Add highlighting to all empty responses
                emptyResponses.forEach(function(element) {
                    element.addClass('is-invalid');
                    element.parent().append(
                        '<div class="question-error text-danger mt-1"><i class="fas fa-exclamation-circle me-1"></i>This question requires a response</div>'
                    );
                    element.closest('tr').addClass('question-highlight');
                });

                // Scroll to the validation summary
                $('html, body').animate({
                    scrollTop: $('#validation-status').offset().top - 100
                }, 500);
            }

            function reviewIncompleteQuestions(emptyResponses) {
                let currentIndex = 0;

                function goToNextIncomplete() {
                    if (currentIndex < emptyResponses.length) {
                        const currentSelect = emptyResponses[currentIndex];
                        const questionRow = currentSelect.closest('tr');

                        // Scroll to question
                        $('html, body').animate({
                            scrollTop: questionRow.offset().top - 150
                        }, 500);

                        // Highlight current question
                        $('.question-row').removeClass('reviewing');
                        questionRow.addClass('reviewing');

                        // Focus on select
                        setTimeout(() => {
                            currentSelect.focus();
                        }, 600);

                        currentIndex++;

                        // Auto-advance after 3 seconds or when answered
                        const autoAdvance = setTimeout(() => {
                            goToNextIncomplete();
                        }, 3000);

                        // Clear auto-advance if user answers
                        currentSelect.one('change', function() {
                            clearTimeout(autoAdvance);
                            questionRow.removeClass('reviewing');
                            setTimeout(goToNextIncomplete, 500);
                        });
                    } else {
                        // All reviewed, scroll back to validation summary
                        $('.question-row').removeClass('reviewing');
                        $('html, body').animate({
                            scrollTop: $('#validation-status').offset().top - 100
                        }, 500);
                    }
                }

                goToNextIncomplete();
            }

            function scrollToSection(sectionId) {
                var section = $(`#section-${sectionId}`);
                var firstEmptyInSection = section.find('.is-invalid').first();

                if (firstEmptyInSection.length) {
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

            function showSuccessMessage(response, isUpdateMode = false) {
                const assessmentId = response.assessment_id;
                const compliancePercentage = response.compliance_percentage;
                const summary = response.summary;
                const trackingInfo = response.tracking_info;
<<<<<<< HEAD
                const selections = JSON.parse(localStorage.getItem('qip_selections'));
=======

>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee

                const actionText = isUpdateMode ? 'Updated' : 'Completed';
                const actionVerb = isUpdateMode ? 'updated' : 'saved and submitted';

                let successHtml = `
        <div class="alert alert-success mt-4">
            <h4 class="alert-heading">
                <i class="fas fa-check-circle me-2"></i>
                SafeCare Assessment Successfully ${actionText}!
            </h4>
<<<<<<< HEAD
            <p class="mb-3">Your quality improvement assessment for ${selections.quarter} ${selections.year} has been ${actionVerb}.</p>
=======
            <p class="mb-3">Your quality improvement assessment has been ${actionVerb}.</p>
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee

            <!-- Assessment Summary Card -->
            <div class="card mt-3">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Assessment Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="text-center mb-3">
                                <h2 class="text-primary">${compliancePercentage}%</h2>
                                <p class="text-muted mb-0">Overall Compliance Score</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center mb-3">
                                <h3 class="text-info">#${assessmentId}</h3>
                                <p class="text-muted mb-0">Assessment ID</p>
<<<<<<< HEAD
                                <small class="text-muted">${selections.quarter} ${selections.year}</small>
=======
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row text-center">
                        <div class="col-3">
                            <div class="p-2">
                                <h4 class="text-success mb-1">${summary.fully_compliant}</h4>
                                <small class="text-muted">Fully Compliant</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="p-2">
                                <h4 class="text-warning mb-1">${summary.partially_compliant}</h4>
                                <small class="text-muted">Partially Compliant</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="p-2">
                                <h4 class="text-danger mb-1">${summary.not_compliant}</h4>
                                <small class="text-muted">Not Compliant</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="p-2">
                                <h4 class="text-secondary mb-1">${summary.not_applicable}</h4>
                                <small class="text-muted">Not Applicable</small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar"
                                 style="width: ${(summary.fully_compliant/summary.total_questions)*100}%"
                                 title="Fully Compliant: ${summary.fully_compliant}"></div>
                            <div class="progress-bar bg-warning" role="progressbar"
                                 style="width: ${(summary.partially_compliant/summary.total_questions)*100}%"
                                 title="Partially Compliant: ${summary.partially_compliant}"></div>
                            <div class="progress-bar bg-danger" role="progressbar"
                                 style="width: ${(summary.not_compliant/summary.total_questions)*100}%"
                                 title="Not Compliant: ${summary.not_compliant}"></div>
                            <div class="progress-bar bg-secondary" role="progressbar"
                                 style="width: ${(summary.not_applicable/summary.total_questions)*100}%"
                                 title="Not Applicable: ${summary.not_applicable}"></div>
                        </div>
                    </div>
                </div>
<<<<<<< HEAD
            </div>

            <!-- Assessment Information -->
=======
            </div>`;

                // Add comprehensive date and user tracking information with better null checks
                successHtml += `
            <!-- Assessment Tracking Information -->
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
            <div class="card mt-3">
                <div class="card-header ${isUpdateMode ? 'bg-warning text-dark' : 'bg-success text-white'}">
                    <h6 class="card-title mb-0">
                        <i class="fas ${isUpdateMode ? 'fa-history' : 'fa-calendar'} me-2"></i>
<<<<<<< HEAD
                        Assessment Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-2"><strong>Assessment Period:</strong> <span class="badge bg-primary">${selections.quarter} ${selections.year}</span></div>
                            <div class="mb-2"><strong>Assessment Date:</strong> <span class="badge bg-success">${new Date(selections.assessment_date).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            })}</span></div>
                            <div class="mb-2"><strong>Facility:</strong> <span class="badge bg-secondary">${selections.phc_name}</span></div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2"><strong>Assessor:</strong> <span class="badge bg-dark">${trackingInfo && trackingInfo.assessor_name ? trackingInfo.assessor_name : 'Current User'}</span></div>
                            <div class="mb-2"><strong>Status:</strong> <span class="badge ${isUpdateMode ? 'bg-warning text-dark' : 'bg-success'}">${isUpdateMode ? 'Updated' : 'New Assessment'}</span></div>
                            <div class="mb-2"><strong>Created:</strong> <span class="badge bg-info">${trackingInfo && trackingInfo.created_at ? new Date(trackingInfo.created_at).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric'
                            }) + ' at ' + new Date(trackingInfo.created_at).toLocaleTimeString('en-US', {
                                hour: '2-digit',
                                minute: '2-digit'
                            }) : 'Just now'}</span></div>
                        </div>
                    </div>
=======
                        ${isUpdateMode ? 'Assessment History & Tracking' : 'Assessment Information'}
                    </h6>
                </div>
                <div class="card-body">`;

                if (isUpdateMode && trackingInfo) {
                    // For updates - show both original and update information with better null checks
                    const assessmentDate = trackingInfo.assessment_date ? new Date(trackingInfo.assessment_date) :
                        null;
                    const createdDate = trackingInfo.created_at ? new Date(trackingInfo.created_at) : null;
                    const lastUpdatedDate = trackingInfo.last_updated_date ? new Date(trackingInfo
                        .last_updated_date) : null;

                    successHtml += `
                    <div class="row">
                        <div class="col-md-6">
                            <div class="tracking-section">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-user-plus me-2"></i>Original Assessment
                                </h6>
                                <div class="ms-3">
                                    <div class="mb-2">
                                        <strong>Original Assessor:</strong>
                                        <span class="badge bg-primary ms-2">${trackingInfo.original_assessor || 'Unknown'}</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Assessment Date:</strong>
                                        <span class="badge bg-info ms-2">${assessmentDate ? assessmentDate.toLocaleDateString('en-US', {
                                            year: 'numeric',
                                            month: 'long',
                                            day: 'numeric'
                                        }) : 'Not Available'}</span>
                                    </div>
                                    <div class="mb-0">
                                        <strong>Created On:</strong>
                                        <span class="badge bg-secondary ms-2">${createdDate ? createdDate.toLocaleDateString('en-US', {
                                            year: 'numeric',
                                            month: 'short',
                                            day: 'numeric'
                                        }) + ' at ' + createdDate.toLocaleTimeString('en-US', {
                                            hour: '2-digit',
                                            minute: '2-digit'
                                        }) : 'Not Available'}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="tracking-section">
                                <h6 class="text-warning mb-3">
                                    <i class="fas fa-user-edit me-2"></i>Latest Update
                                </h6>
                                <div class="ms-3">
                                    <div class="mb-2">
                                        <strong>Updated by:</strong>
                                        <span class="badge bg-warning text-dark ms-2">${trackingInfo.updated_by || 'Current User'}</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Update Date:</strong>
                                        <span class="badge bg-success ms-2">${lastUpdatedDate ? lastUpdatedDate.toLocaleDateString('en-US', {
                                            year: 'numeric',
                                            month: 'long',
                                            day: 'numeric'
                                        }) : 'Today'}</span>
                                    </div>
                                    <div class="mb-0">
                                        <strong>Update Time:</strong>
                                        <span class="badge bg-dark ms-2">${lastUpdatedDate ? lastUpdatedDate.toLocaleTimeString('en-US', {
                                            hour: '2-digit',
                                            minute: '2-digit',
                                            second: '2-digit'
                                        }) : new Date().toLocaleTimeString('en-US', {
                                            hour: '2-digit',
                                            minute: '2-digit',
                                            second: '2-digit'
                                        })}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    </div>`;
                } else {
                    // For new assessments - show single assessment information with better date handling
                    const assessmentDate = trackingInfo && trackingInfo.assessment_date ? new Date(trackingInfo
                        .assessment_date) : new Date();
                    const createdDate = trackingInfo && trackingInfo.created_at ? new Date(trackingInfo
                        .created_at) : new Date();

                    successHtml += `
                    <div class="row">
                        <div class="col-md-6">
                            <div class="tracking-section">
                                <h6 class="text-success mb-3">
                                    <i class="fas fa-calendar-plus me-2"></i>Assessment Information
                                </h6>
                                <div class="ms-3">
                                    <div class="mb-2">
                                        <strong>Assessment Date:</strong>
                                        <span class="badge bg-success ms-2">${assessmentDate.toLocaleDateString('en-US', {
                                            year: 'numeric',
                                            month: 'long',
                                            day: 'numeric'
                                        })}</span>
                                    </div>
                                    <div class="mb-0">
                                        <strong>Created On:</strong>
                                        <span class="badge bg-secondary ms-2">${createdDate.toLocaleDateString('en-US', {
                                            year: 'numeric',
                                            month: 'short',
                                            day: 'numeric'
                                        })} at ${createdDate.toLocaleTimeString('en-US', {
                                            hour: '2-digit',
                                            minute: '2-digit'
                                        })}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="tracking-section">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-user me-2"></i>Assessor Information
                                </h6>
                                <div class="ms-3">
                                    <div class="mb-2">
                                        <strong>Assessed by:</strong>
                                        <span class="badge bg-primary ms-2">${trackingInfo && trackingInfo.assessor_name ? trackingInfo.assessor_name : 'Current User'}</span>
                                    </div>
                                    <div class="mb-0">
                                        <strong>Assessment ID:</strong>
                                        <span class="badge bg-info ms-2">#${assessmentId}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-success mt-3 mb-0">
                        <div class="row align-items-center">
                            <div class="col-md-1 text-center">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                            <div class="col-md-11">
                                <h6 class="mb-1"><strong>New Assessment Created</strong></h6>
                                <p class="mb-0">
                                    This assessment was successfully created on ${assessmentDate.toLocaleDateString('en-US', {
                                        year: 'numeric',
                                        month: 'long',
                                        day: 'numeric'
                                    })} at ${createdDate.toLocaleTimeString('en-US', {
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    })}.
                                </p>
                            </div>
                        </div>
                    </div>`;
                }

                successHtml += `
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex gap-2 flex-wrap">
                <button id="new-assessment" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Start New Assessment
                </button>
                <button id="view-history" class="btn btn-outline-info">
                    <i class="fas fa-history me-2"></i>View Assessment History
                </button>
                <button id="download-report" class="btn btn-outline-success">
                    <i class="fas fa-download me-2"></i>Download Report
                </button>
            </div>
        </div>
    `;

                // Replace the assessment container with the success message
                $('#assessment-container').replaceWith(successHtml);
                $('#floating-save-btn').hide();

                // Scroll to top to show the success message
                $('html, body').animate({
                    scrollTop: 0
                }, 500);

                // Handle action buttons
                $('#new-assessment').on('click', function() {
                    clearSavedProgress();
                    localStorage.removeItem('qip_selections');
                    window.location.reload();
                });

                $('#view-history').on('click', function() {
                    alert('Assessment history feature coming soon!');
                });

                $('#download-report').on('click', function() {
                    alert('Download report feature coming soon!');
                });
            }
<<<<<<< HEAD

            // Initialize the current quarter based on today's date
            function initializeCurrentQuarter() {
                const today = new Date();
                const month = today.getMonth() + 1; // getMonth() returns 0-11
                const year = today.getFullYear();

                let currentQuarter;
                if (month >= 1 && month <= 3) {
                    currentQuarter = 'Q1';
                } else if (month >= 4 && month <= 6) {
                    currentQuarter = 'Q2';
                } else if (month >= 7 && month <= 9) {
                    currentQuarter = 'Q3';
                } else {
                    currentQuarter = 'Q4';
                }

                console.log('Initializing current quarter:', currentQuarter, 'year:', year);

                // Set current quarter as default with a slight delay to ensure DOM is ready
                setTimeout(() => {
                    $(`.quarter-option[data-quarter="${currentQuarter}"]`).trigger('click');
                }, 100);

                // Set current year
                currentYear = year;
                $('#current-year').text(currentYear);
                $('#selected_year').val(currentYear);

                // Set today's date as default assessment date
                const todayStr = today.toISOString().split('T')[0];
                $('#assessment_date').val(todayStr);

                // Update constraints
                setTimeout(() => {
                    updateAssessmentDateConstraints();
                    validateForm();
                }, 200);
            }

            // Initialize defaults when modal is shown
            $('#qipModal').on('show.bs.modal', function() {
                console.log('Modal showing, initializing...');

                // Reset form first
                $('#qip-selection-form')[0].reset();
                $('.quarter-option').removeClass('selected');
                $('#assessment-summary').hide();
                $('.form-control').removeClass('is-valid is-invalid');
                $('.validation-message').hide();
                $('.quarter-grid').removeClass('border border-danger');
                $('.year-selector').removeClass('border border-danger rounded p-2');
                $('#submit-selection').prop('disabled', true);

                setTimeout(() => {
                    initializeCurrentQuarter();
                }, 300);
            });

            $('#qipModal').on('hidden.bs.modal', function() {
                console.log('Modal hidden, resetting...');
                $('#qip-selection-form')[0].reset();
                $('.quarter-option').removeClass('selected');
                $('#assessment-summary').hide();
                $('.form-control').removeClass('is-valid is-invalid');
                $('.validation-message').hide();
                $('.quarter-grid').removeClass('border border-danger');
                $('.year-selector').removeClass('border border-danger rounded p-2');
                $('#submit-selection').prop('disabled', true);
                $('#selected_quarter').val('');
                $('#selected_year').val('');
            });
        });
    </script>

=======
        });
    </script>


>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
