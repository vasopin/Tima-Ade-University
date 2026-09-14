<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Timetable;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Teacher;

class TimetableSeeder extends Seeder
{
    public function run(): void
    {
        $classes  = SchoolClass::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();
        $teachers = Teacher::all();

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $timeSlots = [
            ['start' => '08:30', 'end' => '09:30'],
            ['start' => '09:35', 'end' => '10:35'],
            ['start' => '11:00', 'end' => '12:00'],
            ['start' => '12:05', 'end' => '13:05'],
            ['start' => '14:00', 'end' => '15:00'],
        ];

        foreach ($classes as $class) {
            $section = Section::where('school_class_id', $class->id)->first();
            $subIndex = 0;

            foreach ($days as $day) {
                foreach ($timeSlots as $slotIndex => $slot) {
                    $subject = $subjects[$subIndex % count($subjects)] ?? $subjects->first();
                    $teacher = $teachers[$subIndex % max(1, count($teachers))] ?? null;

                    Timetable::updateOrCreate(
                        [
                            'school_class_id' => $class->id,
                            'day_of_week'     => $day,
                            'start_time'      => $slot['start'],
                        ],
                        [
                            'section_id'  => $section?->id,
                            'subject_id'  => $subject->id,
                            'teacher_id'  => $teacher?->id,
                            'end_time'    => $slot['end'],
                            'room_number' => 'Hall ' . ($slotIndex + 1),
                            'is_active'   => true,
                        ]
                    );

                    $subIndex++;
                }
            }
        }
    }
}
