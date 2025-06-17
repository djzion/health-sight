<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SafeCare Assessment - Not Available</title>

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
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1rem 0;
        }

        .countdown-box {
            background: #f8f9fa;
            border-left: 4px solid #3498db;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
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
        <div class="main-content">
            <div class="container py-4">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card status-card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-shield-alt status-icon"></i>
                                <h2 class="mb-4">SafeCare Assessment Not Available</h2>

                                @if($nextAvailableDate)
                                <div class="period-info">
                                    <h5><i class="fas fa-calendar-alt me-2"></i>Next Assessment Period</h5>
                                    <p class="mb-0">
                                        <strong>{{ \Carbon\Carbon::parse($nextAvailableDate)->format('F d, Y') }}</strong>
                                        <br>
                                        <small>{{ \Carbon\Carbon::parse($nextAvailableDate)->diffForHumans() }}</small>
                                    </p>
                                </div>

                                @if($daysRemaining !== null)
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
                                    SafeCare assessments are conducted quarterly. The next assessment window will open on the date shown above.
                                </div>
                                @else
                                <div class="alert alert-warning mt-4">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    No SafeCare assessment periods are currently scheduled. Please contact your administrator.
                                </div>
                                @endif

                                @if($info)
                                <div class="alert alert-secondary mt-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    {{ $info }}
                                </div>
                                @endif

                                <div class="d-flex justify-content-center gap-3 mt-4">
                                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                                    </a>
                                    <!-- <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-home me-2"></i>Dashboard
                                </a> -->
                                </div>

                                <!-- Additional Information -->
                                <div class="mt-5">
                                    <h6 class="text-muted">About SafeCare Assessments</h6>
                                    <div class="row mt-3">
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <i class="fas fa-calendar-check text-primary mb-2" style="font-size: 2rem;"></i>
                                                <h6>Quarterly Schedule</h6>
                                                <p class="small text-muted">Assessments are conducted every quarter (Q1, Q2, Q3, Q4)</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <i class="fas fa-edit text-success mb-2" style="font-size: 2rem;"></i>
                                                <h6>7-Day Edit Window</h6>
                                                <p class="small text-muted">You can edit your assessment within 7 days of submission</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <i class="fas fa-chart-bar text-warning mb-2" style="font-size: 2rem;"></i>
                                                <h6>Quality Improvement</h6>
                                                <p class="small text-muted">Track compliance and improve healthcare quality standards</p>
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
