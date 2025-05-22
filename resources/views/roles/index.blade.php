@extends('layouts.sneat')

@section('title', 'Roles Management')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Roles & Permissions Management</h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Roles</h5>
            <a href="{{ route('roles.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Add Role
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($roles as $role)
                <div class="col-md-4 mb-4">
                    <div class="card border">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title mb-1">{{ $role->display_name }}</h5>
                                    <p class="text-muted small">{{ $role->description }}</p>
                                </div>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('roles.show', $role) }}">
                                            <i class="bx bx-show me-1"></i> View
                                        </a>
                                        <a class="dropdown-item" href="{{ route('roles.edit', $role) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                        @if($role->users_count == 0)
                                        <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this role?')">
                                                <i class="bx bx-trash me-1"></i> Delete
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-2">
                                <span class="badge bg-label-info">{{ $role->users_count }} Users</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection