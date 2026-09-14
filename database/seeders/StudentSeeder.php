<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $class10Id  = DB::table('school_classes')->where('grade_level', '10')->value('id');
        $class11Id  = DB::table('school_classes')->where('grade_level', '11')->value('id');
        $section10A = DB::table('sections')->where('school_class_id', $class10Id)->where('name', 'Section A')->value('id');
        $section10B = DB::table('sections')->where('school_class_id', $class10Id)->where('name', 'Section B')->value('id');
        $section11A = DB::table('sections')->where('school_class_id', $class11Id)->where('name', 'Section A')->value('id');

        $studentEmails = [
            'student1@school.com',
            'student2@school.com',
            'student3@school.com',
            'student4@school.com',
            'student5@school.com',
        ];

        $studentData = [
            [
                'roll_number'      => 'ROLL-001',
                'admission_number' => 'ADM-2024-001',
                'school_class_id'  => $class10Id,
                'section_id'       => $section10A,
                'admission_date'   => '2024-01-15',
                'gender'           => 'female',
                'date_of_birth'    => '2008-05-12',
                'address'          => '10 Maple Drive',
                'parent_name'      => 'Robert Thompson',
                'parent_phone'     => '+1-555-3001',
                'parent_email'     => 'rthompson@email.com',
                'blood_group'      => 'A+',
                'status'           => 'active',
            ],
            [
                'roll_number'      => 'ROLL-002',
                'admission_number' => 'ADM-2024-002',
                'school_class_id'  => $class10Id,
                'section_id'       => $section10A,
                'admission_date'   => '2024-01-15',
                'gender'           => 'male',
                'date_of_birth'    => '2008-11-20',
                'address'          => '22 Pine Road',
                'parent_name'      => 'Maria Martinez',
                'parent_phone'     => '+1-555-3002',
                'parent_email'     => 'mmartinez@email.com',
                'blood_group'      => 'O+',
                'status'           => 'active',
            ],
            [
                'roll_number'      => 'ROLL-003',
                'admission_number' => 'ADM-2024-003',
                'school_class_id'  => $class10Id,
                'section_id'       => $section10B,
                'admission_date'   => '2024-01-15',
                'gender'           => 'female',
                'date_of_birth'    => '2008-03-08',
                'address'          => '55 Cedar Lane',
                'parent_name'      => 'James Davis',
                'parent_phone'     => '+1-555-3003',
                'parent_email'     => 'jdavis@email.com',
                'blood_group'      => 'B+',
                'status'           => 'active',
            ],
            [
                'roll_number'      => 'ROLL-004',
                'admission_number' => 'ADM-2024-004',
                'school_class_id'  => $class11Id,
                'section_id'       => $section11A,
                'admission_date'   => '2023-01-10',
                'gender'           => 'male',
                'date_of_birth'    => '2007-09-15',
                'address'          => '77 Birch Boulevard',
                'parent_name'      => 'Susan Lee',
                'parent_phone'     => '+1-555-3004',
                'parent_email'     => 'slee@email.com',
                'blood_group'      => 'AB+',
                'status'           => 'active',
            ],
            [
                'roll_number'      => 'ROLL-005',
                'admission_number' => 'ADM-2024-005',
                'school_class_id'  => $class11Id,
                'section_id'       => $section11A,
                'admission_date'   => '2023-01-10',
                'gender'           => 'female',
                'date_of_birth'    => '2007-12-25',
                'address'          => '99 Willow Way',
                'parent_name'      => 'Carlos Garcia',
                'parent_phone'     => '+1-555-3005',
                'parent_email'     => 'cgarcia@email.com',
                'blood_group'      => 'O-',
                'status'           => 'active',
            ],
        ];

        foreach ($studentEmails as $i => $email) {
            $userId = DB::table('users')->where('email', $email)->value('id');
            if ($userId) {
                Student::updateOrCreate(
                    ['user_id' => $userId],
                    $studentData[$i]
                );
            }
        }
    }
}
