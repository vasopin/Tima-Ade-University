<?php

namespace Database\Seeders;

use App\Models\Campus;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Program;
use Illuminate\Database\Seeder;

class OfficialProgramCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $campus = Campus::query()->firstOrCreate(
            ['code' => 'TIMA_ADE_CORE'],
            ['name' => 'Main Campus', 'description' => 'Official university campus for academic programs.', 'is_active' => true]
        );

        $catalog = [
            'Faculty of Business and Management' => [
                'code' => 'BAM',
                'departments' => [
                    'Department of Accounting and Finance' => [
                        ['name' => 'Accounting', 'code' => 'ACC', 'degree_type' => 'Bachelor'],
                        ['name' => 'Finance', 'code' => 'FIN', 'degree_type' => 'Bachelor'],
                    ],
                    'Department of Business and Management' => [
                        ['name' => 'Business Administration', 'code' => 'BA', 'degree_type' => 'Bachelor'],
                        ['name' => 'Economics', 'code' => 'ECON', 'degree_type' => 'Bachelor'],
                        ['name' => 'Management', 'code' => 'MGT', 'degree_type' => 'Bachelor'],
                        ['name' => 'Marketing', 'code' => 'MKT', 'degree_type' => 'Bachelor'],
                        ['name' => 'Human Resources', 'code' => 'HRM', 'degree_type' => 'Bachelor'],
                        ['name' => 'International Business', 'code' => 'IB', 'degree_type' => 'Bachelor'],
                        ['name' => 'Logistics and Supply Chain Management', 'code' => 'LSCM', 'degree_type' => 'Bachelor'],
                    ],
                ],
            ],
            'Faculty of Computing and Digital Sciences' => [
                'code' => 'CDS',
                'departments' => [
                    'Department of Computing' => [
                        ['name' => 'Computer Science', 'code' => 'CS', 'degree_type' => 'Bachelor'],
                        ['name' => 'Information Technology', 'code' => 'IT', 'degree_type' => 'Bachelor'],
                        ['name' => 'Software Engineering', 'code' => 'SE', 'degree_type' => 'Bachelor'],
                        ['name' => 'Cybersecurity', 'code' => 'CYB', 'degree_type' => 'Bachelor'],
                        ['name' => 'Data Science', 'code' => 'DS', 'degree_type' => 'Bachelor'],
                        ['name' => 'Artificial Intelligence', 'code' => 'AI', 'degree_type' => 'Bachelor'],
                    ],
                ],
            ],
            'Faculty of Engineering' => [
                'code' => 'ENG',
                'departments' => [
                    'Department of Engineering' => [
                        ['name' => 'Civil Engineering', 'code' => 'CE', 'degree_type' => 'Bachelor'],
                        ['name' => 'Electrical Engineering', 'code' => 'EE', 'degree_type' => 'Bachelor'],
                        ['name' => 'Mechanical Engineering', 'code' => 'ME', 'degree_type' => 'Bachelor'],
                        ['name' => 'Architecture', 'code' => 'ARC', 'degree_type' => 'Bachelor'],
                    ],
                ],
            ],
            'Faculty of Health Sciences' => [
                'code' => 'HLS',
                'departments' => [
                    'Department of Clinical Health Sciences' => [
                        ['name' => 'Medicine', 'code' => 'MED', 'degree_type' => 'Doctor of Medicine'],
                        ['name' => 'Nursing', 'code' => 'NUR', 'degree_type' => 'Bachelor'],
                        ['name' => 'Midwifery', 'code' => 'MID', 'degree_type' => 'Bachelor'],
                        ['name' => 'Public Health', 'code' => 'PH', 'degree_type' => 'Bachelor'],
                        ['name' => 'Pharmacy', 'code' => 'PHA', 'degree_type' => 'Bachelor'],
                        ['name' => 'Dentistry', 'code' => 'DEN', 'degree_type' => 'Doctor of Dental Surgery'],
                    ],
                    'Department of Biomedical and Applied Sciences' => [
                        ['name' => 'Medical Laboratory Science', 'code' => 'MLS', 'degree_type' => 'Bachelor'],
                        ['name' => 'Nutrition and Dietetics', 'code' => 'NUT', 'degree_type' => 'Bachelor'],
                    ],
                ],
            ],
            'Faculty of Science' => [
                'code' => 'SCI',
                'departments' => [
                    'Department of Life and Physical Sciences' => [
                        ['name' => 'Biology', 'code' => 'BIO', 'degree_type' => 'Bachelor'],
                        ['name' => 'Chemistry', 'code' => 'CHM', 'degree_type' => 'Bachelor'],
                        ['name' => 'Physics', 'code' => 'PHY', 'degree_type' => 'Bachelor'],
                        ['name' => 'Mathematics', 'code' => 'MTH', 'degree_type' => 'Bachelor'],
                        ['name' => 'Agriculture', 'code' => 'AGR', 'degree_type' => 'Bachelor'],
                        ['name' => 'Veterinary Medicine', 'code' => 'VET', 'degree_type' => 'Doctor of Veterinary Medicine'],
                        ['name' => 'Environmental Science', 'code' => 'ENV', 'degree_type' => 'Bachelor'],
                        ['name' => 'Geology', 'code' => 'GEOL', 'degree_type' => 'Bachelor'],
                        ['name' => 'Statistics', 'code' => 'STAT', 'degree_type' => 'Bachelor'],
                        ['name' => 'Marine Science', 'code' => 'MAR', 'degree_type' => 'Bachelor'],
                    ],
                ],
            ],
            'Faculty of Law and Humanities' => [
                'code' => 'LHS',
                'departments' => [
                    'Department of Law and Governance' => [
                        ['name' => 'Law', 'code' => 'LAW', 'degree_type' => 'Bachelor'],
                        ['name' => 'Political Science', 'code' => 'POL', 'degree_type' => 'Bachelor'],
                        ['name' => 'International Relations', 'code' => 'IR', 'degree_type' => 'Bachelor'],
                        ['name' => 'Public Administration', 'code' => 'PA', 'degree_type' => 'Bachelor'],
                    ],
                    'Department of Humanities and Social Sciences' => [
                        ['name' => 'Islamic Studies', 'code' => 'IS', 'degree_type' => 'Bachelor'],
                        ['name' => 'Journalism', 'code' => 'JRN', 'degree_type' => 'Bachelor'],
                        ['name' => 'Communication', 'code' => 'COM', 'degree_type' => 'Bachelor'],
                        ['name' => 'Sociology', 'code' => 'SOC', 'degree_type' => 'Bachelor'],
                        ['name' => 'Psychology', 'code' => 'PSY', 'degree_type' => 'Bachelor'],
                        ['name' => 'Social Work', 'code' => 'SW', 'degree_type' => 'Bachelor'],
                        ['name' => 'Education', 'code' => 'EDU', 'degree_type' => 'Bachelor'],
                        ['name' => 'English', 'code' => 'ENG-LIT', 'degree_type' => 'Bachelor'],
                        ['name' => 'History', 'code' => 'HIS', 'degree_type' => 'Bachelor'],
                        ['name' => 'Geography', 'code' => 'GEOG', 'degree_type' => 'Bachelor'],
                    ],
                ],
            ],
        ];

        foreach ($catalog as $facultyName => $facultyConfig) {
            $faculty = Faculty::query()->firstOrCreate(
                ['code' => $facultyConfig['code']],
                ['campus_id' => $campus->id, 'name' => $facultyName, 'description' => 'Official faculty catalog entry.', 'is_active' => true]
            );

            if (empty($faculty->campus_id)) {
                $faculty->campus_id = $campus->id;
                $faculty->save();
            }

            foreach ($facultyConfig['departments'] as $departmentName => $programs) {
                $department = Department::query()->firstOrCreate(
                    ['faculty_id' => $faculty->id, 'code' => $this->departmentCodeFor($departmentName)],
                    ['faculty_id' => $faculty->id, 'name' => $departmentName, 'description' => 'Official department catalog entry.', 'is_active' => true]
                );

                foreach ($programs as $program) {
                    $this->ensureProgram($department->id, $program['name'], $program['code'], $program['degree_type']);
                }
            }
        }
    }

    private function ensureProgram(int $departmentId, string $name, string $code, string $degreeType): void
    {
        $normalizedName = trim(preg_replace('/\s+/', ' ', $name) ?? $name);
        $normalizedCode = strtoupper(trim($code));

        $program = Program::query()
            ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($normalizedName)])
            ->first();

        if (! $program) {
            $program = Program::query()
                ->whereRaw('LOWER(TRIM(code)) = ?', [mb_strtolower($normalizedCode)])
                ->first();
        }

        $payload = [
            'department_id' => $departmentId,
            'name' => $normalizedName,
            'code' => $normalizedCode,
            'degree_type' => $degreeType,
            'description' => 'Official program catalog entry.',
            'is_active' => true,
        ];

        if ($program) {
            $program->forceFill($payload)->save();
            return;
        }

        Program::query()->create($payload);
    }

    private function departmentCodeFor(string $name): string
    {
        return strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 8) ?: 'DEPT');
    }
}
