<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Helpers\SweetAlert;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $roles = Role::withCount('users')->select('roles.*');
            
            return DataTables::of($roles)
                ->addIndexColumn()
                ->addColumn('checkbox', function($role) {
                    return '<input type="checkbox" class="role-checkbox" value="' . $role->id . '">';
                })
                ->addColumn('users_count_badge', function($role) {
                    $badgeClass = $role->users_count > 0 ? 'bg-label-info' : 'bg-label-secondary';
                    return '<span class="badge ' . $badgeClass . '">' . $role->users_count . ' Users</span>';
                })
                ->addColumn('permissions_count', function($role) {
                    $permissionsCount = $role->permissions()->count();
                    return '<span class="badge bg-label-primary">' . $permissionsCount . ' Permissions</span>';
                })
                ->addColumn('description_short', function($role) {
                    return $role->description ? 
                        '<span title="' . $role->description . '">' . 
                        (strlen($role->description) > 50 ? substr($role->description, 0, 50) . '...' : $role->description) . 
                        '</span>' : 
                        '<span class="text-muted">No description</span>';
                })
                ->addColumn('created_formatted', function($role) {
                    return $role->created_at->format('M d, Y') . '<br><small class="text-muted">' . $role->created_at->diffForHumans() . '</small>';
                })
                ->addColumn('action', function($role) {
                    $actions = '<div class="dropdown">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item btn-show" href="javascript:void(0)" data-id="' . $role->id . '">
                                <i class="bx bx-show me-1"></i> View Details
                            </a>
                            <a class="dropdown-item btn-edit" href="javascript:void(0)" data-id="' . $role->id . '">
                                <i class="bx bx-edit-alt me-1"></i> Edit
                            </a>
                            <a class="dropdown-item btn-clone" href="javascript:void(0)" data-id="' . $role->id . '">
                                <i class="bx bx-copy me-1"></i> Clone
                            </a>
                            <a class="dropdown-item btn-permissions" href="javascript:void(0)" data-id="' . $role->id . '">
                                <i class="bx bx-lock-open-alt me-1"></i> Manage Permissions
                            </a>';
                    
                    if ($role->users_count == 0) {
                        $actions .= '<div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger btn-delete" href="javascript:void(0)" data-id="' . $role->id . '" data-name="' . $role->display_name . '">
                                <i class="bx bx-trash me-1"></i> Delete
                            </a>';
                    }
                    
                    $actions .= '</div></div>';
                    
                    return $actions;
                })
                ->rawColumns(['checkbox', 'users_count_badge', 'permissions_count', 'description_short', 'created_formatted', 'action'])
                ->make(true);
        }

        $permissions = Permission::all();
        return view('roles.index', compact('permissions'));
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
            'message' => 'Role created successfully!',
            'data' => $role
        ]);
    }

    public function show(Role $role)
    {
        $role->load('permissions', 'users');
        return response()->json([
            'success' => true,
            'data' => $role
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
            'message' => 'Role updated successfully!',
            'data' => $role
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

    // Clone Role
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

    // Bulk Delete
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:roles,id'
        ]);

        $rolesWithUsers = Role::whereIn('id', $request->ids)
            ->has('users')
            ->pluck('display_name');

        if ($rolesWithUsers->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete roles that have users assigned: ' . $rolesWithUsers->implode(', ')
            ], 400);
        }

        $deletedCount = Role::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$deletedCount} roles deleted successfully!"
        ]);
    }

    // Export Roles
    public function export(Request $request)
    {
        $roles = Role::withCount('users')->with('permissions')->get();
        
        $csvData = [];
        $csvData[] = ['Name', 'Display Name', 'Description', 'Users Count', 'Permissions', 'Created At']; // Header
        
        foreach ($roles as $role) {
            $permissions = $role->permissions->pluck('display_name')->implode(', ');
            $csvData[] = [
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