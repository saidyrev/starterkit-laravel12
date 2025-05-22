@extends('layouts.sneat')

@section('title', 'User Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Users /</span> User Details
    </h4>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">User Information</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="avatar avatar-lg me-3">
                            <img src="{{ asset('sneat/assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle">
                        </div>
                        <div>
                            <h4 class="mb-1">{{ $user->name }}</h4>
                            <span class="badge bg-label-{{ $user->role->name === 'admin' ? 'danger' : ($user->role->name === 'editor' ? 'warning' : 'info') }}">
                                {{ $user->role->display_name ?? 'No Role' }}
                            </span>
                        </div>
                    </div>

                    <div class="info-container">
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <span class="fw-bold me-2">Email:</span>
                                <span>{{ $user->email }}</span>
                            </li>
                            <li class="mb-3">
                                <span class="fw-bold me-2">Role:</span>
                                <span>{{ $user->role->display_name ?? 'No Role' }}</span>
                            </li>
                            <li class="mb-3">
                                <span class="fw-bold me-2">Created:</span>
                                <span>{{ $user->created_at->format('M d, Y h:i A') }}</span>
                            </li>
                            <li class="mb-3">
                                <span class="fw-bold me-2">Last Updated:</span>
                                <span>{{ $user->updated_at->format('M d, Y h:i A') }}</span>
                            </li>
                        </ul>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Back
                        </a>
                        <div>
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-primary me-2">
                                <i class="bx bx-edit me-1"></i> Edit
                            </a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" 
                                        onclick="return confirm('Are you sure you want to delete this user?')">
                                    <i class="bx bx-trash me-1"></i> Delete
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Permissions</h5>
                </div>
                <div class="card-body">
                    @if($user->role && $user->role->permissions->count() > 0)
                        @foreach($user->role->permissions as $permission)
                            <span class="badge bg-label-primary me-1 mb-1">{{ $permission->display_name }}</span>
                        @endforeach
                    @else
                        <p class="text-muted">No permissions assigned</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection