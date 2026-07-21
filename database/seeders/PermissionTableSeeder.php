<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $modules = config('PermissionModule.modules');
        $rolesConfig = config('PermissionModule.roles');

        // Create Permissions
        foreach ($modules as $module => $permissions) {
            foreach ($permissions as $permission) {
                Permission::findOrCreate($permission, 'web');
            }
        }

        // Create Roles and Assign Permissions
        foreach ($rolesConfig as $roleName => $assignedModules) {
            $role = Role::findOrCreate($roleName, 'web');

            $permissionsToAssign = [];
            foreach ($assignedModules as $moduleName) {
                if (isset($modules[$moduleName])) {
                    foreach ($modules[$moduleName] as $pName) {
                        $permissionsToAssign[] = Permission::where('name', $pName)->where('guard_name', 'web')->first();
                    }
                }
            }

            $role->syncPermissions($permissionsToAssign);
        }

        // Create a default Admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );

        $admin->assignRole('Admin');
    }
}
