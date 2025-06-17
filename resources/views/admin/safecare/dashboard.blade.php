<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHC Management Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Custom styles */
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        #sidebar-wrapper {
            min-height: 100vh;
            width: 280px;
            margin-left: -280px;
            transition: margin 0.25s ease-out;
            background: linear-gradient(180deg, #2c3e50 0%, #1a252f 100%);
        }

        #sidebar-wrapper .sidebar-heading {
            padding: 1.2rem 1.5rem;
            font-size: 1.4rem;
            background: rgba(255, 255, 255, 0.1);
        }

        .list-group-item {
            border: none;
            padding: 15px 30px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }

        .list-group-item:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
            transform: translateX(5px);
        }

        .list-group-item.active {
            background-color: rgba(255, 255, 255, 0.2) !important;
            border-left: 4px solid #3498db;
        }

        .navbar {
            background-color: #ffffff !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .page-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #3498db;
        }

        /* Enhanced Table Styling */
        .table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            margin-top: 1rem;
        }

        .table thead {
            background-color: #f8f9fa;
        }

        .table thead th {
            border-bottom: 2px solid #3498db;
            color: #2c3e50;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
            padding: 1rem;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
        }

        /* Status Badge Styling */
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.85rem;
        }

        .status-active {
            background-color: #2ecc71;
            color: white;
        }

        .status-pending {
            background-color: #f1c40f;
            color: white;
        }

        .status-inactive {
            background-color: #e74c3c;
            color: white;
        }

        /* Action Buttons */
        .btn-action {
            padding: 0.5rem 1rem;
            border-radius: 5px;
            margin: 0 0.2rem;
            transition: all 0.3s ease;
        }

        .btn-edit {
            background-color: #3498db;
            color: white;
        }

        .btn-delete {
            background-color: #e74c3c;
            color: white;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Navbar Enhancements */
        .navbar-nav .nav-item .nav-link {
            padding: 0.8rem 1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-item .nav-link:hover {
            background-color: #f8f9fa;
            color: #3498db;
        }

        .nav-icon {
            font-size: 1.2rem;
        }

        /* Responsive Adjustments */
        @media (min-width: 768px) {
            #sidebar-wrapper {
                margin-left: 0;
            }

            #page-content-wrapper {
                min-width: 0;
                width: 100%;
            }

            #wrapper.toggled #sidebar-wrapper {
                margin-left: -280px;
            }
        }

        a {
            text-decoration: none !important;
        }
    </style>
</head>

