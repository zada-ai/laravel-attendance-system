<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'mark-attendance',
            'submit-leave',
            'view-attendance',
            'view-tasks',
            'submit-task',
            'assign-task',
            'approve-leave',
            'approve-task',
            'view-reports',
            'manage-students',
            'manage-users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $student = Role::firstOrCreate([
            'name' => 'student',
            'guard_name' => 'web',
        ]);
        $student->syncPermissions([
            'mark-attendance',
            'submit-leave',
            'view-attendance',
            'view-tasks',
            'submit-task',
            'view-reports',
        ]);

        $teacher = Role::firstOrCreate([
            'name' => 'teacher',
            'guard_name' => 'web',
        ]);
        $teacher->syncPermissions([
            'mark-attendance',
            'assign-task',
            'approve-leave',
            'approve-task',
            'view-attendance',
            'view-reports',
        ]);

        $hr = Role::firstOrCreate([
            'name' => 'hr',
            'guard_name' => 'web',
        ]);
        $hr->syncPermissions([
            'approve-leave',
            'view-attendance',
            'view-reports',
        ]);

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $admin->syncPermissions(Permission::all());
    }
}
