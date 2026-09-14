<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $password = env('DEMO_ROLE_PASSWORD', 'password');
        $now = now();

        $accounts = [
            ['name' => 'Super Administrator', 'email' => 'superadmin@school.com', 'role' => 'super_admin'],
            ['name' => 'System Administrator', 'email' => 'admin@school.com', 'role' => 'admin'],
            ['name' => 'University Staff', 'email' => 'staff@school.com', 'role' => 'staff'],
            ['name' => 'Demo Faculty Member', 'email' => 'faculty@school.com', 'role' => 'teacher'],
            ['name' => 'Demo Student', 'email' => 'student@school.com', 'role' => 'student'],
            ['name' => 'Demo Parent', 'email' => 'parent@school.com', 'role' => 'parent'],
            ['name' => 'Admissions Officer', 'email' => 'admissions@school.com', 'role' => 'admissions_officer'],
            ['name' => 'Finance Officer', 'email' => 'finance@school.com', 'role' => 'finance_officer'],
            ['name' => 'Registrar', 'email' => 'registrar@school.com', 'role' => 'registrar'],
            ['name' => 'HR Officer', 'email' => 'hr@school.com', 'role' => 'hr_officer'],
            ['name' => 'Librarian', 'email' => 'librarian@school.com', 'role' => 'librarian'],
        ];

        foreach ($accounts as $account) {
            $roleId = DB::table('roles')->where('slug', $account['role'])->value('id');

            if (! $roleId || DB::table('users')->where('email', $account['email'])->exists()) {
                continue;
            }

            DB::table('users')->insert([
                'role_id' => $roleId,
                'name' => $account['name'],
                'email' => $account['email'],
                'password' => Hash::make($password),
                'is_active' => true,
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('users')->whereIn('email', [
            'faculty@school.com',
            'student@school.com',
            'parent@school.com',
            'admissions@school.com',
            'finance@school.com',
            'registrar@school.com',
            'hr@school.com',
            'librarian@school.com',
        ])->delete();
    }
};
