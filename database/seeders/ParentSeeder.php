<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guardian;
use App\Models\User;
use App\Models\Student;

class ParentSeeder extends Seeder
{
    public function run(): void
    {
        $parentUser = User::where('email', 'parent1@school.com')->first();
        if ($parentUser) {
            $guardian = Guardian::firstOrCreate(
                ['user_id' => $parentUser->id],
                [
                    'occupation'        => 'Senior Architect',
                    'relationship'      => 'father',
                    'national_id'       => 'NAT-998811',
                    'emergency_contact' => '+1-555-0301',
                ]
            );

            // Link first 2 students to this parent
            $studentIds = Student::take(2)->pluck('id');
            $guardian->students()->sync($studentIds);
        }
    }
}
