<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicStanding extends Model
{
    protected $table = "academic_standing";
    protected $fillable = ["student_id", "status", "cumulative_gpa", "failed_courses", "notes", "updated_by"];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "updated_by");
    }

    /**
     * Compute cumulative GPA, failed-course count, and standing status
     * from the student's real ExamMark history (no fabricated figures).
     */
    public static function computeForStudent(int $studentId): array
    {
        $marks = ExamMark::where('student_id', $studentId)
            ->whereHas('exam.resultsApproval', fn ($query) => $query->whereIn('status', ['approved', 'published']))
            ->with('exam')->get();

        if ($marks->isEmpty()) {
            return [
                'cumulative_gpa' => 0.0,
                'failed_courses' => 0,
                'status' => 'good',
                'honors' => null,
                'assessed_count' => 0,
            ];
        }

        $percentages = $marks->map(function ($mark) {
            $total = (float) ($mark->exam->total_marks ?? 0);
            if ($total <= 0) {
                return 0.0;
            }
            return (min((float) $mark->marks_obtained, $total) / $total) * 100;
        });

        $avgPercentage = $percentages->avg();
        $gpa = round(min(4.0, max(0.0, $avgPercentage / 25)), 2);
        $failedCourses = $marks->where('grade', 'F')->count();

        $status = match (true) {
            $gpa < 1.0 => 'dismissed',
            $gpa < 1.5 => 'suspended',
            $gpa < 2.0 || $failedCourses > 0 => 'probation',
            default => 'good',
        };

        return [
            'cumulative_gpa' => $gpa,
            'failed_courses' => $failedCourses,
            'status' => $status,
            'honors' => $gpa >= 3.5 && $failedCourses === 0 ? 'deans_list' : null,
            'assessed_count' => $marks->count(),
        ];
    }
}
