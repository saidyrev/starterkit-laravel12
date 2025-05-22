@extends('layouts.sneat')

@section('title', 'Users Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Users Management</h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Users</h5>
            <button type="button" class="btn btn-primary" id="btnAddUser">
                <i class="bx bx-plus me-1"></i> Add User
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="usersTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalTitle">Add User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="userForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role_id" class="form-label">Role</label>
                        <select class="form-select" id="role_id" name="role_id" required>
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="row" id="passwordFields">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password">
                                <div class="invalid-feedback"></div>
                                <small class="text-muted" id="passwordHelp">Minimum 8 characters</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveUser">Save User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- User Detail Modal -->
<div class="modal fade" id="userDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="userDetailContent">
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
    // CSRF Token Setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize DataTable
    var table = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "{{ route('users.index') }}",
            type: "GET"
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'role_name', name: 'role.display_name'},
            {data: 'created_formatted', name: 'created_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        pageLength: 10,
        language: {
            processing: '<div class="spinner-border text-primary" role="status"></div>',
            emptyTable: "No users found"
        }
    });

    // Add User
    $('#btnAddUser').click(function() {
        resetForm();
        $('#userModalTitle').text('Add User');
        $('#btnSaveUser').text('Save User');
        $('#password, #password_confirmation').prop('required', true);
        $('#passwordHelp').text('Minimum 8 characters');
        $('#userModal').modal('show');
    });

    // Edit User
    $(document).on('click', '.btn-edit', function() {
        var userId = $(this).data('id');
        resetForm();
        
        $.get("{{ url('users') }}/" + userId + "/edit")
            .done(function(response) {
                if (response.success) {
                    var user = response.data;
                    $('#userModalTitle').text('Edit User');
                    $('#btnSaveUser').text('Update User');
                    $('#name').val(user.name);
                    $('#email').val(user.email);
                    $('#role_id').val(user.role_id);
                    $('#password, #password_confirmation').prop('required', false);
                    $('#passwordHelp').text('Leave blank to keep current password');
                    $('#userForm').data('user-id', userId);
                    $('#userModal').modal('show');
                }
            })
            .fail(function() {
                Swal.fire('Error!', 'Failed to load user data', 'error');
            });
    });

    // Show User
    $(document).on('click', '.btn-show', function() {
        var userId = $(this).data('id');
        
        $.get("{{ url('users') }}/" + userId)
            .done(function(response) {
                if (response.success) {
                    var user = response.data;
                    var roleHtml = user.role ? 
                        '<span class="badge bg-primary">' + user.role.display_name + '</span>' :
                        '<span class="badge bg-secondary">No Role</span>';
                    
                    var content = `
                        <div class="row">
                            <div class="col-12">
                                <h5>${user.name}</h5>
                                <p><strong>Email:</strong> ${user.email}</p>
                                <p><strong>Role:</strong> ${roleHtml}</p>
                                <p><strong>Created:</strong> ${new Date(user.created_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                    `;
                    
                    $('#userDetailContent').html(content);
                    $('#userDetailModal').modal('show');
                }
            })
            .fail(function() {
                Swal.fire('Error!', 'Failed to load user details', 'error');
            });
    });

    // Delete User
    $(document).on('click', '.btn-delete', function() {
        var userId = $(this).data('id');
        var userName = $(this).data('name');
        
        Swal.fire({
            title: 'Are you sure?',
            text: `Delete user "${userName}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('users') }}/" + userId,
                    type: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Deleted!', response.message, 'success');
                            table.ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        var response = xhr.responseJSON;
                        Swal.fire('Error!', response?.message || 'Something went wrong!', 'error');
                    }
                });
            }
        });
    });

    // Form Submit
    $('#userForm').submit(function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var userId = $(this).data('user-id');
        var url = userId ? "{{ url('users') }}/" + userId : "{{ route('users.store') }}";
        
        if (userId) {
            formData += '&_method=PUT';
        }
        
        // Clear errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#btnSaveUser').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');
            },
            success: function(response) {
                if (response.success) {
                    $('#userModal').modal('hide');
                    Swal.fire('Success!', response.message, 'success');
                    table.ajax.reload();
                }
            },
            error: function(xhr) {
                var errors = xhr.responseJSON?.errors;
                if (errors) {
                    $.each(errors, function(field, messages) {
                        var input = $('#' + field);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(messages[0]);
                    });
                } else {
                    Swal.fire('Error!', 'Something went wrong!', 'error');
                }
            },
            complete: function() {
                var isEdit = $('#userModalTitle').text().includes('Edit');
                $('#btnSaveUser').prop('disabled', false).text(isEdit ? 'Update User' : 'Save User');
            }
        });
    });

    // Reset Form Function
    function resetForm() {
        $('#userForm')[0].reset();
        $('#userForm').removeData('user-id');
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    }
});
</script>
@endpush