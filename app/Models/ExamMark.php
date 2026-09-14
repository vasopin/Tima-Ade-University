<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamMark extends Model
{
    protected $fillable = [
        'exam_id', 'student_id', 'marks_obtained',
        'grade', 'is_absent', 'remarks', 'recorded_by', 'change_reason', 'submitted_at',
    ];

    protected $casts = [
        'is_absent'      => 'boolean',
        'marks_obtained' => 'decimal:2',
        'submitted_at' => 'datetime',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function recordedBy(): BelongsTo { return $this->belongsTo(User::class, 'recorded_by'); }
    public function history(): \Illuminate\Database\Eloquent\Relations\HasMany { return $this->hasMany(ExamMarkHistory::class, 'exam_mark_id'); }

    /**
     * Automatically compute letter grade from marks.
     */
    public static function computeGrade($obtained, $total): string
    {
        // Defensive handling: accept numeric-like values, coerce to float when possible
        if (!is_numeric($obtained) || !is_numeric($total)) {
            // Non-numeric inputs are treated as zero (fail-safe)
            $obtained = 0.0;
            $total = floatval($total) ?: 0.0;
        }

        $obtained = max(0.0, floatval($obtained));
        $total = floatval($total);

        // If total is zero or negative, return 'F' as default fail-safe (consistent with existing callers that guard against zero)
        if ($total <= 0.0) {
            return 'F';
        }

        // If obtained exceeds total (data anomaly), clamp to total
        if ($obtained > $total) {
            $obtained = $total;
        }

        $pct = ($obtained / $total) * 100.0;

        return match (true) {
            $pct >= 90.0 => 'A+',
            $pct >= 80.0 => 'A',
            $pct >= 70.0 => 'B+',
            $pct >= 60.0 => 'B',
            $pct >= 50.0 => 'C+',
            $pct >= 40.0 => 'C',
            $pct >= 33.0 => 'D',
            default      => 'F',
        };
    }
}
