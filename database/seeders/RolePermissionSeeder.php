<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // User Management
            ['name' => 'View Users', 'key' => 'users.view', 'group' => 'users'],
            ['name' => 'Create Users', 'key' => 'users.create', 'group' => 'users'],
            ['name' => 'Edit Users', 'key' => 'users.edit', 'group' => 'users'],
            ['name' => 'Delete Users', 'key' => 'users.delete', 'group' => 'users'],

            // Role Management
            ['name' => 'View Roles', 'key' => 'roles.view', 'group' => 'roles'],
            ['name' => 'Create Roles', 'key' => 'roles.create', 'group' => 'roles'],
            ['name' => 'Edit Roles', 'key' => 'roles.edit', 'group' => 'roles'],
            ['name' => 'Delete Roles', 'key' => 'roles.delete', 'group' => 'roles'],

            // Tenant Management (Super Admin only)
            ['name' => 'View Tenants', 'key' => 'tenants.view', 'group' => 'tenants'],
            ['name' => 'Create Tenants', 'key' => 'tenants.create', 'group' => 'tenants'],
            ['name' => 'Edit Tenants', 'key' => 'tenants.edit', 'group' => 'tenants'],
            ['name' => 'Delete Tenants', 'key' => 'tenants.delete', 'group' => 'tenants'],

            // Module Management
            ['name' => 'View Modules', 'key' => 'modules.view', 'group' => 'modules'],
            ['name' => 'Manage Modules', 'key' => 'modules.manage', 'group' => 'modules'],

            // Settings
            ['name' => 'View Settings', 'key' => 'settings.view', 'group' => 'settings'],
            ['name' => 'Edit Settings', 'key' => 'settings.edit', 'group' => 'settings'],

            // Dashboard & Reports
            ['name' => 'View Dashboard', 'key' => 'dashboard.view', 'group' => 'dashboard'],
            ['name' => 'View Reports', 'key' => 'reports.view', 'group' => 'reports'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['key' => $permission['key']],
                $permission
            );
        }

        // Create roles
        $roles = [
            [
                'name' => 'Owner',
                'key' => 'owner',
                'description' => 'Tenant owner with full access to the tenant',
                'is_system' => true,
                'permissions' => [
                    'users.view', 'users.create', 'users.edit', 'users.delete',
                    'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
                    'modules.view', 'modules.manage',
                    'settings.view', 'settings.edit',
                    'dashboard.view', 'reports.view',
                ]
            ],
            [
                'name' => 'Admin',
                'key' => 'admin',
                'description' => 'Tenant administrator with most permissions',
                'is_system' => true,
                'permissions' => [
                    'users.view', 'users.create', 'users.edit',
                    'roles.view',
                    'modules.view',
                    'settings.view', 'settings.edit',
                    'dashboard.view', 'reports.view',
                ]
            ],
            [
                'name' => 'Manager',
                'key' => 'manager',
                'description' => 'Tenant manager with limited administrative permissions',
                'is_system' => true,
                'permissions' => [
                    'users.view', 'users.edit',
                    'modules.view',
                    'settings.view',
                    'dashboard.view', 'reports.view',
                ]
            ],
            [
                'name' => 'User',
                'key' => 'user',
                'description' => 'Regular tenant user with basic permissions',
                'is_system' => true,
                'permissions' => [
                    'dashboard.view',
                ]
            ]
        ];

        foreach ($roles as $roleData) {
            $permissions = $roleData['permissions'];
            unset($roleData['permissions']);

            $role = Role::updateOrCreate(
                ['key' => $roleData['key']],
                $roleData
            );

            // Assign permissions to role
            $permissionIds = Permission::whereIn('key', $permissions)->pluck('id');
            $role->permissions()->sync($permissionIds);
        }
    }
}