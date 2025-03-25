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
            background-color: #1abc9c;
        }

        .sidebar .logout-btn {
            margin-top: 30px;
        }

        .main-content {
            margin-left: 250px;
            padding: 30px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
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
    <div class="main-content">
        <div class="container py-4">
            <form action="{{ route('assessments.store') }}" method="POST" class="mb-5">
                @csrf

                @php
                    $globalCounter = 1;
                    $processedQuestions = [];
                @endphp

                @foreach($sections as $section)
                    @php
                        $sectionQuestions = $assessments->where('assessment_section_id', $section->id)->sortBy('order');
                        if ($sectionQuestions->isEmpty()) continue;
                    @endphp

                    <!-- Section Card -->
                    <div class="card mb-4 shadow-sm border-0 rounded">
                        <!-- Section Header -->
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">{{ $section->section_name }}</h5>
                        </div>

                        <!-- Section Body -->
                        <div class="card-body">
                            @php $sectionProcessed = []; @endphp

                            @foreach($sectionQuestions as $assessment)
                                @php
                                    $questionKey = $assessment->question . '_' . $assessment->response_type;
                                    if (in_array($questionKey, $sectionProcessed)) continue;
                                    $sectionProcessed[] = $questionKey;
                                @endphp

                                <!-- Individual Question -->
                                <div class="mb-4 border-bottom pb-3">
                                    <label class="form-label fw-bold text-dark">
                                        <span class="me-2">{{ $globalCounter }}.</span>
                                        {{ $assessment->question }}
                                    </label>

                                    <!-- Input Controls -->
                                    @switch($assessment->response_type)
                                        @case('text')
                                            <textarea name="responses[{{ $assessment->id }}]" rows="3"
                                                class="form-control" placeholder="Enter your response here...">{{ $existingResponses[$assessment->id]->response ?? '' }}</textarea>
                                            @break

                                        @case('yes_no')
                                            <div class="d-flex gap-3 mt-2">
                                                @foreach(['yes' => 'Yes', 'no' => 'No'] as $value => $label)
                                                    <div class="form-check">
                                                        <input type="radio" class="form-check-input"
                                                            name="responses[{{ $assessment->id }}]"
                                                            value="{{ $value }}"
                                                            {{ isset($existingResponses[$assessment->id]) && $existingResponses[$assessment->id]->response === $value ? 'checked' : '' }}>
                                                        <label class="form-check-label">{{ $label }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @break

                                        @case('good_bad')
                                            <div class="d-flex gap-3 mt-2">
                                                @foreach(['good' => 'Good', 'bad' => 'Bad'] as $value => $label)
                                                    <div class="form-check">
                                                        <input type="radio" class="form-check-input"
                                                            name="responses[{{ $assessment->id }}]" value="{{ $value }}">
                                                        <label class="form-check-label">{{ $label }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @break
                                    @endswitch

                                    <!-- Conditional Input -->
                                    @if(isset($assessment->conditional_logic))
                                        <div class="mt-3 d-none conditional-input" data-parent="{{ $assessment->id }}">
                                            <textarea name="responses[{{ $assessment->id }}][additional]" rows="2"
                                                class="form-control" placeholder="Please provide additional details...">{{ $existingResponses[$assessment->id]->comments ?? '' }}</textarea>
                                        </div>
                                    @endif
                                </div>

                                @php $globalCounter++; @endphp
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <!-- Submit Button -->
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-5 py-2">
                        Submit Assessment
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>




@extends('layouts.user')

@section('content')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function () {
            const parentDiv = this.closest('.mb-4');
            if (parentDiv) {
                const conditional = parentDiv.querySelector('.conditional-input');
                if (conditional) {
                    conditional.classList.toggle('d-none', this.value !== 'no');
                }
            }
        });
    });

    // Trigger visibility on page load if needed
    document.querySelectorAll('input[type="radio"]:checked').forEach(radio => radio.dispatchEvent(new Event('change')));
});
</script>
@endpush
@endsection
