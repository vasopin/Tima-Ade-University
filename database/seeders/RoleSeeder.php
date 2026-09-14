<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Super Admin', 'slug' => 'super_admin', 'description' => 'Unrestricted super administrator access'],
            ['name' => 'Admin',       'slug' => 'admin',       'description' => 'Full administrative school access'],
            ['name' => 'Staff',       'slug' => 'staff',       'description' => 'University staff workspace access'],
            ['name' => 'Teacher',     'slug' => 'teacher',     'description' => 'Manage classes, attendance, and grades'],
            ['name' => 'Student',     'slug' => 'student',     'description' => 'View own profile, courses, and grades'],
            ['name' => 'Parent',      'slug' => 'parent',      'description' => 'View linked children profiles and reports'],
            ['name' => 'Admissions Officer', 'slug' => 'admissions_officer', 'description' => 'Manage admissions applications and decisions'],
            ['name' => 'Finance Officer',    'slug' => 'finance_officer',    'description' => 'Manage student fees and payment records'],
            ['name' => 'Registrar',   'slug' => 'registrar',   'description' => 'Manage academic records and transcripts'],
            ['name' => 'HR Officer',  'slug' => 'hr_officer',  'description' => 'Manage HR records, leave, and payroll'],
            ['name' => 'Librarian',   'slug' => 'librarian',   'description' => 'Manage library and book borrowing'],
            ['name' => 'President',   'slug' => 'president',   'description' => 'University-wide executive leadership access'],
            ['name' => 'Chancellor',  'slug' => 'chancellor',  'description' => 'University-wide executive leadership access'],
            ['name' => 'Dean',        'slug' => 'dean',        'description' => 'Faculty-scoped academic leadership access'],
            ['name' => 'Department Head', 'slug' => 'department_head', 'description' => 'Department-scoped academic leadership access'],
            ['name' => 'Academic Advisor', 'slug' => 'academic_advisor', 'description' => 'Assigned-student academic advising access'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(['slug' => $role['slug']], array_merge($role, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
