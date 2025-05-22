@extends('layouts.sneat')

@section('title', 'Dashboard')

@push('styles')
<style>
.stats-card {
    border: none;
    border-radius: 12px;
    transition: all 0.3s ease;
    overflow: hidden;
    position: relative;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--gradient-start), var(--gradient-end));
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}

.chart-container {
    position: relative;
    height: 300px;
}

.activity-item {
    padding: 1rem;
    border-left: 3px solid transparent;
    transition: all 0.3s ease;
    border-radius: 0 8px 8px 0;
}

.activity-item:hover {
    background: rgba(105, 108, 255, 0.05);
    border-left-color: #696cff;
}

.welcome-card {
    background: linear-gradient(135deg, #696cff 0%, #9155fd 100%);
    color: white;
    border: none;
    border-radius: 16px;
    overflow: hidden;
    position: relative;
}

.welcome-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -30%;
    width: 80%;
    height: 100%;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    opacity: 0.5;
}

.quick-action-card {
    border: 2px dashed #e4e6ea;
    border-radius: 12px;
    transition: all 0.3s ease;
    cursor: pointer;
    text-decoration: none;
    color: inherit;
}

.quick-action-card:hover {
    border-color: #696cff;
    background: rgba(105, 108, 255, 0.05);
    transform: translateY(-2px);
    color: #696cff;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-card {
    animation: fadeInUp 0.6s ease forwards;
}

