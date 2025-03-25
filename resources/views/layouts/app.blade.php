@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row">
    <!-- Total Users Card -->
    <div class="row">
        <!-- Total Users Card -->
        <div class="col-lg-3 col-md-6">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text">{{ $totalUsers ?? 0 }}</p>
                </div>
            </div>
        </div>

    <!-- Total PHCs Card -->
    <div class="col-lg-3 col-md-6">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">Total PHCs</h5>
                <p class="card-text">{{ $totalPHCs }}</p>
            </div>
        </div>
    </div>

    <!-- Pending Applications Card -->
    <div class="col-lg-3 col-md-6">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
                <h5 class="card-title">Pending Registrations</h5>
                <p class="card-text">{{ $pendingApplications }}</p>
            </div>
        </div>
    </div>

    <!-- Rejected Applications Card -->
    <div class="col-lg-3 col-md-6">
        <div class="card text-white bg-danger mb-3">
            <div class="card-body">
                <h5 class="card-title">Rejected Registrations</h5>
                <p class="card-text">{{ $rejectedApplications }}</p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Recent Users List -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">Recent Users</div>
            <div class="card-body">
                @if($recentUsers->isEmpty())
                    <p>No recent users available.</p>
                @else
                    <ul class="list-group">
                        @foreach($recentUsers as $user)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">{{ $user->full_name }}</h6>
                                    <small class="text-muted">{{ $user->role->name }}</small>
                                </div>
                                <span class="badge bg-primary rounded-pill">New</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent PHCs List -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">Recent PHCs</div>
            <div class="card-body">
                @if($recentPHCs->isEmpty())
                    <p>No recent PHCs available.</p>
                @else
                    <ul class="list-group">
                        @foreach($recentPHCs as $phc)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">{{ $phc->name }}</h6>
                                    <small class="text-muted">{{ $phc->location }}</small>
                                </div>
                                <small class="text-muted">{{ $phc->created_at->diffForHumans() }}</small>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
