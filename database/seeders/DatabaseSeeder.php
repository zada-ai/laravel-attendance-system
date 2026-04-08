<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $leaveTypes = [
            ['name' => 'Sick Leave', 'slug' => 'sick-leave', 'description' => 'Leave for illness or medical reasons'],
            ['name' => 'Casual Leave', 'slug' => 'casual-leave', 'description' => 'Short-term personal leave'],
            ['name' => 'Emergency Leave', 'slug' => 'emergency-leave', 'description' => 'Unplanned leave for urgent matters'],
        ];

        foreach ($leaveTypes as $type) {
            LeaveType::firstOrCreate(['slug' => $type['slug']], $type);
        }

        $admin = User::firstOrCreate(
            ['email' => 'admin123@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('12345'),
                'role' => 'admin',
            ]
        );
        $admin->assignRole('admin');

        $teacher = User::firstOrCreate(
            ['email' => 'teacher@example.com'],
            [
                'name' => 'Teacher User',
                'password' => Hash::make('12345'),
                'role' => 'teacher',
            ]
        );
        $teacher->assignRole('teacher');

        $hr = User::firstOrCreate(
            ['email' => 'hr@example.com'],
            [
                'name' => 'HR User',
                'password' => Hash::make('12345'),
                'role' => 'hr',
            ]
        );
        $hr->assignRole('hr');

        $student = User::firstOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'Student User',
                'password' => Hash::make('12345'),
                'role' => 'student',
            ]
        );
        $student->assignRole('student');

        User::whereNotNull('role')->get()->each(function (User $user) {
            if (in_array($user->role, ['admin', 'teacher', 'hr', 'student'], true)) {
                $user->syncRoles($user->role);
            }
        });
    }
}
