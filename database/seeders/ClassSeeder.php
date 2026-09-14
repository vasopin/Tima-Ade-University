<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassSeeder extends Seeder
{
    public function run(): void
    {
        $classes = [
            ['name' => 'Grade 9',  'grade_level' => '9',  'description' => 'Ninth Grade',  'is_active' => 1],
            ['name' => 'Grade 10', 'grade_level' => '10', 'description' => 'Tenth Grade',  'is_active' => 1],
            ['name' => 'Grade 11', 'grade_level' => '11', 'description' => 'Eleventh Grade', 'is_active' => 1],
            ['name' => 'Grade 12', 'grade_level' => '12', 'description' => 'Twelfth Grade', 'is_active' => 1],
        ];

        foreach ($classes as $class) {
            $classId = DB::table('school_classes')->insertGetId(array_merge($class, [
                'created_at' => now(), 'updated_at' => now(),
            ]));

            // Create two sections per class
            foreach (['A', 'B'] as $sectionName) {
                DB::table('sections')->insert([
                    'school_class_id' => $classId,
                    'name'            => 'Section ' . $sectionName,
                    'capacity'        => 40,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        }
    }
}
