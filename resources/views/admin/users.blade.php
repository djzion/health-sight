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

        .status-approved {
            background-color: green;
            color: white;
        }

        .status-pending {
            background-color: #f1c40f;
            color: white;
        }

        .status-rejected {
            background-color: #f60f0c;
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

        <!-- Enhanced Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light py-3 shadow-sm">
                <div class="container-fluid">
                    <button class="btn btn-link" id="menu-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item mx-2">
                            <a class="nav-link" href="#"><i class="fas fa-bell nav-icon"></i></a>
                        </li>
                        <li class="nav-item mx-2">
                            <a class="nav-link" href="#"><i class="fas fa-user-circle nav-icon"></i></a>
                        </li>
                        <li class="nav-item mx-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-link nav-link">
                                    <i class="fas fa-sign-out-alt nav-icon"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="container-fluid p-4">
                <h1 class="page-title">User Management</h1>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th>PHC</th>
                                <th>Local Government</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-circle me-2 text-primary"></i>
                                            {{ $user->full_name }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $user->role->name }}</span>
                                    </td>
                                    <td>{{ $user->phc->name }}</td>
                                    <td>{{ $user->lga->name }}</td>
                                    <td>
                                        <span class="status-badge {{ strtolower($user->status) === 'approved' ? 'status-approved' : (strtolower($user->status) === 'pending' ? 'status-pending' : 'status-rejected') }}">
                                            {{ $user->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-action btn-edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="btn btn-action btn-delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('menu-toggle').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('wrapper').classList.toggle('toggled');
        });
    </script>
</body>
</html>
