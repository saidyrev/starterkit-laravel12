<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $roles = Role::withCount(['users', 'permissions'])->select('roles.*');
            
            // Apply filters
            if ($request->filled('users_filter')) {
                if ($request->users_filter === 'with_users') {
                    $roles->has('users');
                } else {
                    $roles->doesntHave('users');
                }
            }
            
            if ($request->filled('permissions_filter')) {
                if ($request->permissions_filter === 'with_permissions') {
                    $roles->has('permissions');
                } else {
                    $roles->doesntHave('permissions');
                }
            }
            
            if ($request->filled('date_range')) {
                $dateRange = explode(' to ', $request->date_range);
                if (count($dateRange) === 2) {
                    $roles->whereBetween('created_at', [
                        Carbon::parse($dateRange[0])->startOfDay(),
                        Carbon::parse($dateRange[1])->endOfDay()
                    ]);
                }
            }
            
            return DataTables::of($roles)
                ->addIndexColumn()
                ->addColumn('checkbox', function($role) {
                    return '<input type="checkbox" class="role-checkbox" value="' . $role->id . '">';
                })
                ->addColumn('role_info', function($role) {
                    $iconClass = match($role->name) {
                        'admin' => 'bx-shield-quarter text-danger',
                        'editor' => 'bx-edit text-warning',
                        'manager' => 'bx-user-pin text-info',
                        default => 'bx-group text-primary'
                    };
                    
                    return '
                    <div class="role-info-cell">
                        <div class="role-icon-container">
                            <div class="role-icon">
                                <i class="bx ' . $iconClass . '"></i>
                            </div>
                        </div>
                        <div class="role-details">
                            <div class="role-name">' . $role->display_name . '</div>
                            <div class="role-code" title="' . $role->name . '">' . $role->name . '</div>
                        </div>
                    </div>';
                })
                ->addColumn('description_badge', function($role) {
                    if ($role->description) {
                        $shortDesc = strlen($role->description) > 50 ? 
                            substr($role->description, 0, 50) . '...' : 
                            $role->description;
                        return '<span class="badge bg-label-info rounded-pill" title="' . $role->description . '">
                            <i class="bx bx-info-circle me-1"></i>' . $shortDesc . '
                        </span>';
                    }
                    return '<span class="badge bg-label-secondary rounded-pill">
                        <i class="bx bx-minus me-1"></i>No description
                    </span>';
                })
                ->addColumn('users_count_badge', function($role) {
                    $badgeClass = $role->users_count > 0 ? 'bg-label-success' : 'bg-label-secondary';
                    $icon = $role->users_count > 0 ? 'bx-user-check' : 'bx-user-x';
                    return '<span class="badge ' . $badgeClass . ' rounded-pill">
                        <i class="bx ' . $icon . ' me-1"></i>' . $role->users_count . ' Users
                    </span>';
                })
                ->addColumn('permissions_count_badge', function($role) {
                    $badgeClass = $role->permissions_count > 0 ? 'bg-label-primary' : 'bg-label-secondary';
                    $icon = $role->permissions_count > 0 ? 'bx-shield-alt-2' : 'bx-shield-x';
                    return '<span class="badge ' . $badgeClass . ' rounded-pill">
                        <i class="bx ' . $icon . ' me-1"></i>' . $role->permissions_count . ' Permissions
                    </span>';
                })
                ->addColumn('created_formatted', function($role) {
                    return '<div class="text-nowrap">
                        <small class="text-muted">
                            <i class="bx bx-calendar me-1"></i>
                            ' . $role->created_at->format('M d, Y') . '
                        </small>
                        <br>
                        <small class="text-muted">
                            <i class="bx bx-time me-1"></i>
                            ' . $role->created_at->diffForHumans() . '
                        </small>
                    </div>';
                })
                ->addColumn('action', function($role) {
                    $actions = '
                    <div class="dropdown">
                        <button type="button" class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item btn-show" href="javascript:void(0)" data-id="' . $role->id . '">
                                <i class="bx bx-show me-1"></i> View Details
                            </a>
                            <a class="dropdown-item btn-edit" href="javascript:void(0)" data-id="' . $role->id . '">
                                <i class="bx bx-edit-alt me-1"></i> Edit Role
                            </a>
                            <a class="dropdown-item btn-permissions" href="javascript:void(0)" data-id="' . $role->id . '">
                                <i class="bx bx-lock-open-alt me-1"></i> Manage Permissions
                            </a>
                            <a class="dropdown-item btn-clone" href="javascript:void(0)" data-id="' . $role->id . '">
                                <i class="bx bx-copy me-1"></i> Clone Role
                            </a>';
                    
                    if ($role->users_count == 0) {
                        $actions .= '
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger btn-delete" href="javascript:void(0)" 
                               data-id="' . $role->id . '" data-name="' . $role->display_name . '">
                                <i class="bx bx-trash me-1"></i> Delete Role
                            </a>';
                    }
                    
                    $actions .= '</div></div>';
                    
                    return $actions;
                })
                ->rawColumns(['checkbox', 'role_info', 'description_badge', 'users_count_badge', 'permissions_count_badge', 'created_formatted', 'action'])
                ->make(true);
        }

        $permissions = Permission::all();
        $stats = [
            'total' => Role::count(),
            'with_users' => Role::has('users')->count(),
            'without_users' => Role::doesntHave('users')->count(),
            'this_month' => Role::whereMonth('created_at', Carbon::now()->month)->count(),
        ];

        return view('roles.index', compact('permissions', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->attach($request->permissions);
        }

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully!'
        ]);
    }

    public function show(Role $role)
    {
        $role->load(['permissions', 'users']);
        
        $roleStats = [
            'days_since_created' => $role->created_at->diffInDays(now()),
            'users_count' => $role->users->count(),
            'permissions_count' => $role->permissions->count(),
            'last_updated' => $role->updated_at->diffForHumans(),
        ];

        return response()->json([
            'success' => true,
            'data' => $role,
            'stats' => $roleStats
        ]);
    }

    public function edit(Role $role)
    {
        $role->load('permissions');
        return response()->json([
            'success' => true,
            'data' => $role
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,'.$role->id],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully!'
        ]);
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete role that has users assigned.'
            ], 400);
        }

        $roleName = $role->display_name;
        $role->permissions()->detach();
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => "Role '{$roleName}' deleted successfully!"
        ]);
    }

    public function clone(Role $role)
    {
        $clonedRole = $role->replicate();
        $clonedRole->name = $role->name . '_copy_' . time();
        $clonedRole->display_name = $role->display_name . ' (Copy)';
        $clonedRole->save();

        // Clone permissions
        $permissions = $role->permissions()->pluck('permissions.id');
        $clonedRole->permissions()->attach($permissions);

        return response()->json([
            'success' => true,
            'message' => 'Role cloned successfully!',
            'data' => $clonedRole
        ]);
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete',
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id'
        ]);

        $rolesWithUsers = Role::whereIn('id', $request->role_ids)
            ->has('users')
            ->pluck('display_name');

        if ($rolesWithUsers->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete roles that have users assigned: ' . $rolesWithUsers->implode(', ')
            ], 400);
        }

        $count = Role::whereIn('id', $request->role_ids)->count();
        Role::whereIn('id', $request->role_ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count} roles deleted successfully!"
        ]);
    }

    public function export(Request $request)
    {
        $roles = Role::withCount(['users', 'permissions'])->with('permissions');
        
        // Apply same filters as index
        if ($request->filled('users_filter')) {
            if ($request->users_filter === 'with_users') {
                $roles->has('users');
            } else {
                $roles->doesntHave('users');
            }
        }

        $roles = $roles->get();
        
        $csvData = [];
        $csvData[] = ['ID', 'Name', 'Display Name', 'Description', 'Users Count', 'Permissions', 'Created At'];
        
        foreach ($roles as $role) {
            $permissions = $role->permissions->pluck('display_name')->implode(', ');
            $csvData[] = [
                $role->id,
                $role->name,
                $role->display_name,
                $role->description ?: 'No description',
                $role->users_count,
                $permissions ?: 'No permissions',
                $role->created_at->format('Y-m-d H:i:s')
            ];
        }

        $filename = 'roles_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $handle = fopen('php://temp', 'w+');
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}