<body>
    <div class="d-flex" id="wrapper">
        <!-- Enhanced Sidebar -->
        <div class="border-right" id="sidebar-wrapper">
            <div class="sidebar-heading text-white d-flex align-items-center">
                <i class="fas fa-hospital-alt me-2"></i>
                PHC Management
            </div>
            <div class="list-group list-group-flush">
                <a href="{{ route('dashboard') }}"
                    class="list-group-item list-group-item-action bg-transparent text-white active">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
                <a href="{{ route('users.index') }}"
                    class="list-group-item list-group-item-action bg-transparent text-white">
                    <i class="fas fa-users me-2"></i> Users
                </a>
                <a href="{{ route('roles.index') }}"
                    class="list-group-item list-group-item-action bg-transparent text-white">
                    <i class="fas fa-user-tag me-2"></i> Roles
                </a>
                <a href="{{ route('phcs.index') }}"
                    class="list-group-item list-group-item-action bg-transparent text-white">
                    <i class="fas fa-clinic-medical me-2"></i> PHCs
                </a>
                <a href="{{ route('admin.users.create') }}"
                    class="list-group-item list-group-item-action bg-transparent text-white">
                    <i class="fas fa-users me-2"></i> Create Directors
                </a>
                <a href="{{ route('admin.pendingusers') }}"
                    class="list-group-item list-group-item-action bg-transparent text-white">
                    <i class="fas fa-user-clock me-2"></i> View Pending Users
                </a>
                <a href="{{ route('admin.assessments.set-next-date-form') }}"
                    class="list-group-item list-group-item-action bg-transparent text-white">
                    <i class="fas fa-user-clock me-2"></i>Set General Assessment Date
                </a>
                <a href="{{ route('admin.safecare.dashboard') }}"
                    class="list-group-item list-group-item-action bg-transparent text-white">
                    <i class="fas fa-user-clock me-2"></i>Set Safecare Assessment Date
                </a>
            </div>
        </div>
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fas fa-shield-alt text-primary me-2"></i>SafeCare Settings</h2>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card admin-card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Total Periods</h6>
                                    <h3 class="mb-0" id="total-periods">-</h3>
                                </div>
                                <i class="fas fa-calendar-alt fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card admin-card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Active Periods</h6>
                                    <h3 class="mb-0" id="active-periods">-</h3>
                                </div>
                                <i class="fas fa-check-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card admin-card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Total Assessments</h6>
                                    <h3 class="mb-0" id="total-assessments">-</h3>
                                </div>
                                <i class="fas fa-file-medical-alt fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card admin-card bg-warning text-dark">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Avg Compliance</h6>
                                    <h3 class="mb-0" id="avg-compliance">-</h3>
                                </div>
                                <i class="fas fa-chart-line fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Create New Period -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card admin-card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Create New Assessment Period</h5>
                        </div>
                        <div class="card-body">
                            <form id="create-period-form">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="period-name" class="form-label">Period Name</label>
                                        <input type="text" class="form-control" id="period-name" name="name"
                                            placeholder="e.g., Q1 2024 SafeCare" required>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="quarter" class="form-label">Quarter</label>
                                        <select class="form-select" id="quarter" name="quarter" required>
                                            <option value="">Select Quarter</option>
                                            <option value="Q1">Q1</option>
                                            <option value="Q2">Q2</option>
                                            <option value="Q3">Q3</option>
                                            <option value="Q4">Q4</option>
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="year" class="form-label">Year</label>
                                        <input type="number" class="form-control" id="year" name="year"
                                            min="2024" max="2030" value="{{ date('Y') }}" required>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="start-date" class="form-label">Start Date</label>
                                        <input type="date" class="form-control" id="start-date" name="start_date"
                                            required>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="end-date" class="form-label">End Date</label>
                                        <input type="date" class="form-control" id="end-date" name="end_date"
                                            required>
                                    </div>

                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <label for="description" class="form-label">Description (Optional)</label>
                                        <textarea class="form-control" id="description" name="description" rows="2"
                                            placeholder="Additional notes about this assessment period..."></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assessment Periods List -->
            <div class="row">
                <div class="col-12">
                    <div class="card admin-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Assessment Periods</h5>
                            <button class="btn btn-sm btn-outline-primary" onclick="loadPeriods()">
                                <i class="fas fa-sync-alt me-1"></i>Refresh
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="periods-container">
                                <div class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2">Loading assessment periods...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <script>
            $(document).ready(function() {
                loadPeriods();
                loadStatistics();
            });

            // Create new period
            $('#create-period-form').on('submit', function(e) {
                e.preventDefault();

                const formData = {
                    name: $('#period-name').val(),
                    quarter: $('#quarter').val(),
                    year: $('#year').val(),
                    start_date: $('#start-date').val(),
                    end_date: $('#end-date').val(),
                    description: $('#description').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                $.ajax({
                    url: '/admin/safecare-periods',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            alert('Assessment period created successfully!');
                            $('#create-period-form')[0].reset();
                            loadPeriods();
                            loadStatistics();
                        }
                    },
                    error: function(xhr) {
                        alert('Error creating period: ' + (xhr.responseJSON?.message || 'Unknown error'));
                    }
                });
            });

            // Load periods
            function loadPeriods() {
                $.get('/admin/safecare-periods', function(response) {
                    if (response.success) {
                        displayPeriods(response.periods);
                    }
                }).fail(function() {
                    $('#periods-container').html('<div class="alert alert-danger">Error loading periods</div>');
                });
            }

            // Display periods
            function displayPeriods(periods) {
                if (periods.length === 0) {
                    $('#periods-container').html('<div class="alert alert-info">No assessment periods found</div>');
                    return;
                }

                let html = '<div class="row">';

                periods.forEach(function(period) {
                    const status = getPeriodStatus(period);
                    const statusClass = getStatusClass(status);
                    const statusBadge = getStatusBadge(status);

                    html += `
                    <div class="col-md-6 mb-3">
                        <div class="card period-card ${statusClass}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title mb-0">${period.name}</h6>
                                    ${statusBadge}
                                </div>

                                <p class="text-muted mb-2">${period.quarter} ${period.year}</p>

                                <div class="row text-sm">
                                    <div class="col-6">
                                        <strong>Start:</strong> ${formatDate(period.start_date)}
                                    </div>
                                    <div class="col-6">
                                        <strong>End:</strong> ${formatDate(period.end_date)}
                                    </div>
                                </div>

                                ${period.description ? `<p class="mt-2 mb-2 text-muted small">${period.description}</p>` : ''}

                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-file-medical-alt me-1"></i>
                                        ${period.assessments_count || 0} assessments
                                    </small>

                                    <button class="btn btn-sm ${period.is_active ? 'btn-warning' : 'btn-success'}"
                                            onclick="togglePeriodStatus(${period.id})">
                                        <i class="fas fa-${period.is_active ? 'pause' : 'play'} me-1"></i>
                                        ${period.is_active ? 'Deactivate' : 'Activate'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                });

                html += '</div>';
                $('#periods-container').html(html);
            }

            // Toggle period status
            function togglePeriodStatus(periodId) {
                if (!confirm('Are you sure you want to change the status of this period?')) {
                    return;
                }

                $.ajax({
                    url: `/admin/safecare-periods/${periodId}/toggle-status`,
                    method: 'PATCH',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            loadPeriods();
                            loadStatistics();
                        }
                    },
                    error: function(xhr) {
                        alert('Error updating period status');
                    }
                });
            }

            // Load statistics
            function loadStatistics() {
                $.get('/safecare-analytics', function(response) {
                    if (response.success) {
                        const analytics = response.analytics;
                        $('#total-assessments').text(analytics.total_assessments || 0);
                        $('#avg-compliance').text((analytics.average_compliance || 0).toFixed(1) + '%');
                    }
                });
            }

            // Helper functions
            function getPeriodStatus(period) {
                const now = new Date();
                const start = new Date(period.start_date);
                const end = new Date(period.end_date);

                if (!period.is_active) return 'inactive';
                if (now < start) return 'upcoming';
                if (now > end) return 'expired';
                return 'active';
            }

            function getStatusClass(status) {
                switch (status) {
                    case 'active':
                        return 'active';
                    case 'expired':
                        return 'expired';
                    case 'upcoming':
                        return 'upcoming';
                    default:
                        return '';
                }
            }

            function getStatusBadge(status) {
                const badges = {
                    'active': '<span class="badge bg-success status-badge">Active</span>',
                    'expired': '<span class="badge bg-danger status-badge">Expired</span>',
                    'upcoming': '<span class="badge bg-warning status-badge">Upcoming</span>',
                    'inactive': '<span class="badge bg-secondary status-badge">Inactive</span>'
                };

                return badges[status] || badges['inactive'];
            }

            function formatDate(dateString) {
                return new Date(dateString).toLocaleDateString();
            }
        </script>

        <meta name="csrf-token" content="{{ csrf_token() }}">
</body>

</html>
