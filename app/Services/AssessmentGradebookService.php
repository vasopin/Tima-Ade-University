<?php

namespace App\Services;

use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\ExamMarkHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssessmentGradebookService
{
    public function saveMarks(Exam $exam, array $marks, array $absent, int $actorId, bool $administrator = false): void
    {
        DB::transaction(function () use ($exam, $marks, $absent, $actorId, $administrator): void {
            $exam->loadMissing('courseSection');
            $studentIds = $exam->course_section_id
                ? Enrollment::where('course_section_id', $exam->course_section_id)->whereIn('status', Enrollment::ACTIVE_STATUSES)->pluck('student_id')
                : \App\Models\Student::where('school_class_id', $exam->school_class_id)->pluck('id');

            foreach ($marks as $studentId => $value) {
                abort_unless($studentIds->contains((int) $studentId), 403, 'The student is not eligible for this assessment.');
                $isAbsent = array_key_exists($studentId, $absent);
                $score = $isAbsent ? 0 : (float) $value;
                if ($score < 0 || $score > (float) $exam->total_marks) {
                    throw ValidationException::withMessages(["marks.$studentId" => 'Score must be between zero and the maximum marks.']);
                }
                $existing = ExamMark::where(['exam_id' => $exam->id, 'student_id' => $studentId])->first();
                if ($existing && $exam->resultsApproval?->status === 'approved' && !$administrator) {
                    throw ValidationException::withMessages(['marks' => 'Approved results require an authorized correction workflow.']);
                }
                if ($existing && ((float) $existing->marks_obtained !== $score || (bool) $existing->is_absent !== $isAbsent)) {
                    ExamMarkHistory::create([
                        'exam_mark_id' => $existing->id,
                        'marks_obtained' => $existing->marks_obtained,
                        'grade' => $existing->grade,
                        'is_absent' => $existing->is_absent,
                        'reason' => 'Authorized grade correction',
                        'changed_by' => $actorId,
                    ]);
                }
                ExamMark::updateOrCreate(
                    ['exam_id' => $exam->id, 'student_id' => $studentId],
                    [
                        'marks_obtained' => $score,
                        'grade' => $isAbsent ? 'F' : ExamMark::computeGrade($score, $exam->total_marks),
                        'is_absent' => $isAbsent,
                        'recorded_by' => $actorId,
                        'change_reason' => $existing ? 'Authorized grade correction' : null,
                    ]
                );
            }
        });
    }

    public function teacherMayAccess(Exam $exam, $user): bool
    {
        if ($user->isAdmin() || $user->isRegistrar()) {
            return true;
        }
        if (!$user->isTeacher()) {
            return false;
        }
        if ($exam->course_section_id) {
            return (int) $exam->courseSection?->teacher_id === (int) $user->id;
        }
        return DB::table('class_subject')->where('teacher_id', $user->id)
            ->where('school_class_id', $exam->school_class_id)->where('subject_id', $exam->subject_id)->exists();
    }
}
