@extends('layouts.sneat')

@section('title', 'Role Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="{{ asset('css/user-management-responsive.css') }}" />

<style>
.role-management-header {
    background: linear-gradient(135deg, #ff3e1d 0%, #ff6a47 100%);
    border-radius: 12px;
    color: white;
    position: relative;
    overflow: hidden;
}

.role-management-header::before {
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

.role-info-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    min-width: 200px;
}

.role-icon-container {
    flex-shrink: 0;
}

.role-icon {
    width: 42px;
    height: 42px;
    border-radius: 8px;
    background: rgba(105, 108, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    border: 2px solid #ffffff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.role-details {
    flex-grow: 1;
    min-width: 0;
}

.role-name {
    font-weight: 600;
    color: #566a7f;
    margin-bottom: 0.25rem;
    font-size: 0.875rem;
    line-height: 1.2;
}

.role-code {
    color: #8a8d93;
    font-size: 0.75rem;
    line-height: 1.2;
    font-family: 'Courier New', monospace;
    background: rgba(105, 108, 255, 0.1);
    padding: 0.125rem 0.375rem;
    border-radius: 4px;
    display: inline-block;
}

.permission-chip {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    background: rgba(105, 108, 255, 0.1);
    color: #696cff;
    border-radius: 20px;
    font-size: 0.75rem;
    margin: 0.125rem;
    border: 1px solid rgba(105, 108, 255, 0.2);
}

.permission-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.75rem;
    max-height: 300px;
    overflow-y: auto;
    padding: 0.5rem;
    border: 1px solid #e4e6ea;
    border-radius: 8px;
    background: #f8f9fa;
}

.permission-item {
    background: white;
    border: 1px solid #e4e6ea;
    border-radius: 6px;
    padding: 0.5rem;
    transition: all 0.2s ease;
}

.permission-item:hover {
    border-color: #696cff;
    box-shadow: 0 2px 4px rgba(105, 108, 255, 0.1);
}

.permission-item.selected {
    border-color: #696cff;
    background: rgba(105, 108, 255, 0.05);
}

@media (max-width: 767px) {
    .role-info-cell {
        min-width: 140px;
        gap: 0.375rem;
    }
    
    .role-icon {
        width: 32px;
        height: 32px;
        font-size: 16px;
    }
    
    .role-name {
        font-size: 0.75rem;
    }
    
    .role-code {
        font-size: 0.625rem;
    }
}
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Page Header -->
    {{-- <div class="row mb-4">
        <div class="col-12">
            <div class="card role-management-header">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="text-white mb-2">
                                <i class="bx bx-shield-quarter me-2"></i>
                                Role Management
                            </h3>
                            <p class="text-white-50 mb-0">
                                Configure roles and permissions to control user access levels. 
                                Maintain security and organize your team effectively.
                            </p>
                        </div>
                        <div class="col-md-4 text-center">
                            <img src="{{ asset('sneat/assets/img/illustrations/man-with-laptop-light.png') }}" 
                                 alt="Roles" class="img-fluid" style="max-height: 120px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-widget" style="--accent-color: #ff3e1d;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md">
                                <div class="avatar-initial bg-danger rounded-circle">
                                    <i class="bx bx-shield-quarter text-white"></i>
                                </div>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h4 class="mb-0">{{ $stats['total'] }}</h4>
                            <small class="text-muted">Total Roles</small>
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
                                    <i class="bx bx-user-check text-white"></i>
                                </div>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h4 class="mb-0">{{ $stats['with_users'] }}</h4>
                            <small class="text-muted">Active Roles</small>
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
                                    <i class="bx bx-user-x text-white"></i>
                                </div>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h4 class="mb-0">{{ $stats['without_users'] }}</h4>
                            <small class="text-muted">Unused Roles</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-widget" style="--accent-color: #696cff;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md">
                                <div class="avatar-initial bg-primary rounded-circle">
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

    <!-- Filters and Actions -->
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
                                <label class="form-label">Filter by Users</label>
                                <select class="form-select" id="usersFilter">
                                    <option value="">All Roles</option>
                                    <option value="with_users">With Users</option>
                                    <option value="without_users">Without Users</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3 col-sm-6 mb-3">
                                <label class="form-label">Filter by Permissions</label>
                                <select class="form-select" id="permissionsFilter">
                                    <option value="">All Roles</option>
                                    <option value="with_permissions">With Permissions</option>
                                    <option value="without_permissions">Without Permissions</option>
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
                    <button type="button" class="btn btn-primary" id="btnAddRole">
                        <i class="bx bx-plus me-1"></i> Add Role
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
                    <button class="btn btn-outline-secondary btn-sm me-1" onclick="selectAllRoles()">
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
                    <span id="selectedCount">0</span> roles selected
                </h6>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-danger btn-sm" onclick="bulkAction('delete')">
                    <i class="bx bx-trash me-1"></i> Delete
                </button>
                <button class="btn btn-outline-light btn-sm" onclick="clearSelection()">
                    <i class="bx bx-x"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Roles Table -->
    <div class="row">
        <div class="col-12">
            <div class="card table-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bx bx-shield-quarter me-2 text-primary"></i>
                        Roles Directory
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-label-info">
                            <i class="bx bx-info-circle me-1"></i>
                            <span id="totalRolesCount">{{ $stats['total'] }}</span> Total
                        </span>
                    </div>
                </div>
                
                <div class="card-datatable">
                    <table class="table table-hover" id="rolesTable">
                        <thead>
                            <tr>
                                <th width="50">
                                    <div class="form-check">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                        <label class="form-check-label" for="selectAll"></label>
                                    </div>
                                </th>
                                <th>Role Information</th>
                                <th>Description</th>
                                <th>Users</th>
                                <th>Permissions</th>
                                <th>Created</th>
                                <th width="100">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Role Modal -->
<div class="modal fade" id="roleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleModalTitle">
                    <i class="bx bx-shield-plus me-2"></i>Add New Role
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="roleForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="name" name="name" placeholder="Role Name" required>
                                <label for="name">Role Name (Internal)</label>
                                <div class="invalid-feedback"></div>
                                <small class="text-muted">Use lowercase, no spaces (e.g., manager, supervisor)</small>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="display_name" name="display_name" placeholder="Display Name" required>
                                <label for="display_name">Display Name</label>
                                <div class="invalid-feedback"></div>
                                <small class="text-muted">Friendly name shown to users</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-floating">
                            <textarea class="form-control" id="description" name="description" placeholder="Description" style="height: 80px;"></textarea>
                            <label for="description">Description (Optional)</label>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Permissions</label>
                        <div class="permission-grid" id="permissionsContainer">
                            @foreach($permissions as $permission)
                            <div class="permission-item">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           id="permission_{{ $permission->id }}" 
                                           name="permissions[]" 
                                           value="{{ $permission->id }}">
                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                                        <strong>{{ $permission->display_name }}</strong>
                                        @if($permission->description)
                                            <br><small class="text-muted">{{ $permission->description }}</small>
                                        @endif
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveRole">
                        <i class="bx bx-save me-1"></i>Save Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Enhanced Role Detail Modal -->
<div class="modal fade" id="roleDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bx bx-shield-quarter me-2"></i>Role Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="roleDetailContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editRoleFromDetail">
                    <i class="bx bx-edit me-1"></i>Edit Role
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
    console.log('Role management page loaded');
    
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
    var table = $('#rolesTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "{{ route('roles.index') }}",
            type: "GET",
            data: function(d) {
                d.users_filter = $('#usersFilter').val();
                d.permissions_filter = $('#permissionsFilter').val();
                d.date_range = $('#dateRange').val();
            },
            error: function(xhr, error, thrown) {
                console.log('DataTables AJAX Error:', xhr, error, thrown);
            }
        },
        columns: [
            {data: 'checkbox', name: 'checkbox', orderable: false, searchable: false},
            {data: 'role_info', name: 'display_name'},
            {data: 'description_badge', name: 'description'},
            {data: 'users_count_badge', name: 'users_count'},
            {data: 'permissions_count_badge', name: 'permissions_count'},
            {data: 'created_formatted', name: 'created_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        pageLength: 15,
        lengthMenu: [[10, 15, 25, 50, 100], [10, 15, 25, 50, 100]],
        language: {
            processing: '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>',
            emptyTable: "No roles found",
            zeroRecords: "No matching roles found"
        },
        order: [[1, 'asc']],
        drawCallback: function() {
            updateSelectionCount();
            console.log('Table redrawn');
        }
    });

    console.log('DataTable initialized');

    // Filter event handlers - same as user management
    $('#usersFilter, #permissionsFilter').change(function() {
        console.log('Filter changed:', $(this).attr('id'), $(this).val());
        table.draw();
        $('.filter-card').addClass('active');
    });

    $('#dateRange').change(function() {
        if ($(this).val()) {
            console.log('Date range changed:', $(this).val());
            table.draw();
            $('.filter-card').addClass('active');
        }
    });

    $('#clearFilters').click(function() {
        console.log('Clear filters clicked');
        $('#usersFilter, #permissionsFilter, #dateRange').val('');
        $('.filter-card').removeClass('active');
        table.draw();
    });

    // Selection functions - same pattern as user management
    $('#selectAll').change(function() {
        var isChecked = $(this).is(':checked');
        $('.role-checkbox').prop('checked', isChecked);
        updateSelectionCount();
    });

    $(document).on('change', '.role-checkbox', function() {
        console.log('Checkbox changed:', $(this).val());
        updateSelectionCount();
        var totalCheckboxes = $('.role-checkbox').length;
        var checkedCheckboxes = $('.role-checkbox:checked').length;
        $('#selectAll').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
        $('#selectAll').prop('checked', checkedCheckboxes === totalCheckboxes);
    });

    function updateSelectionCount() {
        var selectedCount = $('.role-checkbox:checked').length;
        $('#selectedCount').text(selectedCount);
        
        if (selectedCount > 0) {
            $('#bulkActions').slideDown();
        } else {
            $('#bulkActions').slideUp();
        }
    }

    // Global functions
    window.selectAllRoles = function() {
        $('.role-checkbox').prop('checked', true);
        $('#selectAll').prop('checked', true);
        updateSelectionCount();
    }

    window.clearSelection = function() {
        $('.role-checkbox, #selectAll').prop('checked', false);
        $('#selectAll').prop('indeterminate', false);
        updateSelectionCount();
    }

    // CRUD operations with proper event delegation
    // Show Role Details
    $(document).on('click', '.btn-show', function(e) {
        e.preventDefault();
        console.log('Show button clicked');
        
        var roleId = $(this).data('id');
        console.log('Role ID:', roleId);
        
        if (!roleId) {
            console.error('Role ID not found');
            return;
        }
        
        $.get("{{ url('roles') }}/" + roleId)
            .done(function(response) {
                console.log('Show response:', response);
                
                if (response.success) {
                    var role = response.data;
                    var stats = response.stats || {};
                    
                    var permissionsHtml = '';
                    if (role.permissions && role.permissions.length > 0) {
                        role.permissions.forEach(function(permission) {
                            permissionsHtml += '<span class="permission-chip">' + permission.display_name + '</span>';
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
                            <div class="col-md-4 text-center mb-4">
                                <div class="role-detail-card p-4">
                                    <div class="role-icon mx-auto mb-3" style="width: 80px; height: 80px; font-size: 32px;">
                                        <i class="bx bx-shield-quarter text-primary"></i>
                                    </div>
                                    <h4 class="mb-1">${role.display_name}</h4>
                                    <p class="text-muted mb-2 role-code">${role.name}</p>
                                    <span class="badge bg-label-primary">${stats.permissions_count || 0} Permissions</span>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <h6>Role Information</h6>
                                        <ul class="list-unstyled">
                                            <li><strong>Role ID:</strong> #${role.id}</li>
                                            <li><strong>Internal Name:</strong> ${role.name}</li>
                                            <li><strong>Display Name:</strong> ${role.display_name}</li>
                                            <li><strong>Description:</strong> ${role.description || 'No description'}</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <h6>Statistics</h6>
                                        <ul class="list-unstyled">
                                            <li><strong>Users Count:</strong> ${stats.users_count || 0}</li>
                                            <li><strong>Permissions:</strong> ${stats.permissions_count || 0}</li>
                                            <li><strong>Created:</strong> ${stats.days_since_created || 0} days ago</li>
                                            <li><strong>Last Updated:</strong> ${stats.last_updated || 'N/A'}</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <h6>Permissions</h6>
                                    <div>${permissionsHtml}</div>
                                </div>
                                ${role.users && role.users.length > 0 ? `
                                <div class="mt-3">
                                    <h6>Assigned Users</h6>
                                    <div>${usersHtml}</div>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    `;
                    
                    $('#roleDetailContent').html(content);
                    $('#editRoleFromDetail').data('role-id', roleId);
                    $('#roleDetailModal').modal('show');
                } else {
                    Swal.fire('Error!', response.message || 'Failed to load role details', 'error');
                }
            })
            .fail(function(xhr) {
                console.log('Show error:', xhr);
                Swal.fire('Error!', 'Failed to load role details', 'error');
            });
    });

    // Edit Role
    $(document).on('click', '.btn-edit', function(e) {
        e.preventDefault();
        console.log('Edit button clicked');
        
        var roleId = $(this).data('id');
        console.log('Edit Role ID:', roleId);
        
        if (!roleId) {
            console.error('Role ID not found');
            return;
        }
        
        resetForm();
        
        $.get("{{ url('roles') }}/" + roleId + "/edit")
            .done(function(response) {
                console.log('Edit response:', response);
                
                if (response.success) {
                    var role = response.data;
                    $('#roleModalTitle').html('<i class="bx bx-edit me-2"></i>Edit Role');
                    $('#btnSaveRole').html('<i class="bx bx-save me-1"></i>Update Role');
                    $('#name').val(role.name);
                    $('#display_name').val(role.display_name);
                    $('#description').val(role.description);
                    
                    // Check permissions
                    if (role.permissions) {
                        role.permissions.forEach(function(permission) {
                            $('#permission_' + permission.id).prop('checked', true);
                            $('#permission_' + permission.id).closest('.permission-item').addClass('selected');
                        });
                    }
                    
                    $('#roleForm').data('role-id', roleId);
                    $('#roleModal').modal('show');
                } else {
                    Swal.fire('Error!', response.message || 'Failed to load role data', 'error');
                }
            })
            .fail(function(xhr) {
                console.log('Edit error:', xhr);
                Swal.fire('Error!', 'Failed to load role data', 'error');
            });
    });

    // Delete Role
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        console.log('Delete button clicked');
        
        var roleId = $(this).data('id');
        var roleName = $(this).data('name');
        
        console.log('Delete Role ID:', roleId, 'Name:', roleName);
        
        if (!roleId) {
            console.error('Role ID not found');
            return;
        }
        
        Swal.fire({
            title: 'Are you sure?',
            text: `Delete role "${roleName}"? This action cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('roles') }}/" + roleId,
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
                        console.log('Delete response:', response);
                        
                        if (response.success) {
                            Swal.fire('Deleted!', response.message, 'success');
                            table.ajax.reload();
                            clearSelection();
                        } else {
                            Swal.fire('Error!', response.message || 'Failed to delete role', 'error');
                        }
                    },
                    error: function(xhr) {
                        console.log('Delete error:', xhr);
                        var response = xhr.responseJSON;
                        Swal.fire('Error!', response?.message || 'Something went wrong!', 'error');
                    }
                });
            }
        });
    });

    // Clone Role
    $(document).on('click', '.btn-clone', function(e) {
        e.preventDefault();
        console.log('Clone button clicked');
        
        var roleId = $(this).data('id');
        
        console.log('Clone Role ID:', roleId);
        
        if (!roleId) {
            console.error('Role ID not found');
            return;
        }
        
        Swal.fire({
            title: 'Clone Role',
            text: 'Are you sure you want to clone this role?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#696cff',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, clone it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('roles') }}/" + roleId + "/clone",
                    type: 'POST',
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Cloning...',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        console.log('Clone response:', response);
                        
                        if (response.success) {
                            Swal.fire('Cloned!', response.message, 'success');
                            table.ajax.reload();
                        } else {
                            Swal.fire('Error!', response.message || 'Failed to clone role', 'error');
                        }
                    },
                    error: function(xhr) {
                        console.log('Clone error:', xhr);
                        var response = xhr.responseJSON;
                        Swal.fire('Error!', response?.message || 'Something went wrong!', 'error');
                    }
                });
            }
        });
    });

    // Manage Permissions (opens edit modal)
    $(document).on('click', '.btn-permissions', function(e) {
        e.preventDefault();
        console.log('Manage permissions clicked');
        
        var roleId = $(this).data('id');
        $('.btn-edit[data-id="' + roleId + '"]').trigger('click');
        
        // Focus on permissions section after modal opens
        setTimeout(function() {
            $('#permissionsContainer')[0].scrollIntoView({ behavior: 'smooth' });
        }, 500);
    });

    // Bulk Actions
    window.bulkAction = function(action) {
        var selectedIds = $('.role-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        console.log('Bulk action:', action, 'IDs:', selectedIds);

        if (selectedIds.length === 0) {
            Swal.fire('Warning!', 'Please select roles first.', 'warning');
            return;
        }

        var actionText = action === 'delete' ? 'delete' : action;

        Swal.fire({
            title: 'Are you sure?',
            text: `You want to ${actionText} ${selectedIds.length} selected roles?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Yes, ${actionText}!`
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("roles.bulk-action") }}',
                    type: 'POST',
                    data: {
                        action: action,
                        role_ids: selectedIds
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        console.log('Bulk action response:', response);
                        
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success');
                            table.ajax.reload();
                            clearSelection();
                        } else {
                            Swal.fire('Error!', response.message || 'Bulk action failed', 'error');
                        }
                    },
                    error: function(xhr) {
                        console.log('Bulk action error:', xhr);
                        var response = xhr.responseJSON;
                        Swal.fire('Error!', response?.message || 'Something went wrong!', 'error');
                    }
                });
            }
        });
    }

    // Add Role
    $('#btnAddRole').click(function() {
        console.log('Add role button clicked');
        resetForm();
        $('#roleModalTitle').html('<i class="bx bx-shield-plus me-2"></i>Add New Role');
        $('#btnSaveRole').html('<i class="bx bx-save me-1"></i>Save Role');
        $('#roleModal').modal('show');
    });

    // Export Roles
    $('#btnExport').click(function() {
        console.log('Export button clicked');
        var params = new URLSearchParams();
        if ($('#usersFilter').val()) params.append('users_filter', $('#usersFilter').val());
        if ($('#permissionsFilter').val()) params.append('permissions_filter', $('#permissionsFilter').val());
        if ($('#dateRange').val()) params.append('date_range', $('#dateRange').val());
        
        window.location.href = '{{ route("roles.export") }}?' + params.toString();
    });

    // Refresh Table
    $('#btnRefresh').click(function() {
        console.log('Refresh button clicked');
        table.ajax.reload();
        Swal.fire({
            icon: 'success',
            title: 'Refreshed!',
            text: 'Role data has been refreshed.',
            timer: 1500,
            showConfirmButton: false
        });
    });

    // Form Submit
    $('#roleForm').submit(function(e) {
        e.preventDefault();
        console.log('Form submitted');
        
        var formData = $(this).serialize();
        var roleId = $(this).data('role-id');
        var url = roleId ? "{{ url('roles') }}/" + roleId : "{{ route('roles.store') }}";
        var method = roleId ? 'PUT' : 'POST';
        
        console.log('Form URL:', url, 'Method:', method);
        
        if (roleId) {
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
                $('#btnSaveRole').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');
            },
            success: function(response) {
                console.log('Form response:', response);
                
                if (response.success) {
                    $('#roleModal').modal('hide');
                    Swal.fire('Success!', response.message, 'success');
                    table.ajax.reload();
                } else {
                    Swal.fire('Error!', response.message || 'Something went wrong!', 'error');
                }
            },
            error: function(xhr) {
                console.log('Form error:', xhr);
                
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
                var isEdit = $('#roleModalTitle').text().includes('Edit');
                $('#btnSaveRole').prop('disabled', false).html(isEdit ? '<i class="bx bx-save me-1"></i>Update Role' : '<i class="bx bx-save me-1"></i>Save Role');
            }
        });
    });

    // Edit from Detail Modal
    $('#editRoleFromDetail').click(function() {
        var roleId = $(this).data('role-id');
        console.log('Edit from detail, Role ID:', roleId);
        
        $('#roleDetailModal').modal('hide');
        $('.btn-edit[data-id="' + roleId + '"]').trigger('click');
    });

    // Permission checkbox interactions
    $(document).on('change', 'input[name="permissions[]"]', function() {
        var permissionItem = $(this).closest('.permission-item');
        if ($(this).is(':checked')) {
            permissionItem.addClass('selected');
        } else {
            permissionItem.removeClass('selected');
        }
    });

    // Permission item click (outside checkbox)
    $(document).on('click', '.permission-item', function(e) {
        if (e.target.type !== 'checkbox' && e.target.tagName !== 'LABEL') {
            var checkbox = $(this).find('input[type="checkbox"]');
            checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
        }
    });

    // Auto-generate role name from display name
    $('#display_name').on('input', function() {
        var displayName = $(this).val();
        var roleName = displayName.toLowerCase()
            .replace(/[^a-z0-9\s]/g, '')
            .replace(/\s+/g, '_')
            .replace(/_+/g, '_')
            .replace(/^_|_$/g, '');
        
        if (!$('#roleForm').data('role-id')) { // Only for new roles
            $('#name').val(roleName);
        }
    });

    // Form Reset Function
    function resetForm() {
        $('#roleForm')[0].reset();
        $('#roleForm').removeData('role-id');
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('.permission-item').removeClass('selected');
        $('input[name="permissions[]"]').prop('checked', false);
    }
    
    console.log('All event handlers attached');
});
</script>
@endpush