@extends('layouts.sneat')

@section('title', 'Roles Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Roles & Permissions Management</h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Roles</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#roleModal" id="btnAddRole">
                <i class="bx bx-plus me-1"></i> Add Role
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="rolesTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Display Name</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Users Count</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Role Modal -->
<div class="modal fade" id="roleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleModalTitle">Add Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="roleForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Role Name (Internal)</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                                <div class="invalid-feedback"></div>
                                <small class="text-muted">Use lowercase, no spaces (e.g., manager, supervisor)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="display_name" class="form-label">Display Name</label>
                                <input type="text" class="form-control" id="display_name" name="display_name" required>
                                <div class="invalid-feedback"></div>
                                <small class="text-muted">Friendly name shown to users</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Permissions</label>
                        <div class="row" id="permissionsContainer">
                            @foreach($permissions as $permission)
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           id="permission_{{ $permission->id }}" 
                                           name="permissions[]" 
                                           value="{{ $permission->id }}">
                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                                        {{ $permission->display_name }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveRole">Save Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Role Detail Modal -->
<div class="modal fade" id="roleDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Role Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="roleDetailContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#rolesTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: "{{ route('roles.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'display_name', name: 'display_name'},
            {data: 'name', name: 'name'},
            {data: 'description_short', name: 'description'},
            {data: 'users_count_badge', name: 'users_count'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
            emptyTable: "No roles found",
            zeroRecords: "No matching roles found"
        }
    });

    // Add Role Button
    $('#btnAddRole').click(function() {
        resetForm();
        $('#roleModalTitle').text('Add Role');
        $('#btnSaveRole').text('Save Role');
        $('#roleModal').modal('show');
    });

    // Edit Role
    $(document).on('click', '.btn-edit', function() {
        var roleId = $(this).data('id');
        resetForm();
        
        $.get('{{ url("roles") }}/' + roleId + '/edit', function(response) {
            if (response.success) {
                var role = response.data;
                $('#roleModalTitle').text('Edit Role');
                $('#btnSaveRole').text('Update Role');
                $('#name').val(role.name);
                $('#display_name').val(role.display_name);
                $('#description').val(role.description);
                
                // Check permissions
                if (role.permissions) {
                    role.permissions.forEach(function(permission) {
                        $('#permission_' + permission.id).prop('checked', true);
                    });
                }
                
                $('#roleForm').data('role-id', roleId);
                $('#roleModal').modal('show');
            }
        });
    });

    // Show Role Details
    $(document).on('click', '.btn-show', function() {
        var roleId = $(this).data('id');
        
        $.get('{{ url("roles") }}/' + roleId, function(response) {
            if (response.success) {
                var role = response.data;
                
                var permissionsHtml = '';
                if (role.permissions && role.permissions.length > 0) {
                    role.permissions.forEach(function(permission) {
                        permissionsHtml += '<span class="badge bg-label-primary me-1 mb-1">' + permission.display_name + '</span>';
                    });
                } else {
                    permissionsHtml = '<span class="text-muted">No permissions assigned</span>';
                }
                
                var usersHtml = '';
                if (role.users && role.users.length > 0) {
                    role.users.forEach(function(user) {
                        usersHtml += '<span class="badge bg-label-info me-1 mb-1">' + user.name + '</span>';
                    });
                } else {
                    usersHtml = '<span class="text-muted">No users assigned</span>';
                }
                
                var content = `
                    <div class="row">
                        <div class="col-md-12">
                            <h4>${role.display_name}</h4>
                            <p class="text-muted">${role.description || 'No description'}</p>
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Role Information</h6>
                            <ul class="list-unstyled">
                                <li><strong>Internal Name:</strong> ${role.name}</li>
                                <li><strong>Display Name:</strong> ${role.display_name}</li>
                                <li><strong>Users Count:</strong> ${role.users ? role.users.length : 0}</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Permissions</h6>
                            ${permissionsHtml}
                        </div>
                    </div>
                    ${role.users && role.users.length > 0 ? `
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h6>Assigned Users</h6>
                            ${usersHtml}
                        </div>
                    </div>
                    ` : ''}
                `;
                
                $('#roleDetailContent').html(content);
                $('#roleDetailModal').modal('show');
            }
        });
    });

    // Delete Role
    $(document).on('click', '.btn-delete', function() {
        var roleId = $(this).data('id');
        var roleName = $(this).data('name');
        
        Swal.fire({
            title: 'Are you sure?',
            text: `You want to delete role "${roleName}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("roles") }}/' + roleId,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Deleted!', response.message, 'success');
                            table.ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        var response = xhr.responseJSON;
                        Swal.fire('Error!', response.message || 'Something went wrong!', 'error');
                    }
                });
            }
        });
    });

    // Form Submit
    $('#roleForm').submit(function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var roleId = $(this).data('role-id');
        var url = roleId ? '{{ url("roles") }}/' + roleId : '{{ route("roles.store") }}';
        var method = roleId ? 'PUT' : 'POST';
        
        if (roleId) {
            formData += '&_method=PUT';
        }
        formData += '&_token={{ csrf_token() }}';
        
        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#btnSaveRole').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');
            },
            success: function(response) {
                if (response.success) {
                    $('#roleModal').modal('hide');
                    Swal.fire('Success!', response.message, 'success');
                    table.ajax.reload();
                }
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                if (errors) {
                    $.each(errors, function(field, messages) {
                        var input = $('#' + field);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(messages[0]);
                    });
                }
            },
            complete: function() {
                $('#btnSaveRole').prop('disabled', false).text($('#roleModalTitle').text().includes('Add') ? 'Save Role' : 'Update Role');
            }
        });
    });

    // Reset Form
    function resetForm() {
        $('#roleForm')[0].reset();
        $('#roleForm').removeData('role-id');
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('input[type="checkbox"]').prop('checked', false);
    }
});
</script>
@endpush