.animate-card:nth-child(1) { animation-delay: 0.1s; }
.animate-card:nth-child(2) { animation-delay: 0.2s; }
.animate-card:nth-child(3) { animation-delay: 0.3s; }
.animate-card:nth-child(4) { animation-delay: 0.4s; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card welcome-card">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="text-white mb-2">
                                Welcome back, {{ auth()->user()->name }}! 👋
                            </h3>
                            <p class="text-white-50 mb-3">
                                You have <strong class="text-white">{{ auth()->user()->role->display_name ?? 'User' }}</strong> access. 
                                Here's what's happening in your dashboard today.
                            </p>
                            <div class="d-flex align-items-center text-white-50">
                                <i class="bx bx-time me-2"></i>
                                <span>Last login: {{ auth()->user()->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="position-relative">
                                <img src="{{ asset('sneat/assets/img/illustrations/man-with-laptop.png') }}" 
                                     alt="Welcome" class="img-fluid" style="max-height: 140px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->hasPermission('manage_users'))
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card stats-card animate-card" style="--gradient-start: #696cff; --gradient-end: #9155fd;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #696cff, #9155fd);">
                            <i class="bx bx-user"></i>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <h6 class="text-muted mb-1">Total Users</h6>
                            <h3 class="mb-0">{{ $stats['total_users'] }}</h3>
                            <small class="text-success">
                                <i class="bx bx-up-arrow-alt"></i>
                                {{ $stats['active_users'] }} active
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card stats-card animate-card" style="--gradient-start: #28c76f; --gradient-end: #48da89;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #28c76f, #48da89);">
                            <i class="bx bx-shield"></i>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <h6 class="text-muted mb-1">Total Roles</h6>
                            <h3 class="mb-0">{{ $stats['total_roles'] }}</h3>
                            <small class="text-info">
                                <i class="bx bx-info-circle"></i>
                                System roles
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card stats-card animate-card" style="--gradient-start: #ff3e1d; --gradient-end: #ff6a47;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #ff3e1d, #ff6a47);">
                            <i class="bx bx-lock-open-alt"></i>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <h6 class="text-muted mb-1">Permissions</h6>
                            <h3 class="mb-0">{{ $stats['total_permissions'] }}</h3>
                            <small class="text-warning">
                                <i class="bx bx-check-circle"></i>
                                Available
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card stats-card animate-card" style="--gradient-start: #ffab00; --gradient-end: #ffc942;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #ffab00, #ffc942);">
                            <i class="bx bx-check-shield"></i>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <h6 class="text-muted mb-1">Active Users</h6>
                            <h3 class="mb-0">{{ $stats['active_users'] }}</h3>
                            <small class="text-success">
                                <i class="bx bx-trending-up"></i>
                                {{ round(($stats['active_users'] / $stats['total_users']) * 100) }}% verified
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Charts Section -->
        <div class="col-lg-8 mb-4">
            <div class="row">
                <!-- User Growth Chart -->
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bx bx-trending-up text-success me-2"></i>
                                User Growth
                            </h5>
                            <small class="text-muted">Last 12 months</small>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="userGrowthChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users by Role Chart -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bx bx-pie-chart-alt text-info me-2"></i>
                                Users by Role
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="height: 250px;">
                                <canvas id="usersByRoleChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bx bx-zap text-warning me-2"></i>
                                Quick Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            @if(auth()->user()->hasPermission('manage_users'))
                            <a href="{{ route('users.index') }}" class="quick-action-card d-block mb-3 p-3 text-decoration-none">
                                <div class="d-flex align-items-center">
                                    <i class="bx bx-user-plus text-primary me-3" style="font-size: 24px;"></i>
                                    <div>
                                        <h6 class="mb-1">Manage Users</h6>
                                        <small class="text-muted">Add, edit, or remove users</small>
                                    </div>
                                </div>
                            </a>
                            @endif

                            @if(auth()->user()->hasPermission('manage_roles'))
                            <a href="{{ route('roles.index') }}" class="quick-action-card d-block mb-3 p-3 text-decoration-none">
                                <div class="d-flex align-items-center">
                                    <i class="bx bx-shield-quarter text-success me-3" style="font-size: 24px;"></i>
                                    <div>
                                        <h6 class="mb-1">Manage Roles</h6>
                                        <small class="text-muted">Configure roles & permissions</small>
                                    </div>
                                </div>
                            </a>
                            @endif

                            <a href="{{ route('profile.edit') }}" class="quick-action-card d-block p-3 text-decoration-none">
                                <div class="d-flex align-items-center">
                                    <i class="bx bx-cog text-info me-3" style="font-size: 24px;"></i>
                                    <div>
                                        <h6 class="mb-1">Profile Settings</h6>
                                        <small class="text-muted">Update your profile</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="col-lg-4">
            <!-- Recent Users -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-group text-primary me-2"></i>
                        Recent Users
                    </h5>
                </div>
                <div class="card-body p-0">
                    @forelse($recentUsers as $recentUser)
                    <div class="d-flex align-items-center p-3 border-bottom">
                        <div class="avatar avatar-sm me-3">
                            <img src="{{ $recentUser->avatar_url ?? asset('sneat/assets/img/avatars/1.png') }}" 
                                 alt="Avatar" class="user-avatar">
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0">{{ $recentUser->name }}</h6>
                            <small class="text-muted">{{ $recentUser->email }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-label-{{ $recentUser->role->name === 'admin' ? 'danger' : ($recentUser->role->name === 'editor' ? 'warning' : 'info') }}">
                                {{ $recentUser->role->display_name ?? 'User' }}
                            </span>
                            <small class="d-block text-muted">{{ $recentUser->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    @empty
                    <div class="p-3 text-center text-muted">
                        <i class="bx bx-user-plus mb-2" style="font-size: 24px;"></i>
                        <p class="mb-0">No users yet</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Activity Feed -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-time-five text-warning me-2"></i>
                        Recent Activity
                    </h5>
                </div>
                <div class="card-body p-0">
                    @foreach($activities as $activity)
                    <div class="activity-item d-flex align-items-start">
                        <div class="avatar avatar-sm me-3">
                            <div class="avatar-initial bg-label-{{ $activity['color'] }} rounded">
                                <i class="bx {{ $activity['icon'] }}"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0">
                                <strong>{{ $activity['user'] }}</strong> {{ $activity['action'] }}
                            </p>
                            <small class="text-muted">{{ $activity['time'] }}</small>
                        </div>
                    </div>
                    @endforeach
                    
                    <div class="p-3 text-center">
                        <small class="text-muted">More activity features coming soon...</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // User Growth Chart
    const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
    const userGrowthData = @json($userGrowth);
    
    // Prepare data for the last 12 months
    const months = [];
    const counts = [];
    
    for (let i = 11; i >= 0; i--) {
        const date = new Date();
        date.setMonth(date.getMonth() - i);
        const monthKey = date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0');
        months.push(date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' }));
        
        const found = userGrowthData.find(item => item.month === monthKey);
        counts.push(found ? found.count : 0);
    }

    new Chart(userGrowthCtx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'New Users',
                data: counts,
                borderColor: '#696cff',
                backgroundColor: 'rgba(105, 108, 255, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#696cff',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            elements: {
                point: {
                    hoverBackgroundColor: '#696cff'
                }
            }
        }
    });

    // Users by Role Chart
    const usersByRoleCtx = document.getElementById('usersByRoleChart').getContext('2d');
    const usersByRoleData = @json($usersByRole);

    new Chart(usersByRoleCtx, {
        type: 'doughnut',
        data: {
            labels: usersByRoleData.map(item => item.label),
            datasets: [{
                data: usersByRoleData.map(item => item.count),
                backgroundColor: usersByRoleData.map(item => item.color),
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                }
            },
            cutout: '60%'
        }
    });
});
</script>
@endpush