<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            ['name' => 'Mathematics',        'code' => 'MATH101', 'description' => 'Algebra, Calculus, and Statistics'],
            ['name' => 'Science',            'code' => 'SCI101',  'description' => 'Physics, Chemistry, Biology'],
            ['name' => 'English Language',   'code' => 'ENG101',  'description' => 'Grammar, Literature, Composition'],
            ['name' => 'History',            'code' => 'HIST101', 'description' => 'World and Local History'],
            ['name' => 'Computer Science',   'code' => 'CS101',   'description' => 'Programming and Digital Literacy'],
            ['name' => 'Physical Education', 'code' => 'PE101',  'description' => 'Sports and Physical Fitness'],
            ['name' => 'Art',                'code' => 'ART101',  'description' => 'Fine Arts and Crafts'],
        ];

        foreach ($subjects as $subject) {
            DB::table('subjects')->updateOrInsert(
                ['code' => $subject['code']],
                array_merge($subject, [
                    'is_active'  => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // Assign subjects to classes (all subjects to all classes by default)
        $classIds   = DB::table('school_classes')->pluck('id');
        $subjectIds = DB::table('subjects')->pluck('id');
        $teacher1Id = DB::table('users')->where('email', 'teacher1@school.com')->value('id');
        $teacher2Id = DB::table('users')->where('email', 'teacher2@school.com')->value('id');

        foreach ($classIds as $classId) {
            foreach ($subjectIds as $index => $subjectId) {
                DB::table('class_subject')->updateOrInsert(
                    ['school_class_id' => $classId, 'subject_id' => $subjectId],
                    [
                        'teacher_id' => ($index % 2 === 0) ? $teacher1Id : $teacher2Id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
