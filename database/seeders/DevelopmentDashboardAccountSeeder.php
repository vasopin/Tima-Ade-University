<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Models\AdvisorAssignment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DevelopmentDashboardAccountSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('DASHBOARD_TEST_PASSWORD', 'password');
        $facultyId = Faculty::query()->value('id');
        $departmentId = Department::query()->value('id');

        $accounts = [
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'staff' => 'Staff',
            'teacher' => 'Teacher',
            'student' => 'Student',
            'parent' => 'Parent',
            'admissions_officer' => 'Admissions Officer',
            'finance_officer' => 'Finance Officer',
            'registrar' => 'Registrar',
            'hr_officer' => 'HR Officer',
            'librarian' => 'Librarian',
            'president' => 'President',
            'chancellor' => 'Chancellor',
            'dean' => 'Dean',
            'department_head' => 'Department Head',
            'academic_advisor' => 'Academic Advisor',
        ];

        foreach ($accounts as $slug => $name) {
            $roleId = Role::query()->where('slug', $slug)->value('id');
            if (! $roleId) {
                continue;
            }

            $user = User::query()->updateOrCreate(
                ['email' => "dashboard.{$slug}@example.test"],
                [
                    'role_id' => $roleId,
                    'faculty_id' => $slug === 'dean' ? $facultyId : null,
                    'name' => "Dashboard {$name}",
                    'password' => Hash::make($password),
                    'is_active' => true,
                    'status' => 'active',
                ]
            );

            if ($slug === 'department_head' && $departmentId) {
                Department::query()->whereKey($departmentId)->update(['head_user_id' => $user->id]);
            }

            if ($slug === 'academic_advisor') {
                $studentId = Student::query()->value('id');
                $assignedBy = User::query()->whereHas('role', fn ($query) => $query->where('slug', Role::ADMIN))->value('id');
                if ($studentId) {
                    AdvisorAssignment::query()->updateOrCreate(
                        ['student_id' => $studentId, 'advisor_id' => $user->id],
                        [
                            'assigned_by' => $assignedBy,
                            'assigned_at' => now()->toDateString(),
                            'is_active' => true,
                            'notes' => 'Development dashboard account assignment.',
                        ]
                    );
                }
            }
        }
    }
}
