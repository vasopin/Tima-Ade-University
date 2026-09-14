<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('notifications')->updateOrInsert(
            ['title' => 'Academic Term Schedule Published'],
            [
                'body' => 'The complete university timetable and exam schedule have been finalized.',
                'role' => 'all',
                'read' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        DB::table('notifications')->updateOrInsert(
            ['title' => 'Grade Submissions Open'],
            [
                'body' => 'Faculty members can now submit midterm evaluation grades.',
                'role' => 'teacher',
                'read' => false,
                'created_at' => $now->subHours(2),
                'updated_at' => $now->subHours(2),
            ]
        );

        DB::table('notifications')->updateOrInsert(
            ['title' => 'Fee Receipt Verified'],
            [
                'body' => 'Your tuition payment for the current semester has been processed.',
                'role' => 'student',
                'read' => false,
                'created_at' => $now->subHours(5),
                'updated_at' => $now->subHours(5),
            ]
        );
    }
}
