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
                ->addColumn('users_count_badge', function($role) {
                    return '<span class="badge bg-label-info">' . $role->users_count . ' Users</span>';
                })
                ->addColumn('description_short', function($role) {
                    return $role->description ? 
                        '<span title="' . $role->description . '">' . 
                        (strlen($role->description) > 50 ? substr($role->description, 0, 50) . '...' : $role->description) . 
                        '</span>' : 
                        '<span class="text-muted">No description</span>';
                })
                ->addColumn('action', function($role) {
                    $actions = '<div class="dropdown">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item btn-show" href="javascript:void(0)" data-id="' . $role->id . '">
                                <i class="bx bx-show me-1"></i> View
                            </a>
                            <a class="dropdown-item btn-edit" href="javascript:void(0)" data-id="' . $role->id . '">
                                <i class="bx bx-edit-alt me-1"></i> Edit
                            </a>';
                    
                    if ($role->users_count == 0) {
                        $actions .= '<a class="dropdown-item text-danger btn-delete" href="javascript:void(0)" data-id="' . $role->id . '" data-name="' . $role->display_name . '">
                                <i class="bx bx-trash me-1"></i> Delete
                            </a>';
                    }
                    
                    $actions .= '</div></div>';
                    
                    return $actions;
                })
                ->rawColumns(['users_count_badge', 'description_short', 'action'])
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
            'message' => 'Role created successfully!'
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

        $role->permissions()->detach();
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully!'
        ]);
    }
}