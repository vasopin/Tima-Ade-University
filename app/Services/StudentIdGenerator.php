<?php

namespace App\Services;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StudentIdGenerator
{
    public function generate(?CarbonInterface $admissionDate = null): string
    {
        $year = ($admissionDate ?? now())->year;

        DB::table('student_id_sequences')->insertOrIgnore([
            'year' => $year,
            'next_number' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $sequence = DB::table('student_id_sequences')
            ->where('year', $year)
            ->lockForUpdate()
            ->first();

        if (!$sequence) {
            throw new RuntimeException('Unable to reserve a Student ID sequence.');
        }

        $nextNumber = $sequence->next_number + 1;
        if ($nextNumber > 999999) {
            throw new RuntimeException("Student ID sequence exhausted for {$year}.");
        }

        DB::table('student_id_sequences')
            ->where('year', $year)
            ->update(['next_number' => $nextNumber, 'updated_at' => now()]);

        return sprintf('TAU-%d-%06d', $year, $nextNumber);
    }
}
