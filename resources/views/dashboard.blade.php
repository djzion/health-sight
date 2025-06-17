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

        /* Director Assessment Card Enhancements */
        .card.bg-primary {
            background: linear-gradient(135deg, #0199dc, #0181b8) !important;
            box-shadow: 0 4px 15px rgba(1, 153, 220, 0.3);
            border: none;
        }

        .card.bg-primary:hover {
            transform: translateY(-2px);
            transition: transform 0.3s ease;
            box-shadow: 0 6px 20px rgba(1, 153, 220, 0.4);
        }

        .card.bg-primary .btn-light {
            background-color: #fff;
            border: none;
            color: #0199dc;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .card.bg-primary .btn-light:hover {
            background-color: #f8f9fa;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Director Stats Cards */
        .director-stats .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            background: #fff;
        }

        .director-stats .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        .director-stats .card-body {
            padding: 1.5rem;
        }

        .director-stats .fas {
            opacity: 0.8;
        }

        /* Icon animation */
        .card.bg-primary .fas.fa-map-marker-alt {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        /* Modal Styles */
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
                width: calc(100% - 60px) !important;
                padding: 15px;
            }

            /* Responsive adjustments for director card */
            .card.bg-primary .card-body {
                padding: 1.25rem;
            }

            .card.bg-primary .btn-lg {
                padding: 0.75rem 1.5rem;
                font-size: 1rem;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
                margin-bottom: 20px;
            }

            .header .profile {
                margin-top: 10px;
            }

            .metrics {
                margin-bottom: 20px;
            }

            .director-stats .col-md-3 {
                margin-bottom: 15px;
            }
        }

        /* PowerBI iframe styles */
        .powerbi-container {
            width: 100%;
            height: 100vh;
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .powerbi-modal .modal-dialog {
            max-width: 95%;
            height: 90vh;
        }

        .powerbi-modal .modal-content {
            height: 100%;
        }

        .powerbi-modal .modal-body {
            padding: 0;
            height: calc(100% - 120px);
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
        <a href="#"><i class="fas fa-home me-2"></i> Dashboard Home</a>
        <a href="#"><i class="fas fa-user-injured me-2"></i> Patients</a>
        <a href="#"><i class="fas fa-calendar-alt me-2"></i> Appointments</a>
        <a href="{{ route('assessments.index') }}"><i class="fas fa-file-medical-alt me-2"></i> Assessment</a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#qipModal"><i class="fas fa-file-medical-alt me-2"></i> Quality Improvement</a>
        <a href="#"><i class="fas fa-tasks me-2"></i> Tasks</a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#powerbiModal"><i class="fas fa-chart-line me-2"></i> Reports</a>
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

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Header -->
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

        <!-- Director Assessment Mode -->
        @if(auth()->user()->role->name === 'director')
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                            <div class="mb-3 mb-md-0">
                                <h5 class="card-title mb-1">
                                    <i class="fas fa-user-shield me-2"></i>Director Assessment Mode
                                </h5>
                                <p class="card-text mb-1">Conduct assessments for any PHC in your district</p>
                                <small class="text-light opacity-75 d-none d-md-block">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Location resets after each submission for new assessments
                                </small>
                            </div>
                            <div class="align-self-stretch align-self-md-auto">
                                <a href="{{ route('assessments.index') }}" class="btn btn-light btn-lg w-100 w-md-auto">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    <span class="d-none d-sm-inline">Select PHC & </span>Start Assessment
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Director Statistics -->
        <div class="row mb-4 director-stats">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-hospital fa-2x text-primary mb-2"></i>
                        <h4 class="mb-1">{{ $totalPhcs ?? '12' }}</h4>
                        <small class="text-muted">PHCs in District</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <h4 class="mb-1">{{ $completedAssessments ?? '8' }}</h4>
                        <small class="text-muted">Completed This Month</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                        <h4 class="mb-1">{{ $pendingAssessments ?? '4' }}</h4>
                        <small class="text-muted">Pending Assessments</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-calendar-check fa-2x text-info mb-2"></i>
                        <h4 class="mb-1">{{ $thisWeekCount ?? '3' }}</h4>
                        <small class="text-muted">This Week</small>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Regular User Metrics -->
        @if(auth()->user()->role->name !== 'director')
        <div class="metrics row g-3">
            <div class="col-md-3">
                <div class="card p-4 text-center">
                    <h3>15</h3>
                    <p>Patients Today</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-4 text-center">
                    <h3>8</h3>
                    <p>Scheduled Appointments</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-4 text-center">
                    <h3>5</h3>
                    <p>Tasks Pending</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-4 text-center">
                    <h3>2</h3>
                    <p>Emergency Cases</p>
                </div>
            </div>
        </div>
        @else
        <!-- Director also gets general metrics -->
        <div class="metrics row g-3">
            <div class="col-md-3">
                <div class="card p-4 text-center">
                    <h3>45</h3>
                    <p>Total Patients Today</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-4 text-center">
                    <h3>28</h3>
                    <p>District Appointments</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-4 text-center">
                    <h3>12</h3>
                    <p>Active PHCs</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-4 text-center">
                    <h3>6</h3>
                    <p>Emergency Cases</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Upcoming Appointments -->
        <h2 class="mt-5 mb-3">
            @if(auth()->user()->role->name === 'director')
                District Overview
            @else
                Upcoming Appointments
            @endif
        </h2>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        @if(auth()->user()->role->name === 'director')
                            <th>PHC</th>
                            <th>Status</th>
                            <th>Last Assessment</th>
                            <th>Action</th>
                        @else
                            <th>Time</th>
                            <th>Patient</th>
                            <th>Reason</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @if(auth()->user()->role->name === 'director')
                        <tr>
                            <td>Central PHC Ibadan</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>2 days ago</td>
                            <td>
                                <a href="{{ route('assessments.index') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-clipboard-check me-1"></i>Assess
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>Agodi PHC</td>
                            <td><span class="badge bg-warning">Pending</span></td>
                            <td>1 week ago</td>
                            <td>
                                <a href="{{ route('assessments.index') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-clipboard-check me-1"></i>Assess
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>Mokola PHC</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>Yesterday</td>
                            <td>
                                <a href="{{ route('assessments.index') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-clipboard-check me-1"></i>Assess
                                </a>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td>10:00 AM</td>
                            <td>John Doe</td>
                            <td>Routine Checkup</td>
                        </tr>
                        <tr>
                            <td>11:30 AM</td>
                            <td>Jane Smith</td>
                            <td>Follow-up</td>
                        </tr>
                        <tr>
                            <td>2:00 PM</td>
                            <td>Mike Johnson</td>
                            <td>Vaccination</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Assessment Section -->
        @if (isset($currentSection))
            <div class="card assessment-card p-4 mt-5">
                <h3 class="mb-4">Assessment for {{ Auth::user()->role->name }}</h3>
                <h5 class="text-primary">{{ $currentSection->name }}</h5>

                <form action="{{ route('assessment.submit') }}" method="POST">
                    @csrf
                    <input type="hidden" name="section_id" value="{{ $currentSection->id }}">

                    @foreach ($currentSection->questions as $question)
                        <div class="mb-4">
                            <h6>{{ $question->content }}</h6>

                            @if ($question->type === 'yes_no')
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" value="yes" required>
                                    <label class="form-check-label">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" value="no" required>
                                    <label class="form-check-label">No</label>
                                </div>
                            @elseif ($question->type === 'text')
                                <textarea class="form-control mt-2" name="answers[{{ $question->id }}]" rows="3" required></textarea>
                            @endif
                        </div>
                    @endforeach

                    <div class="d-flex justify-content-between">
                        @if ($previousSection)
                            <a href="{{ route('assessment.section', $previousSection->id) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Previous
                            </a>
                        @endif

                        <button type="submit" class="btn btn-primary">
                            {{ $nextSection ? 'Next Section' : 'Complete Assessment' }} <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>

<!-- QIP Modal -->
<div class="modal fade" id="qipModal" tabindex="-1" aria-labelledby="qipModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qipModalLabel">Quality Improvement Assessment for PHCs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="qip-selection-form">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="district_id">District</label>
                        <select id="district_id" name="district_id" required>
                            <option value="">Select District</option>
                            @foreach ($districts ?? [] as $district)
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

<!-- PowerBI Reports Modal -->
<div class="modal fade powerbi-modal" id="powerbiModal" tabindex="-1" aria-labelledby="powerbiModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="powerbiModalLabel">
                    <i class="fas fa-chart-line me-2"></i>Health Data Analytics Dashboard
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe
                    src="https://app.powerbi.com/view?r=eyJrIjoiYWM4NDZmZjMtYWU5OC00OWYwLTgxNWEtODM1NzgwYWQxYTg4IiwidCI6IjJkZjMyNDhlLTc5YTItNGI5NC1iNzM1LTA1NTZkMTVhNTkzZiJ9&pageName=f87eebf6820a8b10ad58"
                    class="powerbi-container"
                    frameborder="0"
                    allowfullscreen="true">
                </iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="https://app.powerbi.com/view?r=eyJrIjoiYWM4NDZmZjMtYWU5OC00OWYwLTgxNWEtODM1NzgwYWQxYTg4IiwidCI6IjJkZjMyNDhlLTc5YTItNGI5NC1iNzM1LTA1NTZkMTVhNTkzZiJ9&pageName=f87eebf6820a8b10ad58"
                   target="_blank"
                   class="btn btn-primary">
                    <i class="fas fa-external-link-alt me-2"></i>Open in New Tab
                </a>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Setup AJAX with CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
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
                },
                error: function(xhr, status, error) {
                    console.error('Error loading LGAs:', error);
                    alert('Error loading Local Government Areas. Please try again.');
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
                },
                error: function(xhr, status, error) {
                    console.error('Error loading PHCs:', error);
                    alert('Error loading Primary Health Care centers. Please try again.');
                }
            });
        }
    });

    // Handle form submission - store values in localStorage and redirect to QIP index
    $('#submit-selection').on('click', function() {
        var district_id = $('#district_id').val();
        var lga_id = $('#lga_id').val();
        var phc_id = $('#phc_id').val();

        // Validate selections
        if (!district_id || !lga_id || !phc_id) {
            alert('Please select District, LGA, and PHC');
            return;
        }

        // Show loading state
        $(this).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...').prop('disabled', true);

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

        // Close modal and redirect to QIP index page
        var modal = bootstrap.Modal.getInstance(document.getElementById('qipModal'));
        modal.hide();

        // Add a small delay to ensure modal closes properly, then redirect
        setTimeout(function() {
            window.location.href = "{{ route('qip.index') }}";
        }, 300);
    });

    // Reset modal state when closed
    $('#qipModal').on('hidden.bs.modal', function() {
        $('#submit-selection').html('<i class="fas fa-save me-2"></i> Load Assessment').prop('disabled', false);
        $('#qip-selection-form')[0].reset();
        $('#lga_id').html('<option value="">Select Local Government Area</option>');
        $('#phc_id').html('<option value="">Select Primary Health Care (PHC)</option>');
    });

    // PowerBI Modal handling
    $('#powerbiModal').on('shown.bs.modal', function() {
        // Optional: You can add any PowerBI-specific initialization here
        console.log('PowerBI Dashboard opened');
    });

    $('#powerbiModal').on('hidden.bs.modal', function() {
        // Optional: Clean up when modal is closed
        console.log('PowerBI Dashboard closed');
    });
});
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
