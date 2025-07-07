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
        /* Base Styles */
        :root {
            --primary-color: #0199dc;
            --primary-dark: #0181b8;
            --sidebar-color: #2c3e50;
            --sidebar-hover: #34495e;
            --success-color: #28a745;
            --success-light: #20c997;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-bg: #f1f5f9;
            --sidebar-width: 250px;
            --mobile-header-height: 60px;
            --transition: all 0.3s ease;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background-color: var(--light-bg);
            color: #333;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* Mobile Header */
        .mobile-header {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--mobile-header-height);
            background: linear-gradient(135deg, var(--sidebar-color), var(--sidebar-hover));
            color: white;
            z-index: 1050;
            padding: 0 1rem;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .hamburger-menu {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.25rem;
            transition: var(--transition);
        }

        .hamburger-menu:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .mobile-title {
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .mobile-title i {
            margin-right: 0.5rem;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--sidebar-color), var(--sidebar-hover));
            color: white;
            padding: 1.5rem 1rem;
            z-index: 1040;
            overflow-y: auto;
            overflow-x: hidden;
            transition: var(--transition);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar-brand {
            margin-bottom: 2rem;
            padding: 0 0.5rem;
        }

        .sidebar-brand h2 {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .sidebar-brand i {
            margin-right: 0.75rem;
            font-size: 1.5rem;
        }

        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-nav-item {
            margin-bottom: 0.25rem;
        }

        .sidebar-nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            border-radius: 0.5rem;
            transition: var(--transition);
            font-size: 0.95rem;
            font-weight: 500;
        }

        .sidebar-nav-link:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateX(5px);
        }

        .sidebar-nav-link i {
            margin-right: 0.75rem;
            width: 1.25rem;
            text-align: center;
            font-size: 1rem;
        }

        .sidebar-logout {
            margin-top: auto;
            padding-top: 2rem;
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1035;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-backdrop.show {
            display: block;
            opacity: 1;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            padding: 2rem;
            transition: var(--transition);
            position: relative;
        }

        /* Location Banner */
        .location-banner {
            position: sticky;
            top: 0;
            z-index: 1020;
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 0.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .location-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .location-icon {
            width: 3rem;
            height: 3rem;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .location-details {
            flex: 1;
        }

        .location-title {
            font-weight: 600;
            color: var(--sidebar-color);
            margin-bottom: 0.5rem;
        }

        .location-items {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            font-size: 0.9rem;
        }

        .location-item {
            color: #666;
        }

        .location-value {
            color: var(--primary-color);
            font-weight: 600;
        }

        .location-period {
            margin-top: 0.5rem;
            font-size: 0.85rem;
            color: #666;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .btn-change-location {
            background: var(--primary-color);
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: var(--transition);
            white-space: nowrap;
        }

        .btn-change-location:hover {
            background: var(--primary-dark);
            color: white;
            transform: translateY(-1px);
        }

        /* Header */
        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 600;
            color: var(--sidebar-color);
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: #666;
            margin-bottom: 0;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.25rem;
            border: 2px solid white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

<<<<<<< HEAD
        .user-name {
            font-weight: 500;
            color: var(--sidebar-color);
        }

        /* Cards */
        .custom-card {
            background: white;
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            transition: var(--transition);
            overflow: hidden;
        }

        .custom-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
        }

        .director-card {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
        }

        .quality-card {
            background: linear-gradient(135deg, var(--success-color), var(--success-light));
            color: white;
        }

        .metric-card {
            background: linear-gradient(135deg, #3498db, #6dd5ed);
            color: white;
            text-align: center;
            padding: 1.5rem;
        }

        .metric-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .metric-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .stats-card {
            text-align: center;
            padding: 1.5rem;
        }

        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            opacity: 0.8;
        }

        .stats-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stats-label {
            font-size: 0.85rem;
            color: #666;
            text-transform: uppercase;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        /* Table */
        .custom-table {
            background: white;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: var(--sidebar-color);
            color: white;
            border: none;
            font-weight: 600;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            border-color: #f8f9fa;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Badges */
        .badge {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 0.375rem;
        }

        /* Buttons */
        .btn {
            border-radius: 0.375rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: 0.5rem;
            border-left: 4px solid;
        }

        .alert-success {
            border-left-color: var(--success-color);
            background-color: #d4edda;
        }

        .alert-danger {
            border-left-color: var(--danger-color);
            background-color: #f8d7da;
        }

        /* Mobile Styles */
        @media (max-width: 991.98px) {
            .mobile-header {
                display: flex;
            }

=======
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
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
            .sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-width);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
<<<<<<< HEAD
                margin-left: 0;
                padding-top: calc(var(--mobile-header-height) + 1rem);
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .location-banner {
                margin-top: 1rem;
                padding: 1rem;
            }

            .location-info {
                flex-direction: column;
                align-items: stretch;
                gap: 1rem;
            }

            .location-details {
                order: 1;
            }

            .btn-change-location {
                order: 2;
                align-self: flex-start;
            }

            .location-items {
                flex-direction: column;
                gap: 0.5rem;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .user-profile {
                margin-top: 0.5rem;
            }

            .metric-value {
                font-size: 2rem;
            }

            .stats-value {
                font-size: 1.5rem;
            }

            .stats-icon {
                font-size: 2rem;
            }
        }

        @media (max-width: 575.98px) {
            .main-content {
                padding: calc(var(--mobile-header-height) + 0.5rem) 0.5rem 1rem;
            }

            .location-banner {
                margin: 0.5rem 0 1rem;
                padding: 0.75rem;
            }

            .location-icon {
                width: 2.5rem;
                height: 2.5rem;
                font-size: 1rem;
            }

            .page-title {
                font-size: 1.25rem;
            }

            .user-avatar {
                width: 2.5rem;
                height: 2.5rem;
                font-size: 1rem;
            }

            .metric-card {
                padding: 1rem;
            }

            .metric-value {
                font-size: 1.75rem;
            }

            .stats-card {
                padding: 1rem;
            }

            .table thead th,
            .table tbody td {
                padding: 0.75rem 0.5rem;
                font-size: 0.9rem;
            }
        }

        /* Utilities */
        .text-primary-custom {
            color: var(--primary-color) !important;
        }

        .bg-primary-custom {
            background-color: var(--primary-color) !important;
        }

        .border-primary-custom {
            border-color: var(--primary-color) !important;
        }

        /* Animation */
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        /* Scroll behavior */
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 2px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Modal fixes */
        .modal-backdrop {
            z-index: 1055;
        }

        .modal {
            z-index: 1060;
        }

        /* Prevent body scroll when sidebar is open on mobile */
        body.sidebar-open {
            overflow: hidden;
        }

        @media (max-width: 991.98px) {
            body.sidebar-open .main-content {
                pointer-events: none;
            }
        }
=======
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
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
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
    <!-- Mobile Header -->
    <div class="mobile-header">
        <button class="hamburger-menu" id="sidebarToggle" aria-label="Toggle sidebar">
            <i class="fas fa-bars"></i>
        </button>
        <div class="mobile-title">
            <i class="fas fa-hospital-user"></i>
            Health Dashboard
        </div>
        <div style="width: 2rem;"></div>
    </div>

    <!-- Sidebar Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Sidebar -->
<<<<<<< HEAD
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h2>
                <i class="fas fa-hospital-user"></i>
                Health Dashboard
            </h2>
        </div>
=======
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
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee

        <nav>
            <ul class="sidebar-nav">
                <li class="sidebar-nav-item">
                    <a href="#" class="sidebar-nav-link">
                        <i class="fas fa-home"></i>
                        Dashboard Home
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="#" class="sidebar-nav-link">
                        <i class="fas fa-user-injured"></i>
                        Patients
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="#" class="sidebar-nav-link">
                        <i class="fas fa-calendar-alt"></i>
                        Appointments
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('assessments.index') }}" class="sidebar-nav-link">
                        <i class="fas fa-file-medical-alt"></i>
                        Assessment
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('qip.index') }}?auto_open=true" class="sidebar-nav-link" onclick="localStorage.setItem('auto_open_qip_modal', 'true');">
                        <i class="fas fa-chart-line"></i>
                        SafeCare
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="#" class="sidebar-nav-link">
                        <i class="fas fa-tasks"></i>
                        Tasks
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="#" class="sidebar-nav-link" data-bs-toggle="modal" data-bs-target="#powerbiModal">
                        <i class="fas fa-chart-bar"></i>
                        Reports
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="#" class="sidebar-nav-link">
                        <i class="fas fa-envelope"></i>
                        Messages
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="#" class="sidebar-nav-link">
                        <i class="fas fa-cog"></i>
                        Settings
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-logout">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light w-100">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
<<<<<<< HEAD
    <div class="main-content">
        <!-- Location Banner -->
        @if (session('assessment_location_selected'))
        <div class="location-banner">
            <div class="location-info">
                <div class="location-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="location-details">
                    <div class="location-title">Currently Assessing</div>
                    <div class="location-items">
                        <div class="location-item">
                            District: <span class="location-value">{{ $district ?? 'Unknown' }}</span>
                        </div>
                        <div class="location-item">
                            LGA: <span class="location-value">{{ $lga ?? 'Unknown' }}</span>
                        </div>
                        <div class="location-item">
                            PHC: <span class="location-value">{{ $phc ?? 'Unknown' }}</span>
