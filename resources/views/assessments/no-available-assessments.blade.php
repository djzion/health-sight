<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment - Not Available</title>

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
            width: calc(100% - 250px) !important;
        }

        .btn-primary {
            background-color: #0199dc !important;
        }

        .status-icon {
            font-size: 4rem;
            color: #6c757d;
            margin-bottom: 1.5rem;
        }

        .status-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            background: white;
        }

        .period-info {
            background: linear-gradient(135deg, #0199dc, #017bb8);
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1rem 0;
        }

        .countdown-box {
            background: #f8f9fa;
            border-left: 4px solid #0199dc;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
        }

        .location-info {
            background: #e8f4fd;
            border: 1px solid #0199dc;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
        }

        .edit-window-expired {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1rem 0;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
                padding: 15px;
            }

            .sidebar h2 {
                display: none;
            }

            .main-content {
                margin-left: 60px;
                width: calc(100% - 60px) !important;
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
    <div class="main-content">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card status-card">
                        <div class="card-body text-center py-5">
                            @if($title === 'Assessment Complete' || str_contains($message ?? '', 'successfully'))
                                <i class="fas fa-check-circle status-icon text-success"></i>
                                <h2 class="mb-4 text-success">{{ $title ?? 'Assessment Complete' }}</h2>
                            @elseif(str_contains($message ?? '', 'expired') || str_contains($message ?? '', 'edit'))
                                <i class="fas fa-clock status-icon text-warning"></i>
                                <h2 class="mb-4 text-warning">{{ $title ?? 'Assessment Edit Window Expired' }}</h2>
                            @else
                                <i class="fas fa-file-medical-alt status-icon"></i>
                                <h2 class="mb-4">{{ $title ?? 'Assessment Not Available' }}</h2>
                            @endif

                            <!-- Location Information (for Directors) -->
                            @if($district && $lga && $phc)
                                <div class="location-info">
                                    <h6><i class="fas fa-map-marker-alt me-2"></i>Assessment Location</h6>
                                    <p class="mb-0">
                                        <strong>District:</strong> {{ $district }} |
                                        <strong>LGA:</strong> {{ $lga }} |
                                        <strong>PHC:</strong> {{ $phc }}
                                    </p>
                                </div>
                            @endif

                            <!-- Edit Window Expired Scenario -->
                            @if(str_contains($message ?? '', 'expired') || str_contains($message ?? '', 'edit'))
                                <div class="edit-window-expired">
                                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Edit Period Has Ended</h5>
                                    <p class="mb-0">
                                        Your assessment can no longer be modified as it has been more than 7 days since submission.
                                    </p>
                                </div>

                                <div class="alert alert-info mt-4">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Why can't I edit?</strong><br>
                                    Assessment responses can only be edited within 7 days of submission to maintain data integrity and ensure timely reporting.
                                </div>
                            @endif

                            <!-- Next Assessment Period -->
                            @if($nextAvailableDate)
                                <div class="period-info">
                                    <h5><i class="fas fa-calendar-alt me-2"></i>Next Assessment Period</h5>
                                    <p class="mb-0">
                                        <strong>{{ $nextAvailableDate->format('F d, Y') }}</strong>
                                        <br>
                                        <small>{{ $nextAvailableDate->diffForHumans() }}</small>
                                    </p>
                                </div>

                                @if($daysRemaining !== null && $daysRemaining > 0)
                                    <div class="countdown-box">
                                        <h6 class="mb-2">
                                            <i class="fas fa-clock me-2"></i>Time Remaining
                                        </h6>
                                        <p class="mb-0">
                                            <strong>{{ $daysRemaining }}</strong>
                                            day{{ $daysRemaining != 1 ? 's' : '' }} remaining
                                        </p>
                                    </div>
                                @endif

                                <div class="alert alert-info mt-4">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Primary Healthcare assessments are conducted periodically. The next assessment window will open on the date shown above.
                                </div>
                            @else
                                <div class="alert alert-warning mt-4">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    No assessment periods are currently scheduled. Please contact your administrator.
                                </div>
                            @endif

                            <!-- Custom Message -->
                            @if($message)
                                <div class="alert alert-secondary mt-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    {{ $message }}
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-center gap-3 mt-4">
                                @if(auth()->user()->role->name === 'director')
                                    <a href="{{ route('assessments.reset-location') }}" class="btn btn-primary">
                                        <i class="fas fa-exchange-alt me-2"></i>Change Location
                                    </a>
                                @endif

                                <a href="{{ route('assessments.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Assessment
                                </a>

                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-home me-2"></i>Dashboard
                                </a>
                            </div>

                            <!-- Additional Information -->
                            <div class="mt-5">
                                <h6 class="text-muted">About PHC Assessments</h6>
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <i class="fas fa-calendar-check text-primary mb-2" style="font-size: 2rem;"></i>
                                            <h6>Regular Schedule</h6>
                                            <p class="small text-muted">Assessments are conducted according to your facility's schedule</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <i class="fas fa-edit text-success mb-2" style="font-size: 2rem;"></i>
                                            <h6>7-Day Edit Window</h6>
                                            <p class="small text-muted">You can modify your assessment within 7 days of submission</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <i class="fas fa-chart-bar text-warning mb-2" style="font-size: 2rem;"></i>
                                            <h6>Quality Tracking</h6>
                                            <p class="small text-muted">Monitor and improve healthcare service delivery</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
