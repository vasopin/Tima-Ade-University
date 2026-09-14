<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRoleId = DB::table('roles')->where('slug', 'super_admin')->value('id');
        $adminRoleId      = DB::table('roles')->where('slug', 'admin')->value('id');
        $teacherRoleId    = DB::table('roles')->where('slug', 'teacher')->value('id');
        $studentRoleId    = DB::table('roles')->where('slug', 'student')->value('id');
        $parentRoleId     = DB::table('roles')->where('slug', 'parent')->value('id');

        // Use a stable local demo password unless a project-specific override is supplied.
        // This keeps the Super Admin account usable for the current demo and seeded-role flows.
        $seedSuperPassword = env('SEED_SUPER_ADMIN_PASSWORD', 'password');

        $superAdminEmail = 'superadmin@school.com';
        DB::table('users')->updateOrInsert(
            ['email' => $superAdminEmail],
            [
                'role_id'    => $superAdminRoleId,
                'name'       => 'Super Administrator',
                'email'      => $superAdminEmail,
                'password'   => Hash::make($seedSuperPassword),
                'phone'      => '+1-555-0099',
                'is_active'  => 1,
                'status'     => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $users = [
            // Admin
            [
                'role_id'    => $adminRoleId,
                'name'       => 'System Administrator',
                'email'      => 'admin@school.com',
                'password'   => Hash::make('password'),
                'phone'      => '+1-555-0100',
                'is_active'  => 1,
                'status'     => 'active',
                'created_at' => now(), 'updated_at' => now(),
            ],
            // Admin
            [
                'role_id'    => $adminRoleId,
                'name'       => 'System Administrator',
                'email'      => 'admin@school.com',
                'password'   => Hash::make('password'),
                'phone'      => '+1-555-0100',
                'is_active'  => 1,
                'status'     => 'active',
                'created_at' => now(), 'updated_at' => now(),
            ],
            // Teachers
            [
                'role_id'    => $teacherRoleId,
                'name'       => 'Dr. Sarah Johnson',
                'email'      => 'teacher1@school.com',
                'password'   => Hash::make('password'),
                'phone'      => '+1-555-0101',
                'is_active'  => 1,
                'status'     => 'active',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'role_id'    => $teacherRoleId,
                'name'       => 'Mr. James Wilson',
                'email'      => 'teacher2@school.com',
                'password'   => Hash::make('password'),
                'phone'      => '+1-555-0102',
                'is_active'  => 1,
                'status'     => 'active',
                'created_at' => now(), 'updated_at' => now(),
            ],
            // Students
            [
                'role_id'    => $studentRoleId,
                'name'       => 'Alice Thompson',
                'email'      => 'student1@school.com',
                'password'   => Hash::make('password'),
                'phone'      => '+1-555-0201',
                'is_active'  => 1,
                'status'     => 'active',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'role_id'    => $studentRoleId,
                'name'       => 'Bob Martinez',
                'email'      => 'student2@school.com',
                'password'   => Hash::make('password'),
                'phone'      => '+1-555-0202',
                'is_active'  => 1,
                'status'     => 'active',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'role_id'    => $studentRoleId,
                'name'       => 'Carol Davis',
                'email'      => 'student3@school.com',
                'password'   => Hash::make('password'),
                'phone'      => '+1-555-0203',
                'is_active'  => 1,
                'status'     => 'active',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'role_id'    => $studentRoleId,
                'name'       => 'David Lee',
                'email'      => 'student4@school.com',
                'password'   => Hash::make('password'),
                'phone'      => '+1-555-0204',
                'is_active'  => 1,
                'status'     => 'active',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'role_id'    => $studentRoleId,
                'name'       => 'Emma Garcia',
                'email'      => 'student5@school.com',
                'password'   => Hash::make('password'),
                'phone'      => '+1-555-0205',
                'is_active'  => 1,
                'status'     => 'active',
                'created_at' => now(), 'updated_at' => now(),
            ],
            // Parent
            [
                'role_id'    => $parentRoleId,
                'name'       => 'Robert Thompson',
                'email'      => 'parent1@school.com',
                'password'   => Hash::make('password'),
                'phone'      => '+1-555-0301',
                'is_active'  => 1,
                'status'     => 'active',
                'created_at' => now(), 'updated_at' => now(),
            ],
            // Staff
            [
                'role_id'    => DB::table('roles')->where('slug', 'staff')->value('id'),
                'name'       => 'Staff Test User',
                'email'      => 'staff1@school.com',
                'password'   => Hash::make('password'),
                'phone'      => '+1-555-0401',
                'is_active'  => 1,
                'status'     => 'active',
                'created_at' => now(), 'updated_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(['email' => $user['email']], $user);
        }
    }
}
