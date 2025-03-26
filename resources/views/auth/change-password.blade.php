<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHC Management - Change Password</title>
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

        /* Password form specific styles */
        .password-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header {
            position: relative;
            padding: 1.5rem;
            background: linear-gradient(135deg, #3498db, #2980b9);
            border-bottom: none;
        }

        .card-header::after {
            content: "";
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 100%;
            height: 20px;
            background: white;
            border-radius: 50% 50% 0 0;
        }

        .card-header h5 {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .card-body {
            padding: 2rem;
        }

        .form-control {
            padding: 0.75rem;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }

        .password-strength {
            height: 5px;
            margin-top: 10px;
            border-radius: 5px;
            background-color: #e0e0e0;
            position: relative;
            overflow: hidden;
        }

        .password-strength-meter {
            height: 100%;
            width: 0%;
            transition: width 0.3s ease;
        }

        .weak {
            background-color: #e74c3c;
            width: 25%;
        }

        .medium {
            background-color: #f39c12;
            width: 50%;
        }

        .strong {
            background-color: #2ecc71;
            width: 100%;
        }

        .password-feedback {
            font-size: 0.85rem;
            margin-top: 5px;
        }

        .btn-submit {
            padding: 0.75rem 2rem;
            border-radius: 50px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            border: none;
            font-weight: 600;
            letter-spacing: 1px;
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(52, 152, 219, 0.4);
            background: linear-gradient(135deg, #2980b9, #3498db);
        }

        .password-icon {
            font-size: 4rem;
            color: #3498db;
            margin-bottom: 1rem;
        }

        .password-requirements {
            background-color: #edf7fd;
            border-left: 4px solid #3498db;
            padding: 1.5rem;
            border-radius: 10px;
            margin-top: 1.5rem;
        }

        .requirement-item {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .requirement-icon {
            margin-right: 10px;
            color: #7f8c8d;
        }

        .requirement-text {
            color: #7f8c8d;
        }

        .requirement-met .requirement-icon,
        .requirement-met .requirement-text {
            color: #2ecc71;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-radius: 0 10px 10px 0;
            border-left: none;
            cursor: pointer;
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
                    class="list-group-item list-group-item-action bg-transparent text-white">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
                <a href="{{ route('users.index') }}"
                    class="list-group-item list-group-item-action bg-transparent text-white">
                    <i class="fas fa-users me-2"></i> Users
                </a>
                <a href="#" class="list-group-item list-group-item-action bg-transparent text-white">
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
                <h2 class="page-title">
                    <i class="fas fa-key me-2"></i>Security Settings
                </h2>

                <div class="row">
                    <div class="col-lg-8 col-md-10 mx-auto">
                        @if (session('warning'))
                            <div class="alert alert-warning d-flex align-items-center mb-4">
                                <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
                                <div>{{ session('warning') }}</div>
                            </div>
                        @endif

                        <div class="card password-card">
                            <div class="card-header text-white">
                                <h5 class="mb-0 d-flex align-items-center">
                                    <i class="fas fa-shield-alt me-2"></i>
                                    Change Your Password
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-4">
                                    <div class="password-icon">
                                        <i class="fas fa-lock"></i>
                                    </div>
                                    <h4>Create a Strong Password</h4>
                                    <p class="text-muted">Your password helps keep your account secure.</p>
                                </div>

                                <form method="POST" action="{{ route('change-password.update') }}" id="password-form">
                                    @csrf

                                    <div class="mb-4">
                                        <label for="password" class="form-label fw-bold">New Password</label>
                                        <div class="input-group">
                                            <input id="password" type="password"
                                                class="form-control @error('password') is-invalid @enderror" name="password"
                                                required autocomplete="new-password">
                                            <span class="input-group-text" id="toggle-password">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="password-strength mt-2">
                                            <div class="password-strength-meter" id="strength-meter"></div>
                                        </div>
                                        <div class="password-feedback text-muted" id="password-feedback">
                                            Password strength will be shown here
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="password-confirm" class="form-label fw-bold">Confirm New Password</label>
                                        <div class="input-group">
                                            <input id="password-confirm" type="password" class="form-control"
                                                name="password_confirmation" required autocomplete="new-password">
                                            <span class="input-group-text" id="toggle-confirm">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                        <div class="form-text mt-2" id="password-match-feedback"></div>
                                    </div>

                                    <div class="password-requirements">
                                        <h6 class="mb-3">Password Requirements:</h6>
                                        <div class="requirement-item" id="req-length">
                                            <i class="fas fa-circle requirement-icon"></i>
                                            <span class="requirement-text">At least 8 characters long</span>
                                        </div>
                                        <div class="requirement-item" id="req-uppercase">
                                            <i class="fas fa-circle requirement-icon"></i>
                                            <span class="requirement-text">Contains at least one uppercase letter</span>
                                        </div>
                                        <div class="requirement-item" id="req-lowercase">
                                            <i class="fas fa-circle requirement-icon"></i>
                                            <span class="requirement-text">Contains at least one lowercase letter</span>
                                        </div>
                                        <div class="requirement-item" id="req-number">
                                            <i class="fas fa-circle requirement-icon"></i>
                                            <span class="requirement-text">Contains at least one number</span>
                                        </div>
                                        <div class="requirement-item" id="req-special">
                                            <i class="fas fa-circle requirement-icon"></i>
                                            <span class="requirement-text">Contains at least one special character</span>
                                        </div>
                                    </div>

                                    <div class="d-grid mt-4">
                                        <button type="submit" class="btn btn-submit" id="submit-btn">
                                            <i class="fas fa-key me-2"></i> Update Password
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
        // Toggle sidebar
        document.getElementById('menu-toggle').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('wrapper').classList.toggle('toggled');
        });

        // Toggle password visibility
        document.getElementById('toggle-password').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        document.getElementById('toggle-confirm').addEventListener('click', function() {
            const confirmInput = document.getElementById('password-confirm');
            const icon = this.querySelector('i');

            if (confirmInput.type === 'password') {
                confirmInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                confirmInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Password strength meter
        const passwordInput = document.getElementById('password');
        const strengthMeter = document.getElementById('strength-meter');
        const feedbackText = document.getElementById('password-feedback');

        // Requirement check elements
        const reqLength = document.getElementById('req-length');
        const reqUppercase = document.getElementById('req-uppercase');
        const reqLowercase = document.getElementById('req-lowercase');
        const reqNumber = document.getElementById('req-number');
        const reqSpecial = document.getElementById('req-special');

        passwordInput.addEventListener('input', function() {
            const value = this.value;
            let strength = 0;
            let feedback = '';

            // Check requirements
            const hasLength = value.length >= 8;
            const hasUppercase = /[A-Z]/.test(value);
            const hasLowercase = /[a-z]/.test(value);
            const hasNumber = /[0-9]/.test(value);
            const hasSpecial = /[^A-Za-z0-9]/.test(value);

            // Update requirement indicators
            toggleRequirement(reqLength, hasLength);
            toggleRequirement(reqUppercase, hasUppercase);
            toggleRequirement(reqLowercase, hasLowercase);
            toggleRequirement(reqNumber, hasNumber);
            toggleRequirement(reqSpecial, hasSpecial);

            // Calculate strength
            if (hasLength) strength += 1;
            if (hasUppercase) strength += 1;
            if (hasLowercase) strength += 1;
            if (hasNumber) strength += 1;
            if (hasSpecial) strength += 1;

            // Set strength meter
            strengthMeter.className = 'password-strength-meter';

            if (value.length === 0) {
                strengthMeter.style.width = '0%';
                feedbackText.textContent = 'Password strength will be shown here';
                feedbackText.className = 'password-feedback text-muted';
            } else if (strength < 3) {
                strengthMeter.classList.add('weak');
                feedbackText.textContent = 'Weak password';
                feedbackText.className = 'password-feedback text-danger';
            } else if (strength < 5) {
                strengthMeter.classList.add('medium');
                feedbackText.textContent = 'Medium strength password';
                feedbackText.className = 'password-feedback text-warning';
            } else {
                strengthMeter.classList.add('strong');
                feedbackText.textContent = 'Strong password';
                feedbackText.className = 'password-feedback text-success';
            }
        });

        // Check password match
        const confirmInput = document.getElementById('password-confirm');
        const matchFeedback = document.getElementById('password-match-feedback');

        confirmInput.addEventListener('input', function() {
            if (this.value.length === 0) {
                matchFeedback.textContent = '';
                matchFeedback.className = 'form-text mt-2';
            } else if (this.value === passwordInput.value) {
                matchFeedback.textContent = 'Passwords match';
                matchFeedback.className = 'form-text mt-2 text-success';
            } else {
                matchFeedback.textContent = 'Passwords do not match';
                matchFeedback.className = 'form-text mt-2 text-danger';
            }
        });

        function toggleRequirement(element, isMet) {
            if (isMet) {
                element.classList.add('requirement-met');
                element.querySelector('.requirement-icon').classList.remove('fa-circle');
                element.querySelector('.requirement-icon').classList.add('fa-check-circle');
            } else {
                element.classList.remove('requirement-met');
                element.querySelector('.requirement-icon').classList.remove('fa-check-circle');
                element.querySelector('.requirement-icon').classList.add('fa-circle');
            }
        }

        // Form submission validation
        document.getElementById('password-form').addEventListener('submit', function(e) {
            const password = passwordInput.value;
            const confirm = confirmInput.value;

            if (password !== confirm) {
                e.preventDefault();
                matchFeedback.textContent = 'Passwords do not match';
                matchFeedback.className = 'form-text mt-2 text-danger';
                return false;
            }

            const hasLength = password.length >= 8;
            const hasUppercase = /[A-Z]/.test(password);
            const hasLowercase = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSpecial = /[^A-Za-z0-9]/.test(password);

            if (!(hasLength && hasUppercase && hasLowercase && hasNumber && hasSpecial)) {
                e.preventDefault();
                feedbackText.textContent = 'Your password does not meet all requirements';
                feedbackText.className = 'password-feedback text-danger';
                return false;
            }
        });
    </script>
</body>

</html>
