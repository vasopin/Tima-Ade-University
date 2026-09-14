<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $teacher1UserId = DB::table('users')->where('email', 'teacher1@school.com')->value('id');
        $teacher2UserId = DB::table('users')->where('email', 'teacher2@school.com')->value('id');
        $class1Id       = DB::table('school_classes')->where('grade_level', '10')->value('id');
        $class2Id       = DB::table('school_classes')->where('grade_level', '11')->value('id');

        $teachers = [
            [
                'user_id'          => $teacher1UserId,
                'employee_id'      => 'EMP-001',
                'qualification'    => 'Ph.D. in Mathematics',
                'specialization'   => 'Mathematics & Computer Science',
                'joining_date'     => '2020-08-15',
                'address'          => '123 Elm Street, Springfield',
                'gender'           => 'female',
                'date_of_birth'    => '1985-03-22',
                'emergency_contact'=> '+1-555-9001',
                'is_class_teacher' => 1,
                'class_teacher_of' => $class1Id,
                'created_at'       => now(), 'updated_at' => now(),
            ],
            [
                'user_id'          => $teacher2UserId,
                'employee_id'      => 'EMP-002',
                'qualification'    => 'M.Sc. in Science Education',
                'specialization'   => 'Science & History',
                'joining_date'     => '2019-01-10',
                'address'          => '456 Oak Avenue, Riverside',
                'gender'           => 'male',
                'date_of_birth'    => '1982-07-14',
                'emergency_contact'=> '+1-555-9002',
                'is_class_teacher' => 1,
                'class_teacher_of' => $class2Id,
                'created_at'       => now(), 'updated_at' => now(),
            ],
        ];

        foreach ($teachers as $teacher) {
            DB::table('teachers')->updateOrInsert(['user_id' => $teacher['user_id']], $teacher);
        }
    }
}
