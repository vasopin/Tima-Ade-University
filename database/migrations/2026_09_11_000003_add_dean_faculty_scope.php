<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('faculty_id')->nullable()->after('role_id')->constrained()->nullOnDelete();
            $table->index(['faculty_id', 'role_id']);
        });

        DB::table('roles')->updateOrInsert(
            ['slug' => 'dean'],
            ['name' => 'Dean', 'description' => 'Faculty-scoped academic leadership access', 'created_at' => now(), 'updated_at' => now()]
        );
    }

    public function down(): void
    {
        DB::table('roles')->where('slug', 'dean')->delete();
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['faculty_id']);
            $table->dropIndex(['faculty_id', 'role_id']);
            $table->dropColumn('faculty_id');
        });
    }
};
