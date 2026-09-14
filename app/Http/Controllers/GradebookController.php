<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class GradebookController extends Controller
{
    public function teacher(Request $request)
    {
        $teacher = Teacher::where('user_id', auth()->id())->first();
        abort_unless($teacher, 403, 'Teacher access required.');

        $assignmentQuery = Assignment::query()
            ->where('teacher_id', $teacher->id)
            ->with(['subject', 'schoolClass', 'rubric.criteria'])
            ->latest();

        if ($request->filled('assignment')) {
            $assignmentQuery->whereKey($request->integer('assignment'));
        }

        $assignments = $assignmentQuery->limit(50)->get();
        $classIds = $assignments->pluck('school_class_id')->filter()->unique()->values();
        $students = Student::query()
            ->whereIn('school_class_id', $classIds)
            ->where('status', 'active')
            ->with(['user', 'schoolClass'])
            ->when($request->filled('student'), function ($query) use ($request): void {
                $term = '%' . trim((string) $request->input('student')) . '%';
                $query->where(function ($studentQuery) use ($term): void {
                    $studentQuery->where('admission_number', 'like', $term)
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', $term));
                });
            })
            ->orderBy('id')
            ->get();

        $submissions = AssignmentSubmission::query()
            ->whereIn('assignment_id', $assignments->modelKeys())
            ->whereIn('student_id', $students->modelKeys())
            ->with(['rubricScores', 'grader'])
            ->get()
            ->groupBy(fn (AssignmentSubmission $submission): string => $submission->student_id . ':' . $submission->assignment_id);

        $rows = $students->map(function (Student $student) use ($assignments, $submissions): array {
            $cells = $assignments->map(function (Assignment $assignment) use ($student, $submissions): array {
                $attempts = $submissions->get($student->id . ':' . $assignment->id, collect());
                $submission = $this->authoritativeSubmission($attempts);
                return [
                    'assignment' => $assignment,
                    'submission' => $submission,
                    'percentage' => $submission?->score !== null && (float) $assignment->max_points > 0
                        ? round(((float) $submission->score / (float) $assignment->max_points) * 100, 1)
                        : null,
                ];
            });

            return ['student' => $student, 'cells' => $cells];
        });

        if ($request->input('state')) {
            $state = (string) $request->input('state');
            $rows = $rows->filter(fn (array $row): bool => $this->rowMatchesState($row['cells'], $state))->values();
        }

        $summary = [
            'students' => $rows->count(),
            'assignments' => $assignments->count(),
            'graded' => $rows->flatMap(fn (array $row) => $row['cells'])->filter(fn (array $cell): bool => $cell['submission']?->status === 'graded')->count(),
            'missing' => $rows->flatMap(fn (array $row) => $row['cells'])->filter(fn (array $cell): bool => $cell['submission'] === null)->count(),
        ];

        return view('teacher.gradebook', compact('assignments', 'rows', 'summary'));
    }

    public function student()
    {
        $student = auth()->user()?->student;
        abort_unless($student, 403, 'Student access required.');

        $assignments = Assignment::query()
            ->where('school_class_id', $student->school_class_id)
            ->where('status', 'published')
            ->where(function ($query): void {
                $query->whereNull('available_from')->orWhere('available_from', '<=', now());
            })
            ->with(['subject', 'teacher.user', 'rubric.criteria'])
            ->latest()
            ->get();

        $submissions = AssignmentSubmission::query()
            ->where('student_id', $student->id)
            ->whereIn('assignment_id', $assignments->modelKeys())
            ->with(['rubricScores', 'grader'])
            ->get()
            ->groupBy('assignment_id');

        $rows = $assignments->map(function (Assignment $assignment) use ($submissions): array {
            $submission = $this->authoritativeSubmission($submissions->get($assignment->id, collect()));
            return [
                'assignment' => $assignment,
                'submission' => $submission,
                'percentage' => $submission?->score !== null && (float) $assignment->max_points > 0
                    ? round(((float) $submission->score / (float) $assignment->max_points) * 100, 1)
                    : null,
            ];
        });

        return view('student.gradebook', compact('student', 'rows'));
    }

    private function authoritativeSubmission(Collection $attempts): ?AssignmentSubmission
    {
        return $attempts->where('status', 'graded')->sortByDesc(fn (AssignmentSubmission $submission) => $submission->graded_at?->timestamp ?? 0)->first()
            ?? $attempts->where('status', 'submitted')->sortByDesc('attempt_number')->first()
            ?? $attempts->where('status', 'draft')->sortByDesc('attempt_number')->first();
    }

    private function rowMatchesState(Collection $cells, string $state): bool
    {
        return $cells->contains(function (array $cell) use ($state): bool {
            $submission = $cell['submission'];
            return match ($state) {
                'graded' => $submission?->status === 'graded',
                'ungraded' => $submission?->status === 'submitted',
                'missing' => $submission === null,
                'draft' => $submission?->status === 'draft',
                'late' => (bool) $submission?->is_late,
                'rubric' => $cell['assignment']->rubric !== null,
                default => true,
            };
        });
    }
}
