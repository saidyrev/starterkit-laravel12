<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Helpers\SweetAlert;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
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

        SweetAlert::success('Success!', 'Role has been created successfully.');
        return redirect()->route('roles.index');
    }

    public function show(Role $role)
    {
        $role->load('permissions', 'users');
        return view('roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $role->load('permissions');
        return view('roles.edit', compact('role', 'permissions'));
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

        SweetAlert::success('Success!', 'Role has been updated successfully.');
        return redirect()->route('roles.index');
    }

    public function destroy(Role $role)
    {
        // Prevent deleting role if it has users
        if ($role->users()->count() > 0) {
            SweetAlert::error('Error!', 'Cannot delete role that has users assigned.');
            return redirect()->back();
        }

        $role->permissions()->detach();
        $role->delete();

        SweetAlert::success('Deleted!', 'Role has been deleted successfully.');
        return redirect()->route('roles.index');
    }
}