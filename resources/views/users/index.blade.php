@extends('layouts.sneat')

@section('title', 'User Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
.user-management-header {
    background: linear-gradient(135deg, #696cff 0%, #9155fd 100%);
    border-radius: 12px;
    color: white;
    position: relative;
    overflow: hidden;
}

.user-management-header::before {
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

.stats-widget {
    background: white;
    border-radius: 12px;
    border: none;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stats-widget::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--accent-color);
}

.stats-widget:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.filter-card {
    background: #f8f9fa;
    border: 1px solid #e3e6ea;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.filter-card.active {
    background: white;
    border-color: #696cff;
    box-shadow: 0 2px 8px rgba(105, 108, 255, 0.15);
}

.action-buttons .btn {
    border-radius: 8px;
    padding: 0.5rem 1rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.bulk-actions {
    background: linear-gradient(135deg, #ff3e1d 0%, #ff6a47 100%);
    border-radius: 12px;
    color: white;
    padding: 1rem;
    margin-bottom: 1rem;
    display: none;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.table-card {
    border-radius: 12px;
    border: none;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.table thead th {
    background: #f8f9fa;
    border: none;
    font-weight: 600;
    color: #566a7f;
    padding: 1rem 0.75rem;
}

.table tbody td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
    border-top: 1px solid #f0f0f0;
}

.table tbody tr:hover {
    background: rgba(105, 108, 255, 0.02);
}

.user-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #f8f9fa;
}

.status-indicator {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.modal-header {
    background: linear-gradient(135deg, #696cff 0%, #9155fd 100%);
    color: white;
    border-radius: 12px 12px 0 0;
}

.modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

.form-floating label {
    color: #6c757d;
}

.form-floating .form-control:focus ~ label {
    color: #696cff;
}

.btn-icon {
    width: 32px;
    height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
}

.dropdown-menu {
    border: none;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    padding: 0.5rem 0;
}

.dropdown-item {
    padding: 0.5rem 1rem;
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background: rgba(105, 108, 255, 0.08);
    color: #696cff;
}

.user-detail-card {
    border: none;
    border-radius: 12px;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
}

.permission-chip {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    background: rgba(105, 108, 255, 0.1);
    color: #696cff;
    border-radius: 20px;
    font-size: 0.75rem;
    margin: 0.125rem;
}

@media (max-width: 768px) {
    .stats-widget {
        margin-bottom: 1rem;
    }
    
    .action-buttons {
        margin-bottom: 1rem;
    }
    
    .filter-card {
        margin-bottom: 1rem;
    }
}
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-widget" style="--accent-color: #696cff;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md">
                                <div class="avatar-initial bg-primary rounded-circle">
                                    <i class="bx bx-user text-white"></i>
                                </div>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h4 class="mb-0">{{ $stats['total'] }}</h4>
                            <small class="text-muted">Total Users</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-widget" style="--accent-color: #28c76f;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md">
                                <div class="avatar-initial bg-success rounded-circle">
                                    <i class="bx bx-check-circle text-white"></i>
                                </div>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h4 class="mb-0">{{ $stats['active'] }}</h4>
                            <small class="text-muted">Active Users</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-widget" style="--accent-color: #ffab00;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md">
                                <div class="avatar-initial bg-warning rounded-circle">
                                    <i class="bx bx-time-five text-white"></i>
                                </div>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h4 class="mb-0">{{ $stats['pending'] }}</h4>
                            <small class="text-muted">Pending Users</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-widget" style="--accent-color: #ff3e1d;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md">
                                <div class="avatar-initial bg-danger rounded-circle">
                                    <i class="bx bx-trending-up text-white"></i>
                                </div>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h4 class="mb-0">{{ $stats['this_month'] }}</h4>
                            <small class="text-muted">This Month</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Enhanced Filters for Mobile -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card filter-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="bx bx-filter-alt me-2"></i>Filters
                </h6>
                <button class="btn btn-sm btn-outline-primary d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    <i class="bx bx-chevron-down"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="collapse d-md-block" id="filterCollapse">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <label class="form-label">Filter by Role</label>
                            <select class="form-select" id="roleFilter">
                                <option value="">All Roles</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3 col-sm-6 mb-3">
                            <label class="form-label">Filter by Status</label>
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Pending</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 col-sm-8 mb-3">
                            <label class="form-label">Date Range</label>
                            <input type="text" class="form-control" id="dateRange" placeholder="Select date range">
                        </div>
                        
                        <div class="col-md-2 col-sm-4 mb-3">
                            <label class="form-label d-none d-md-block">&nbsp;</label>
                            <button class="btn btn-outline-primary w-100" id="clearFilters">
                                <i class="bx bx-refresh me-1"></i> Clear
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center action-buttons">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" id="btnAddUser">
                        <i class="bx bx-plus me-1"></i> Add User
                    </button>
                    <button type="button" class="btn btn-outline-success" id="btnExport">
                        <i class="bx bx-download me-1"></i> Export
                    </button>
                    <button type="button" class="btn btn-outline-info" id="btnRefresh">
                        <i class="bx bx-refresh me-1"></i> Refresh
                    </button>
                </div>
                
                <div class="d-flex align-items-center">
                    <span class="text-muted me-2">Quick Actions:</span>
                    <button class="btn btn-outline-secondary btn-sm me-1" onclick="selectAllUsers()">
                        <i class="bx bx-check-square"></i>
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="clearSelection()">
                        <i class="bx bx-square"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="bulk-actions" id="bulkActions">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="text-white mb-0">
                    <i class="bx bx-check-circle me-2"></i>
                    <span id="selectedCount">0</span> users selected
                </h6>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-light btn-sm" onclick="bulkAction('activate')">
                    <i class="bx bx-check me-1"></i> Activate
                </button>
                <button class="btn btn-light btn-sm" onclick="bulkAction('deactivate')">
                    <i class="bx bx-x me-1"></i> Deactivate
                </button>
                <button class="btn btn-danger btn-sm" onclick="bulkAction('delete')">
                    <i class="bx bx-trash me-1"></i> Delete
                </button>
                <button class="btn btn-outline-light btn-sm" onclick="clearSelection()">
                    <i class="bx bx-x"></i>
                </button>
            </div>
        </div>
    </div>

<!-- Users Table - IMPROVED VERSION -->
<div class="row">
    <div class="col-12">
        <div class="card table-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="me-2 text-primary"></i>
                    Users Directory
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-label-info">
                        <i class="bx bx-info-circle me-1"></i>
                        <span id="totalUsersCount">{{ $stats['total'] }}</span> Total
                    </span>
                </div>
            </div>
            
            <div class="card-datatable">
                <table class="table table-hover" id="usersTable">
                    <thead>
                        <tr>
                            <th width="50">
                                <div class="form-check">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                    <label class="form-check-label" for="selectAll"></label>
                                </div>
                            </th>
                            <th>User Information</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Active</th>
                            <th width="100">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Enhanced User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalTitle">
                    <i class="bx bx-user-plus me-2"></i>Add New User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="userForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" required>
                                <label for="name">Full Name</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                <label for="email">Email Address</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <select class="form-select" id="role_id" name="role_id" required>
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                    @endforeach
                                </select>
                                <label for="role_id">Role</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" id="send_welcome_email" name="send_welcome_email">
                                <label class="form-check-label" for="send_welcome_email">
                                    Send Welcome Email
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row" id="passwordFields">
                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                                <label for="password">Password</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password">
                                <label for="password_confirmation">Confirm Password</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info" id="passwordHelp">
                        <i class="bx bx-info-circle me-2"></i>
                        <span>Password must be at least 8 characters long</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveUser">
                        <i class="bx bx-save me-1"></i>Save User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Enhanced User Detail Modal -->
<div class="modal fade" id="userDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bx bx-user me-2"></i>User Profile
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="userDetailContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editUserFromDetail">
                    <i class="bx bx-edit me-1"></i>Edit User
                </button>
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
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
$(document).ready(function() {
    console.log('User management page loaded'); // Debug log
    
    // CSRF Setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize Date Range Picker
    flatpickr("#dateRange", {
        mode: "range",
        dateFormat: "Y-m-d",
        placeholder: "Select date range"
    });

    // Initialize DataTable
    var table = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "{{ route('users.index') }}",
            type: "GET",
            data: function(d) {
                d.role_filter = $('#roleFilter').val();
                d.status_filter = $('#statusFilter').val();
                d.date_range = $('#dateRange').val();
            },
            error: function(xhr, error, thrown) {
                console.log('DataTables AJAX Error:', xhr, error, thrown);
            }
        },
        columns: [
            {data: 'checkbox', name: 'checkbox', orderable: false, searchable: false},
            {data: 'avatar_name', name: 'name'},
            {data: 'role_badge', name: 'role.display_name'},
            {data: 'status_badge', name: 'status', orderable: false},
            {data: 'last_active', name: 'updated_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        pageLength: 15,
        lengthMenu: [[10, 15, 25, 50, 100], [10, 15, 25, 50, 100]],
        language: {
            processing: '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>',
            emptyTable: "No users found",
            zeroRecords: "No matching users found"
        },
        order: [[1, 'asc']],
        drawCallback: function() {
            updateSelectionCount();
            console.log('Table redrawn'); // Debug log
        }
    });

    console.log('DataTable initialized'); // Debug log

    // Filter Functions
    $('#roleFilter, #statusFilter').change(function() {
        console.log('Filter changed:', $(this).attr('id'), $(this).val()); // Debug log
        table.draw();
        $('.filter-card').addClass('active');
    });

    $('#dateRange').change(function() {
        if ($(this).val()) {
            console.log('Date range changed:', $(this).val()); // Debug log
            table.draw();
            $('.filter-card').addClass('active');
        }
    });

    $('#clearFilters').click(function() {
        console.log('Clear filters clicked'); // Debug log
        $('#roleFilter, #statusFilter, #dateRange').val('');
        $('.filter-card').removeClass('active');
        table.draw();
    });

    // Selection Functions
    $('#selectAll').change(function() {
        var isChecked = $(this).is(':checked');
        $('.user-checkbox').prop('checked', isChecked);
        updateSelectionCount();
    });

    // FIXED: Event delegation untuk checkbox yang di-generate oleh DataTables
    $(document).on('change', '.user-checkbox', function() {
        console.log('Checkbox changed:', $(this).val()); // Debug log
        updateSelectionCount();
        var totalCheckboxes = $('.user-checkbox').length;
        var checkedCheckboxes = $('.user-checkbox:checked').length;
        $('#selectAll').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
        $('#selectAll').prop('checked', checkedCheckboxes === totalCheckboxes);
    });

    function updateSelectionCount() {
        var selectedCount = $('.user-checkbox:checked').length;
        $('#selectedCount').text(selectedCount);
        
        if (selectedCount > 0) {
            $('#bulkActions').slideDown();
        } else {
            $('#bulkActions').slideUp();
        }
    }

    // Global selection functions
    window.selectAllUsers = function() {
        $('.user-checkbox').prop('checked', true);
        $('#selectAll').prop('checked', true);
        updateSelectionCount();
    }

    window.clearSelection = function() {
        $('.user-checkbox, #selectAll').prop('checked', false);
        $('#selectAll').prop('indeterminate', false);
        updateSelectionCount();
    }

    // FIXED: Event delegation untuk tombol aksi
    // Show User Details - FIXED
    $(document).on('click', '.btn-show', function(e) {
        e.preventDefault();
        console.log('Show button clicked'); // Debug log
        
        var userId = $(this).data('id');
        console.log('User ID:', userId); // Debug log
        
        if (!userId) {
            console.error('User ID not found');
            return;
        }
        
        $.get("{{ url('users') }}/" + userId)
            .done(function(response) {
                console.log('Show response:', response); // Debug log
                
                if (response.success) {
                    var user = response.data;
                    var stats = response.stats || {};
                    
                    var roleHtml = user.role ? 
                        '<span class="badge bg-primary">' + user.role.display_name + '</span>' :
                        '<span class="badge bg-secondary">No Role</span>';
                    
                    var permissionsHtml = '';
                    if (user.role && user.role.permissions && user.role.permissions.length > 0) {
                        user.role.permissions.forEach(function(permission) {
                            permissionsHtml += '<span class="permission-chip">' + permission.display_name + '</span>';
                        });
                    } else {
                        permissionsHtml = '<span class="text-muted">No permissions assigned</span>';
                    }
                    
                    var content = `
                        <div class="row">
                            <div class="col-md-4 text-center mb-4">
                                <div class="user-detail-card p-4">
                                    <div class="avatar avatar-xl mx-auto mb-3">
                                        <img src="${user.avatar_url || '{{ asset("sneat/assets/img/avatars/1.png") }}'}" 
                                             alt="Avatar" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                                    </div>
                                    <h4 class="mb-1">${user.name}</h4>
                                    <p class="text-muted mb-2">${user.email}</p>
                                    ${roleHtml}
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <h6>Account Information</h6>
                                        <ul class="list-unstyled">
                                            <li><strong>User ID:</strong> #${user.id}</li>
                                            <li><strong>Status:</strong> ${user.email_verified_at ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-warning">Pending</span>'}</li>
                                            <li><strong>Created:</strong> ${new Date(user.created_at).toLocaleDateString()}</li>
                                            <li><strong>Last Updated:</strong> ${stats.last_login || 'N/A'}</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <h6>Statistics</h6>
                                        <ul class="list-unstyled">
                                            <li><strong>Days Since Created:</strong> ${stats.days_since_created || 0} days</li>
                                            <li><strong>Permissions:</strong> ${stats.permissions_count || 0}</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <h6>Permissions</h6>
                                    <div>${permissionsHtml}</div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    $('#userDetailContent').html(content);
                    $('#editUserFromDetail').data('user-id', userId);
                    $('#userDetailModal').modal('show');
                } else {
                    Swal.fire('Error!', response.message || 'Failed to load user details', 'error');
                }
            })
            .fail(function(xhr) {
                console.log('Show error:', xhr); // Debug log
                Swal.fire('Error!', 'Failed to load user details', 'error');
            });
    });

    // Edit User - FIXED
    $(document).on('click', '.btn-edit', function(e) {
        e.preventDefault();
        console.log('Edit button clicked'); // Debug log
        
        var userId = $(this).data('id');
        console.log('Edit User ID:', userId); // Debug log
        
        if (!userId) {
            console.error('User ID not found');
            return;
        }
        
        resetForm();
        
        $.get("{{ url('users') }}/" + userId + "/edit")
            .done(function(response) {
                console.log('Edit response:', response); // Debug log
                
                if (response.success) {
                    var user = response.data;
                    $('#userModalTitle').html('<i class="bx bx-edit me-2"></i>Edit User');
                    $('#btnSaveUser').html('<i class="bx bx-save me-1"></i>Update User');
                    $('#name').val(user.name);
                    $('#email').val(user.email);
                    $('#role_id').val(user.role_id);
                    $('#password, #password_confirmation').prop('required', false);
                    $('#passwordHelp').html('<i class="bx bx-info-circle me-2"></i>Leave blank to keep current password');
                    $('#userForm').data('user-id', userId);
                    $('#userModal').modal('show');
                } else {
                    Swal.fire('Error!', response.message || 'Failed to load user data', 'error');
                }
            })
            .fail(function(xhr) {
                console.log('Edit error:', xhr); // Debug log
                Swal.fire('Error!', 'Failed to load user data', 'error');
            });
    });

    // Delete User - FIXED
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        console.log('Delete button clicked'); // Debug log
        
        var userId = $(this).data('id');
        var userName = $(this).data('name');
        
        console.log('Delete User ID:', userId, 'Name:', userName); // Debug log
        
        if (!userId) {
            console.error('User ID not found');
            return;
        }
        
        Swal.fire({
            title: 'Are you sure?',
            text: `Delete user "${userName}"? This action cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('users') }}/" + userId,
                    type: 'DELETE',
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Deleting...',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        console.log('Delete response:', response); // Debug log
                        
                        if (response.success) {
                            Swal.fire('Deleted!', response.message, 'success');
                            table.ajax.reload();
                            clearSelection();
                        } else {
                            Swal.fire('Error!', response.message || 'Failed to delete user', 'error');
                        }
                    },
                    error: function(xhr) {
                        console.log('Delete error:', xhr); // Debug log
                        var response = xhr.responseJSON;
                        Swal.fire('Error!', response?.message || 'Something went wrong!', 'error');
                    }
                });
            }
        });
    });

    // Toggle Status - FIXED
    $(document).on('click', '.btn-toggle-status', function(e) {
        e.preventDefault();
        console.log('Toggle status clicked'); // Debug log
        
        var userId = $(this).data('id');
        var currentStatus = $(this).data('status');
        var actionText = currentStatus === 'active' ? 'deactivate' : 'activate';
        
        console.log('Toggle User ID:', userId, 'Status:', currentStatus); // Debug log
        
        if (!userId) {
            console.error('User ID not found');
            return;
        }
        
        Swal.fire({
            title: 'Change Status',
            text: `Are you sure you want to ${actionText} this user?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#696cff',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Yes, ${actionText}!`
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('users') }}/" + userId + "/toggle-status",
                    type: 'PATCH',
                    success: function(response) {
                        console.log('Toggle status response:', response); // Debug log
                        
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success');
                            table.ajax.reload();
                        } else {
                            Swal.fire('Error!', response.message || 'Failed to change status', 'error');
                        }
                    },
                    error: function(xhr) {
                        console.log('Toggle status error:', xhr); // Debug log
                        var response = xhr.responseJSON;
                        Swal.fire('Error!', response?.message || 'Something went wrong!', 'error');
                    }
                });
            }
        });
    });

    // Reset Password - FIXED
    $(document).on('click', '.btn-reset-password', function(e) {
        e.preventDefault();
        console.log('Reset password clicked'); // Debug log
        
        var userId = $(this).data('id');
        
        console.log('Reset password User ID:', userId); // Debug log
        
        if (!userId) {
            console.error('User ID not found');
            return;
        }
        
        Swal.fire({
            title: 'Reset Password',
            text: 'Are you sure you want to reset this user\'s password?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#696cff',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, reset!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('users') }}/" + userId + "/reset-password",
                    type: 'POST',
                    success: function(response) {
                        console.log('Reset password response:', response); // Debug log
                        
                        if (response.success) {
                            Swal.fire({
                                title: 'Password Reset!',
                                html: `${response.message}<br><br><strong>New Password:</strong> <code>${response.new_password}</code>`,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            Swal.fire('Error!', response.message || 'Failed to reset password', 'error');
                        }
                    },
                    error: function(xhr) {
                        console.log('Reset password error:', xhr); // Debug log
                        var response = xhr.responseJSON;
                        Swal.fire('Error!', response?.message || 'Something went wrong!', 'error');
                    }
                });
            }
        });
    });

    // Bulk Actions
    window.bulkAction = function(action) {
        var selectedIds = $('.user-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        console.log('Bulk action:', action, 'IDs:', selectedIds); // Debug log

        if (selectedIds.length === 0) {
            Swal.fire('Warning!', 'Please select users first.', 'warning');
            return;
        }

        var actionText = {
            'delete': 'delete',
            'activate': 'activate',
            'deactivate': 'deactivate'
        }[action];

        Swal.fire({
            title: 'Are you sure?',
            text: `You want to ${actionText} ${selectedIds.length} selected users?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: action === 'delete' ? '#d33' : '#696cff',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Yes, ${actionText}!`
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("users.bulk-action") }}',
                    type: 'POST',
                    data: {
                        action: action,
                        user_ids: selectedIds
                    },
                    success: function(response) {
                        console.log('Bulk action response:', response); // Debug log
                        
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success');
                            table.ajax.reload();
                            clearSelection();
                        } else {
                            Swal.fire('Error!', response.message || 'Bulk action failed', 'error');
                        }
                    },
                    error: function(xhr) {
                        console.log('Bulk action error:', xhr); // Debug log
                        var response = xhr.responseJSON;
                        Swal.fire('Error!', response?.message || 'Something went wrong!', 'error');
                    }
                });
            }
        });
    }

    // Add User
    $('#btnAddUser').click(function() {
        console.log('Add user button clicked'); // Debug log
        resetForm();
        $('#userModalTitle').html('<i class="bx bx-user-plus me-2"></i>Add New User');
        $('#btnSaveUser').html('<i class="bx bx-save me-1"></i>Save User');
        $('#password, #password_confirmation').prop('required', true);
        $('#passwordHelp').html('<i class="bx bx-info-circle me-2"></i>Password must be at least 8 characters long');
        $('#userModal').modal('show');
    });

    // Export Users
    $('#btnExport').click(function() {
        console.log('Export button clicked'); // Debug log
        var params = new URLSearchParams();
        if ($('#roleFilter').val()) params.append('role_filter', $('#roleFilter').val());
        if ($('#statusFilter').val()) params.append('status_filter', $('#statusFilter').val());
        if ($('#dateRange').val()) params.append('date_range', $('#dateRange').val());
        
        window.location.href = '{{ route("users.export") }}?' + params.toString();
    });

    // Refresh Table
    $('#btnRefresh').click(function() {
        console.log('Refresh button clicked'); // Debug log
        table.ajax.reload();
        Swal.fire({
            icon: 'success',
            title: 'Refreshed!',
            text: 'User data has been refreshed.',
            timer: 1500,
            showConfirmButton: false
        });
    });

    // Form Submit
    $('#userForm').submit(function(e) {
        e.preventDefault();
        console.log('Form submitted'); // Debug log
        
        var formData = $(this).serialize();
        var userId = $(this).data('user-id');
        var url = userId ? "{{ url('users') }}/" + userId : "{{ route('users.store') }}";
        var method = userId ? 'PUT' : 'POST';
        
        console.log('Form URL:', url, 'Method:', method); // Debug log
        
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
                console.log('Form response:', response); // Debug log
                
                if (response.success) {
                    $('#userModal').modal('hide');
                    Swal.fire('Success!', response.message, 'success');
                    table.ajax.reload();
                } else {
                    Swal.fire('Error!', response.message || 'Something went wrong!', 'error');
                }
            },
            error: function(xhr) {
                console.log('Form error:', xhr); // Debug log
                
                var response = xhr.responseJSON;
                if (xhr.status === 422 && response && response.errors) {
                    // Validation errors
                    $.each(response.errors, function(field, messages) {
                        var input = $('#' + field);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(messages[0]);
                    });
                } else {
                    var errorMessage = response?.message || 'Something went wrong!';
                    Swal.fire('Error!', errorMessage, 'error');
                }
            },
            complete: function() {
                var isEdit = $('#userModalTitle').text().includes('Edit');
                $('#btnSaveUser').prop('disabled', false).html(isEdit ? '<i class="bx bx-save me-1"></i>Update User' : '<i class="bx bx-save me-1"></i>Save User');
            }
        });
    });

    // Edit from Detail Modal
    $('#editUserFromDetail').click(function() {
        var userId = $(this).data('user-id');
        console.log('Edit from detail, User ID:', userId); // Debug log
        
        $('#userDetailModal').modal('hide');
        $('.btn-edit[data-id="' + userId + '"]').trigger('click');
    });

    // Form Reset Function
    function resetForm() {
        $('#userForm')[0].reset();
        $('#userForm').removeData('user-id');
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    }

    console.log('All event handlers attached'); // Debug log
});
</script>
@endpush