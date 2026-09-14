<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NoticeSeeder extends Seeder
{
    public function run(): void
    {
        $notices = [
            [
                'title'          => 'Fall 2026 Admissions Now Open for Grades 9 through 12',
                'content'        => 'Tima-Ade University is pleased to announce that admissions for the upcoming 2026 academic term are now officially open. Early applicant scholarship assessments will take place starting next month. Prospective students and parents are invited to schedule campus tours or submit applications online.',
                'category'       => 'Admissions',
                'published_date' => now()->subDays(2)->toDateString(),
                'is_pinned'      => 1,
                'is_active'      => 1,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'title'          => 'Annual Science, Robotics & Innovation Exhibition',
                'content'        => 'Our students from Computer Science and Natural Science departments will be showcasing their innovative projects, AI prototypes, and chemistry experiments in the Main Auditorium on Friday, September 5th. All parents and visitors are welcome!',
                'category'       => 'Event',
                'published_date' => now()->subDays(5)->toDateString(),
                'is_pinned'      => 1,
                'is_active'      => 1,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'title'          => 'Mid-Term Examination Schedule & Study Guidelines Released',
                'content'        => 'The timetable for Mid-Term Assessments has been finalized and distributed through the student and parent portals. Students are encouraged to attend supplementary tutorial clinics organized by faculty.',
                'category'       => 'Academic',
                'published_date' => now()->subDays(8)->toDateString(),
                'is_pinned'      => 0,
                'is_active'      => 1,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'title'          => 'Tima-Ade University Inter-School Athletic Meet and Sports Gala',
                'content'        => 'Join us in cheering on our Tima-Ade University athletic teams as they compete against regional high schools in soccer, track and field, basketball, and tennis at the Olympic sports ground.',
                'category'       => 'Sports',
                'published_date' => now()->subDays(12)->toDateString(),
                'is_pinned'      => 0,
                'is_active'      => 1,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ];

        foreach ($notices as $notice) {
            DB::table('notices')->insert($notice);
        }
    }
}
