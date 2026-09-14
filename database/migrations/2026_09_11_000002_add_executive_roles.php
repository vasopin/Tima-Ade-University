<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            ['name' => 'President', 'slug' => 'president', 'description' => 'University-wide executive leadership access'],
            ['name' => 'Chancellor', 'slug' => 'chancellor', 'description' => 'University-wide executive leadership access'],
        ] as $role) {
            DB::table('roles')->updateOrInsert(
                ['slug' => $role['slug']],
                array_merge($role, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    public function down(): void
    {
        DB::table('roles')->whereIn('slug', ['president', 'chancellor'])->delete();
    }
};
