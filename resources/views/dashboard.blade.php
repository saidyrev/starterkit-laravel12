@extends('layouts.sneat')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-8 mb-4 order-0">
        <div class="card">
            <div class="d-flex align-items-end row">
                <div class="col-sm-7">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Welcome {{ auth()->user()->name }}! 🎉</h5>
                        <p class="mb-4">
                            You have <span class="fw-bold">{{ auth()->user()->role->display_name ?? 'User' }}</span> access.
                            <br>Check your dashboard to see what's new.
                        </p>
                        <a href="javascript:;" class="btn btn-sm btn-outline-primary">View Profile</a>
                    </div>
                </div>
                <div class="col-sm-5 text-center text-sm-left">
                    <div class="card-body pb-0 px-0 px-md-4">
                        <img src="{{ asset('sneat/assets/img/illustrations/man-with-laptop-light.png') }}" 
                             height="140" alt="View Badge User" 
                             data-app-dark-img="illustrations/man-with-laptop-dark.png" 
                             data-app-light-img="illustrations/man-with-laptop-light.png" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-4 order-1">
        <div class="row">
            <div class="col-lg-6 col-md-12 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('sneat/assets/img/icons/unicons/chart-success.png') }}" alt="chart success" class="rounded" />
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Total Users</span>
                        <h3 class="card-title mb-2">{{ \App\Models\User::count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('sneat/assets/img/icons/unicons/wallet-info.png') }}" alt="Credit Card" class="rounded" />
                            </div>
                        </div>
                        <span>Roles</span>
                        <h3 class="card-title text-nowrap mb-1">{{ \App\Models\Role::count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Permissions Info -->
<div class="row">
    <div class="col-md-6 col-lg-4 mb-3">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between pb-0">
                <div class="card-title mb-0">
                    <h5 class="m-0 me-2">Your Permissions</h5>
                </div>
            </div>
            <div class="card-body">
                @if(auth()->user()->role && auth()->user()->role->permissions->count() > 0)
                    @foreach(auth()->user()->role->permissions as $permission)
                        <span class="badge bg-label-primary me-1 mb-1">{{ $permission->display_name }}</span>
                    @endforeach
                @else
                    <p class="text-muted">No permissions assigned</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection