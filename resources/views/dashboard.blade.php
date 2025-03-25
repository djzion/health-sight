
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Primary Health Care Dashboard</title>

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
    }
    </style>
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
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Access Denied:</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Header -->
        <div class="header">
            <div>
                <h1>Welcome, {{ Auth::user()->full_name }}</h1>
                <p class="text-muted">Here’s an overview of your day.</p>
            </div>
            <div class="profile">
                <img src="https://via.placeholder.com/50" alt="User Profile">
                <span>{{ Auth::user()->full_name }}</span>
            </div>
        </div>

        <!-- Metrics -->
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

        <!-- Upcoming Appointments -->
        <h2 class="mt-5 mb-3">Upcoming Appointments</h2>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Patient</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
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
        @else

        @endif
    </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
