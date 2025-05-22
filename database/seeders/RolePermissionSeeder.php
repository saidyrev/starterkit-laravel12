<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Buat permissions
        $permissions = [
            ['name' => 'view_dashboard', 'display_name' => 'View Dashboard'],
            ['name' => 'manage_users', 'display_name' => 'Manage Users'],
            ['name' => 'manage_roles', 'display_name' => 'Manage Roles'],
            ['name' => 'view_reports', 'display_name' => 'View Reports'],
            ['name' => 'create_content', 'display_name' => 'Create Content'],
            ['name' => 'edit_content', 'display_name' => 'Edit Content'],
            ['name' => 'delete_content', 'display_name' => 'Delete Content'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Buat roles
        $adminRole = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Full access to all features'
        ]);

        $editorRole = Role::create([
            'name' => 'editor',
            'display_name' => 'Editor',
            'description' => 'Can create and edit content'
        ]);

        $userRole = Role::create([
            'name' => 'user',
            'display_name' => 'User',
            'description' => 'Basic user access'
        ]);

        // Assign permissions to roles
        $adminRole->permissions()->attach(Permission::all());
        
        $editorRole->permissions()->attach(
            Permission::whereIn('name', [
                'view_dashboard', 
                'view_reports', 
                'create_content', 
                'edit_content'
            ])->pluck('id')
        );
        
        $userRole->permissions()->attach(
            Permission::whereIn('name', ['view_dashboard'])->pluck('id')
        );

        // Buat user admin default
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
        ]);
    }
}