=======
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
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee
                        </div>
                    </div>
                    @if(session('assessment_quarter') && session('assessment_year'))
                    <div class="location-period">
                        <div>
                            <i class="fas fa-calendar me-1"></i>
                            Period: <strong>{{ session('assessment_quarter') }} {{ session('assessment_year') }}</strong>
                        </div>
                        @if(session('assessment_date'))
                        <div>
                            <i class="fas fa-clock me-1"></i>
                            Date: <strong>{{ \Carbon\Carbon::parse(session('assessment_date'))->format('M j, Y') }}</strong>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
                <form action="{{ route('assessments.reset-location') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-change-location">
                        <i class="fas fa-exchange-alt me-2"></i>
                        <span class="d-none d-sm-inline">Change Location</span>
                        <span class="d-sm-none">Change</span>
                    </button>
                </form>
            </div>
<<<<<<< HEAD
        </div>
        @endif

        <!-- Alerts -->
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

        <!-- Page Header -->
        <div class="page-header d-flex justify-content-between align-items-start">
            <div>
                <h1 class="page-title">Welcome, {{ Auth::user()->full_name }}</h1>
                <p class="page-subtitle">Here's an overview of your day.</p>
            </div>
            <div class="user-profile">
                <div class="user-avatar">
                    {{ substr(Auth::user()->full_name, 0, 1) }}
                </div>
                <div class="user-name d-none d-md-block">
                    {{ Auth::user()->full_name }}
                </div>
            </div>
        </div>

        <!-- Director Assessment Card -->
        @if(auth()->user()->role->name === 'director')
        <div class="row mb-4">
            <div class="col-12">
                <div class="custom-card director-card">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                            <div class="mb-3 mb-md-0">
                                <h5 class="card-title mb-2">
                                    <i class="fas fa-user-shield me-2 pulse-animation"></i>
                                    Director Assessment Mode
                                </h5>
                                <p class="card-text mb-2 opacity-90">
                                    Conduct assessments for any PHC in your district
                                </p>
                                <small class="opacity-75 d-none d-md-block">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Select location and assessment period for comprehensive tracking
                                </small>
                                @if(session('assessment_location_selected'))
                                <div class="mt-2 d-block d-md-none">
                                    <small class="opacity-75">
                                        Current: {{ session('assessment_quarter') }} {{ session('assessment_year') }} -
                                        {{ $district ?? 'Unknown' }} / {{ $phc ?? 'Unknown' }}
                                    </small>
                                </div>
                                @endif
                            </div>
                            <div class="align-self-stretch align-self-md-auto">
                                <a href="{{ route('assessments.index') }}" class="btn btn-light btn-lg w-100 w-md-auto">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    @if(session('assessment_location_selected'))
                                        <span class="d-none d-sm-inline">Continue </span>Assessment
                                    @else
                                        <span class="d-none d-sm-inline">Select PHC & </span>Start Assessment
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quality Improvement Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="custom-card quality-card">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                            <div class="mb-3 mb-md-0">
                                <h5 class="card-title mb-2">
                                    <i class="fas fa-chart-line me-2"></i>
                                    Quality Improvement Assessment
                                </h5>
                                <p class="card-text mb-2 opacity-90">
                                    @if(auth()->user()->role->name === 'director')
                                        Conduct SafeCare assessments for PHCs with quarterly tracking
                                    @else
                                        Conduct SafeCare assessments for your facility
                                    @endif
                                </p>
                                <small class="opacity-75 d-none d-md-block">
                                    <i class="fas fa-info-circle me-1"></i>
                                    @if(auth()->user()->role->name === 'director')
                                        Select quarter, year, and assessment date for comprehensive tracking
                                    @else
                                        Track facility performance across quarters with detailed reporting
                                    @endif
                                </small>
                            </div>
                            <div class="align-self-stretch align-self-md-auto">
                                <a href="{{ route('qip.index') }}?auto_open=true" class="btn btn-light btn-lg w-100 w-md-auto" onclick="localStorage.setItem('auto_open_qip_modal', 'true');">
                                    <i class="fas fa-clipboard-check me-2"></i>
                                    <span class="d-none d-sm-inline">Start Quality </span>Assessment
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Director Statistics -->
        @if(auth()->user()->role->name === 'director')
        <div class="row mb-4 g-3">
            <div class="col-6 col-md-3">
                <div class="custom-card stats-card">
                    <i class="fas fa-hospital stats-icon text-primary-custom"></i>
                    <div class="stats-value">{{ $totalPhcs ?? '12' }}</div>
                    <div class="stats-label">PHCs in District</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="custom-card stats-card">
                    <i class="fas fa-check-circle stats-icon text-success"></i>
                    <div class="stats-value">{{ $completedAssessments ?? '8' }}</div>
                    <div class="stats-label">Completed This Month</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="custom-card stats-card">
                    <i class="fas fa-clock stats-icon text-warning"></i>
                    <div class="stats-value">{{ $pendingAssessments ?? '4' }}</div>
                    <div class="stats-label">Pending Assessments</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="custom-card stats-card">
                    <i class="fas fa-calendar-check stats-icon text-info"></i>
                    <div class="stats-value">{{ $thisWeekCount ?? '3' }}</div>
                    <div class="stats-label">This Week</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Regular User Metrics -->
        @if(auth()->user()->role->name !== 'director')
        <div class="row mb-4 g-3">
            <div class="col-6 col-md-3">
                <div class="custom-card metric-card">
                    <div class="metric-value">15</div>
                    <div class="metric-label">Patients Today</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="custom-card metric-card">
                    <div class="metric-value">8</div>
                    <div class="metric-label">Scheduled Appointments</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="custom-card metric-card">
                    <div class="metric-value">5</div>
                    <div class="metric-label">Tasks Pending</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="custom-card metric-card">
                    <div class="metric-value">2</div>
                    <div class="metric-label">Emergency Cases</div>
                </div>
            </div>
        </div>
        @else
        <!-- Director also gets general metrics -->
        <div class="row mb-4 g-3">
            <div class="col-6 col-md-3">
                <div class="custom-card metric-card">
                    <div class="metric-value">45</div>
                    <div class="metric-label">Total Patients Today</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="custom-card metric-card">
                    <div class="metric-value">28</div>
                    <div class="metric-label">District Appointments</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="custom-card metric-card">
                    <div class="metric-value">12</div>
                    <div class="metric-label">Active PHCs</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="custom-card metric-card">
                    <div class="metric-value">6</div>
                    <div class="metric-label">Emergency Cases</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Overview Table -->
        <div class="row">
            <div class="col-12">
                <div class="custom-table">
                    <h4 class="p-4 pb-0 mb-0">
                        @if(auth()->user()->role->name === 'director')
                            District Overview
                        @else
                            Upcoming Appointments
                        @endif
                    </h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    @if(auth()->user()->role->name === 'director')
                                        <th>PHC</th>
                                        <th>Status</th>
                                        <th>Last Assessment</th>
                                        <th>Actions</th>
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
                                        <td>
                                            <div class="fw-semibold">Central PHC Ibadan</div>
                                            <small class="text-muted">Primary facility</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">Active</span>
                                        </td>
                                        <td>
                                            <div>2 days ago</div>
                                            <small class="text-muted">Q3 2024</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('assessments.index') }}" class="btn btn-outline-primary">
                                                    <i class="fas fa-clipboard-check me-1"></i>
                                                    <span class="d-none d-md-inline">Standard</span>
                                                </a>
                                                <a href="{{ route('qip.index') }}?auto_open=true" class="btn btn-outline-success" onclick="localStorage.setItem('auto_open_qip_modal', 'true');">
                                                    <i class="fas fa-chart-line me-1"></i>
                                                    <span class="d-none d-md-inline">Quality</span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">Agodi PHC</div>
                                            <small class="text-muted">Secondary facility</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        </td>
                                        <td>
                                            <div>1 week ago</div>
                                            <small class="text-muted">Q2 2024</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('assessments.index') }}" class="btn btn-outline-primary">
                                                    <i class="fas fa-clipboard-check me-1"></i>
                                                    <span class="d-none d-md-inline">Standard</span>
                                                </a>
                                                <a href="{{ route('qip.index') }}" class="btn btn-outline-success">
                                                    <i class="fas fa-chart-line me-1"></i>
                                                    <span class="d-none d-md-inline">Quality</span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">Mokola PHC</div>
                                            <small class="text-muted">Primary facility</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">Active</span>
                                        </td>
                                        <td>
                                            <div>Yesterday</div>
                                            <small class="text-muted">Q3 2024</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('assessments.index') }}" class="btn btn-outline-primary">
                                                    <i class="fas fa-clipboard-check me-1"></i>
                                                    <span class="d-none d-md-inline">Standard</span>
                                                </a>
                                                <a href="{{ route('qip.index') }}" class="btn btn-outline-success">
                                                    <i class="fas fa-chart-line me-1"></i>
                                                    <span class="d-none d-md-inline">Quality</span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">10:00 AM</div>
                                            <small class="text-muted">Today</small>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">John Doe</div>
                                            <small class="text-muted">ID: PT001</small>
                                        </td>
                                        <td>
                                            <div>Routine Checkup</div>
                                            <small class="text-muted">General consultation</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">11:30 AM</div>
                                            <small class="text-muted">Today</small>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">Jane Smith</div>
                                            <small class="text-muted">ID: PT002</small>
                                        </td>
                                        <td>
                                            <div>Follow-up</div>
                                            <small class="text-muted">Post-treatment review</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">2:00 PM</div>
                                            <small class="text-muted">Today</small>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">Mike Johnson</div>
                                            <small class="text-muted">ID: PT003</small>
                                        </td>
                                        <td>
                                            <div>Vaccination</div>
                                            <small class="text-muted">Immunization schedule</small>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assessment Section (if needed) -->
        @if (isset($currentSection))
        <div class="row mt-4">
            <div class="col-12">
                <div class="custom-card">
                    <div class="card-body p-4">
                        <h3 class="mb-4">Assessment for {{ Auth::user()->role->name }}</h3>
                        <h5 class="text-primary-custom mb-4">{{ $currentSection->name }}</h5>

                        <form action="{{ route('assessment.submit') }}" method="POST">
                            @csrf
                            <input type="hidden" name="section_id" value="{{ $currentSection->id }}">

                            @foreach ($currentSection->questions as $question)
                                <div class="mb-4 p-3 border rounded">
                                    <h6 class="fw-semibold">{{ $question->content }}</h6>

                                    @if ($question->type === 'yes_no')
                                        <div class="mt-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" value="yes" required>
                                                <label class="form-check-label">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" value="no" required>
                                                <label class="form-check-label">No</label>
                                            </div>
                                        </div>
                                    @elseif ($question->type === 'text')
                                        <textarea class="form-control mt-3" name="answers[{{ $question->id }}]" rows="3" required placeholder="Enter your response here..."></textarea>
                                    @endif
                                </div>
                            @endforeach

                            <div class="d-flex justify-content-between align-items-center mt-4">
                                @if ($previousSection)
                                    <a href="{{ route('assessment.section', $previousSection->id) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>
                                        Previous
                                    </a>
                                @else
                                    <div></div>
                                @endif

                                <button type="submit" class="btn btn-primary">
                                    {{ $nextSection ? 'Next Section' : 'Complete Assessment' }}
                                    <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- PowerBI Modal -->
    <div class="modal fade" id="powerbiModal" tabindex="-1" aria-labelledby="powerbiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="powerbiModalLabel">
                        <i class="fas fa-chart-line me-2"></i>
                        Health Data Analytics Dashboard
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe
                        src="https://app.powerbi.com/view?r=eyJrIjoiYWM4NDZmZjMtYWU5OC00OWYwLTgxNWEtODM1NzgwYWQxYTg4IiwidCI6IjJkZjMyNDhlLTc5YTItNGI5NC1iNzM1LTA1NTZkMTVhNTkzZiJ9&pageName=f87eebf6820a8b10ad58"
                        style="width: 100%; height: 80vh; border: none;"
                        allowfullscreen="true">
                    </iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="https://app.powerbi.com/view?r=eyJrIjoiYWM4NDZmZjMtYWU5OC00OWYwLTgxNWEtODM1NzgwYWQxYTg4IiwidCI6IjJkZjMyNDhlLTc5YTItNGI5NC1iNzM1LTA1NTZkMTVhNTkzZiJ9&pageName=f87eebf6820a8b10ad58"
                       target="_blank"
                       class="btn btn-primary">
                        <i class="fas fa-external-link-alt me-2"></i>
                        Open in New Tab
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
=======
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
>>>>>>> a15ae561d52746b4fd377fd78effafc2d4fff0ee

    <script>
        // Mobile Sidebar Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');
            const body = document.body;

            // Toggle sidebar
            function toggleSidebar() {
                const isOpen = sidebar.classList.contains('show');

                if (isOpen) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            }

            // Open sidebar
            function openSidebar() {
                sidebar.classList.add('show');
                sidebarBackdrop.classList.add('show');
                body.classList.add('sidebar-open');

                // Update hamburger icon
                const icon = sidebarToggle.querySelector('i');
                icon.className = 'fas fa-times';
            }

            // Close sidebar
            function closeSidebar() {
                sidebar.classList.remove('show');
                sidebarBackdrop.classList.remove('show');
                body.classList.remove('sidebar-open');

                // Update hamburger icon
                const icon = sidebarToggle.querySelector('i');
                icon.className = 'fas fa-bars';
            }

            // Event listeners
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', toggleSidebar);
            }

            if (sidebarBackdrop) {
                sidebarBackdrop.addEventListener('click', closeSidebar);
            }

            // Close sidebar when clicking on sidebar links (mobile)
            const sidebarLinks = document.querySelectorAll('.sidebar-nav-link');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 991) {
                        setTimeout(closeSidebar, 150); // Small delay for better UX
                    }
                });
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 991) {
                    closeSidebar();
                }
            });

            // Handle orientation change on mobile
            window.addEventListener('orientationchange', function() {
                setTimeout(() => {
                    if (window.innerWidth > 991) {
                        closeSidebar();
                    }
                }, 100);
            });

            // Prevent scroll when sidebar is open on mobile
            document.addEventListener('touchmove', function(e) {
                if (body.classList.contains('sidebar-open') && window.innerWidth <= 991) {
                    e.preventDefault();
                }
            }, { passive: false });

            console.log('Dashboard initialized successfully');
        });

        // jQuery Setup
        $(document).ready(function() {
            // Setup AJAX with CSRF token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // PowerBI Modal handling
            $('#powerbiModal').on('shown.bs.modal', function() {
                console.log('PowerBI Dashboard opened');
            });

            $('#powerbiModal').on('hidden.bs.modal', function() {
                console.log('PowerBI Dashboard closed');
            });

            // Smooth scroll for in-page links
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                const target = $(this.getAttribute('href'));
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 80
                    }, 500);
                }
            });

            // Auto-dismiss alerts after 5 seconds
            setTimeout(function() {
                $('.alert-dismissible').alert('close');
            }, 5000);

            console.log('jQuery features initialized');
        });

        // Progressive Web App features (optional)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                // You can register a service worker here for offline functionality
                console.log('Service Worker support detected');
            });
        }

        // Performance monitoring (optional)
        window.addEventListener('load', function() {
            if (performance.navigation.type === performance.navigation.TYPE_RELOAD) {
                console.log('Page was reloaded');
            }

            // Log performance metrics
            setTimeout(function() {
                const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
                console.log('Page load time:', loadTime + 'ms');
            }, 0);
        });
    </script>
</body>
</html>
