<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHC Management - Add New User</title>
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

        /* Form specific styles */
        .form-card {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .form-card .card-header {
            border-radius: 10px 10px 0 0;
            border-bottom: 2px solid #3498db;
        }

        .input-group-text {
            background-color: #3498db;
            color: white;
            border: none;
            width: 45px;
            justify-content: center;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }

        .btn-primary {
            background-color: #3498db;
            border-color: #3498db;
        }

        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }

        a {
            text-decoration: none !important;
        }
    </style>
</head>

<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
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

        <!-- Page Content -->
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

            <!-- Main Content -->
            <div class="container-fluid p-4">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}

                        @if (session('username') && session('password'))
                            <div class="mt-2">
                                <strong>Username:</strong> {{ session('username') }}<br>
                                <strong>Password:</strong> {{ session('password') }}<br>
                                <strong>Debug:</strong> {{ session('debug_info') ?? 'No debug info' }}<br>
                                Please save this password information as it will not be shown again.
                            </div>
                        @else
                            <div class="mt-2 text-danger">
                                <strong>Warning:</strong> Username and password information were not available.
                            </div>
                        @endif
                    </div>
                @endif
                <h2 class="page-title">
                    <i class="fas fa-user-plus me-2"></i>Add New User
                </h2>

                <div class="row">
                    <div class="col-lg-8 col-md-10 mx-auto">
                        <div class="card form-card">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0 text-primary">User Information</h5>
                            </div>

                            <div class="card-body p-4">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('admin.users.store') }}">
                                    @csrf

                                    <div class="mb-4">
                                        <label for="full_name" class="form-label fw-bold">Full Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <input type="text" class="form-control" id="full_name" name="full_name"
                                                value="{{ old('full_name') }}" placeholder="Enter user's full name"
                                                required>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="username" class="form-label fw-bold">Username</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                            <input type="text" class="form-control" id="username" name="username"
                                                value="{{ old('username') }}" placeholder="Enter username" required>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="email" class="form-label fw-bold">Email Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                            <input type="email" class="form-control" id="email" name="email"
                                                value="{{ old('email') }}" placeholder="Enter user's email address"
                                                required>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="phone" class="form-label fw-bold">Phone Number</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                            <input type="text" class="form-control" id="phone" name="phone"
                                                value="{{ old('phone') }}" placeholder="Enter user's phone number"
                                                required>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="role_id" class="form-label fw-bold">Role</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                            <select class="form-select" id="role_id" name="role_id" required>
                                                <option value="">Select a role</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}"
                                                        {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="password" class="form-label fw-bold">Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input type="text" class="form-control" id="password"
                                                name="password" value="{{ old('password') }}"
                                                placeholder="Enter password (minimum 6 characters)" required>
                                        </div>
                                        <div class="form-text">Password must be at least 6 characters long.</div>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <a href="{{ route('users.index') }}" class="btn btn-secondary me-2">
                                            <i class="fas fa-times me-1"></i> Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-user-plus me-1"></i> Create User
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
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
