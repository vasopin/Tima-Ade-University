<?php

namespace Database\Factories;

use App\Models\AdmissionApplication;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AdmissionApplicationFactory extends Factory
{
    protected $model = AdmissionApplication::class;

    public function definition(): array
    {
        $program = Program::query()->where('is_active', true)->inRandomOrder()->first();

        return [
            'reference' => 'TA-' . now()->format('Ym') . '-' . strtoupper(Str::random(6)),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'program_id' => $program?->id,
            'grade_interested' => $program?->name ?? $this->faker->randomElement(['B.Sc. Computer Science', 'B.A. English', 'B.Eng. Civil Engineering']),
            'message' => $this->faker->sentence(),
            'education' => [
                'previous_institution' => $this->faker->company(),
                'qualification' => 'WAEC',
            ],
            'documents' => [],
            'status' => $this->faker->randomElement(['submitted', 'under_review', 'approved', 'declined']),
            'decided_by' => null,
            'decided_at' => null,
            'decision_reason' => null,
        ];
    }
}
