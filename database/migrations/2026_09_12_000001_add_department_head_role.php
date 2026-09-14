<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('roles')->updateOrInsert(
            ['slug' => 'department_head'],
            ['name' => 'Department Head', 'description' => 'Department-scoped academic leadership access', 'created_at' => now(), 'updated_at' => now()]
        );
    }

    public function down(): void
    {
        DB::table('roles')->where('slug', 'department_head')->delete();
    }
};
