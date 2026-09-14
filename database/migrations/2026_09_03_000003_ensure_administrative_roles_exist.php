<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $roles = [
            ['name' => 'Admissions Officer', 'slug' => 'admissions_officer', 'description' => 'Manage admissions applications and decisions'],
            ['name' => 'Finance Officer', 'slug' => 'finance_officer', 'description' => 'Manage student fees and payment records'],
            ['name' => 'Registrar', 'slug' => 'registrar', 'description' => 'Manage academic records and transcripts'],
            ['name' => 'HR Officer', 'slug' => 'hr_officer', 'description' => 'Manage HR records, leave, and payroll'],
            ['name' => 'Librarian', 'slug' => 'librarian', 'description' => 'Manage library and book borrowing'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['slug' => $role['slug']],
                array_merge($role, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    public function down(): void
    {
        DB::table('roles')->whereIn('slug', [
            'admissions_officer',
            'finance_officer',
            'registrar',
            'hr_officer',
            'librarian',
        ])->delete();
    }
};
