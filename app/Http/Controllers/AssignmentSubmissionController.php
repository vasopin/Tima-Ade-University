<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Rubric;
use App\Models\RubricScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AssignmentSubmissionController extends Controller
{
    public function storeRubric(Request $request, Assignment $assignment)
    {
        $teacher = $this->teacher();
        abort_unless((int) $assignment->teacher_id === (int) $teacher->id, 403);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'criteria' => ['required', 'array', 'min:1', 'max:30'],
            'criteria.*.name' => ['required', 'string', 'max:255'],
            'criteria.*.description' => ['nullable', 'string', 'max:2000'],
            'criteria.*.max_points' => ['required', 'numeric', 'min:0.01', 'max:100000'],
        ]);
        $total = collect($validated['criteria'])->sum(fn (array $criterion): float => (float) $criterion['max_points']);
        abort_if($total > (float) $assignment->max_points, 422, 'Rubric points cannot exceed assignment points.');
        DB::transaction(function () use ($assignment, $teacher, $validated): void {
            $rubric = $assignment->rubric()->updateOrCreate(
                [],
                ['teacher_id' => $teacher->id, 'name' => $validated['name'], 'description' => $validated['description'] ?? null]
            );
            $rubric->criteria()->delete();
            foreach ($validated['criteria'] as $order => $criterion) {
                $rubric->criteria()->create([
                    'name' => $criterion['name'],
                    'description' => $criterion['description'] ?? null,
                    'max_points' => $criterion['max_points'],
                    'sort_order' => $order,
                ]);
            }
        });
        return back()->with('success', 'Rubric saved successfully.');
    }

    public function store(Request $request, Assignment $assignment)
    {
        $student = $this->student();
        abort_unless($assignment->status === 'published', 404);
        abort_unless((int) $student->school_class_id === (int) $assignment->school_class_id, 403);
        abort_unless($this->isEnrolledOrLegacyClass($student, $assignment), 403, 'You are not enrolled in this assignment course.');

        $validated = $request->validate([
            'submission_file' => ['required_if:status,submitted', 'nullable', 'file', 'max:20000', 'mimes:pdf,doc,docx,txt,zip'],
            'status' => ['nullable', 'in:draft,submitted'],
        ]);
        $now = now();
        abort_if($assignment->available_from && $now->lt($assignment->available_from), 422, 'This assignment is not available yet.');
        $status = $validated['status'] ?? 'submitted';
        $submission = DB::transaction(function () use ($assignment, $student, $request, $status, $now): AssignmentSubmission {
            $draft = $assignment->submissions()->where('student_id', $student->id)->where('status', 'draft')->lockForUpdate()->latest('attempt_number')->first();
            $attempt = $draft?->attempt_number ?: ((int) $assignment->submissions()->where('student_id', $student->id)->lockForUpdate()->max('attempt_number')) + 1;
            abort_if(!$draft && $attempt > $assignment->max_attempts, 422, 'The maximum number of attempts has been reached.');
            $file = $request->file('submission_file');
            $attributes = [
                'student_id' => $student->id,
                'attempt_number' => $attempt,
                'status' => $status,
                'submitted_at' => $status === 'submitted' ? $now : null,
                'is_late' => $assignment->due_date && $now->startOfDay()->gt($assignment->due_date->endOfDay()),
            ];
            if ($file) {
                $attributes += [
                    'file_path' => $file->store('uploads/assignment-submissions', 'private'),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ];
            }
            if ($draft) {
                $draft->update($attributes);
                return $draft->refresh();
            }
            return $assignment->submissions()->create($attributes);
        });

        return back()->with('success', $status === 'submitted' ? 'Assignment submitted successfully.' : 'Assignment saved as a draft.');
    }

    public function updateDraft(Request $request, AssignmentSubmission $submission)
    {
        $student = $this->student();
        abort_unless((int) $submission->student_id === (int) $student->id, 403);
        abort_unless($submission->status === 'draft', 422, 'Submitted attempts cannot be edited.');
        $assignment = $submission->assignment;
        abort_unless($assignment->status === 'published', 404);
        abort_unless((int) $student->school_class_id === (int) $assignment->school_class_id, 403);
        abort_unless($this->isEnrolledOrLegacyClass($student, $assignment), 403);
        $validated = $request->validate([
            'submission_file' => ['required_if:status,submitted', 'nullable', 'file', 'max:20000', 'mimes:pdf,doc,docx,txt,zip'],
            'status' => ['required', 'in:draft,submitted'],
        ]);
        abort_if($assignment->available_from && now()->lt($assignment->available_from), 422, 'This assignment is not available yet.');
        $attributes = ['status' => $validated['status'], 'submitted_at' => $validated['status'] === 'submitted' ? now() : null];
        if ($validated['status'] === 'submitted' && $assignment->due_date) {
            $attributes['is_late'] = now()->startOfDay()->gt($assignment->due_date->endOfDay());
        }
        if ($request->hasFile('submission_file')) {
            if ($submission->file_path) {
                Storage::disk('private')->delete($submission->file_path);
            }
            $file = $request->file('submission_file');
            $attributes += [
                'file_path' => $file->store('uploads/assignment-submissions', 'private'),
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ];
        }
        $submission->update($attributes);
        return back()->with('success', $validated['status'] === 'submitted' ? 'Draft submitted successfully.' : 'Draft updated successfully.');
    }

    public function index(Assignment $assignment)
    {
        $teacher = $this->teacher();
        abort_unless((int) $assignment->teacher_id === (int) $teacher->id, 403);
        $assignment->load('rubric.criteria');
        $submissions = $assignment->submissions()
            ->with(['student.user', 'rubricScores' => fn ($query) => $query->latest('graded_at')])
            ->latest('submitted_at')
            ->paginate(25);
        return view('teacher.assignment-submissions', compact('assignment', 'submissions'));
    }

    public function grade(Request $request, AssignmentSubmission $submission)
    {
        $teacher = $this->teacher();
        abort_unless((int) $submission->assignment->teacher_id === (int) $teacher->id, 403);
        $validated = $request->validate([
            'score' => ['nullable', 'numeric', 'min:0', 'max:' . $submission->assignment->max_points],
            'feedback' => ['nullable', 'string', 'max:5000'],
            'rubric_scores' => ['nullable', 'array'],
            'rubric_scores.*.criterion_id' => ['required_with:rubric_scores', 'integer'],
            'rubric_scores.*.awarded_points' => ['required_with:rubric_scores', 'numeric', 'min:0'],
            'rubric_scores.*.feedback' => ['nullable', 'string', 'max:2000'],
        ]);
        abort_if (! array_key_exists('score', $validated) && ! array_key_exists('rubric_scores', $validated), 422, 'A score or rubric scores are required.');
        DB::transaction(function () use ($submission, $validated, $teacher): void {
            $rubric = $submission->assignment->rubric()->with('criteria')->first();
            $scores = $validated['rubric_scores'] ?? null;
            $total = $validated['score'] ?? null;
            if ($scores !== null) {
                abort_unless($rubric && $rubric->teacher_id === $teacher->id, 422, 'A valid rubric is required for rubric grading.');
                abort_unless(count($scores) === $rubric->criteria->count(), 422, 'Every rubric criterion must be scored.');
                $criteria = $rubric->criteria->keyBy('id');
                $total = 0;
                $scoredCriteria = [];
                foreach ($scores as $score) {
                    $criterion = $criteria->get((int) $score['criterion_id']);
                    abort_unless($criterion, 422, 'Invalid rubric criterion.');
                    abort_if(isset($scoredCriteria[$criterion->id]), 422, 'Each rubric criterion can only be scored once.');
                    $scoredCriteria[$criterion->id] = true;
                    abort_if((float) $score['awarded_points'] > (float) $criterion->max_points, 422, 'A rubric score exceeds its criterion maximum.');
                    RubricScore::create([
                        'assignment_submission_id' => $submission->id,
                        'rubric_criterion_id' => $criterion->id,
                        'criterion_name' => $criterion->name,
                        'criterion_max_points' => $criterion->max_points,
                        'awarded_points' => $score['awarded_points'],
                        'feedback' => $score['feedback'] ?? null,
                        'graded_by' => auth()->id(),
                        'graded_at' => now(),
                    ]);
                    $total += (float) $score['awarded_points'];
                }
            }
            abort_if($total === null, 422, 'A score or rubric scores are required.');
            abort_if($total > (float) $submission->assignment->max_points, 422, 'The total score exceeds assignment points.');
            $submission->update(['score' => $total, 'feedback' => $validated['feedback'] ?? null, 'graded_by' => auth()->id(), 'graded_at' => now(), 'status' => 'graded']);
        });
        return back()->with('success', 'Assignment submission graded.');
    }

    public function file(AssignmentSubmission $submission)
    {
        $student = auth()->user()?->student;
        $teacher = auth()->user()?->isTeacher() ? Teacher::where('user_id', auth()->id())->first() : null;
        $allowed = auth()->user()?->isAdmin()
            || ($student && $student->id === $submission->student_id)
            || ($teacher && $teacher->id === $submission->assignment->teacher_id);
        abort_unless($allowed, 403);
        abort_unless($submission->file_path && Storage::disk('private')->exists($submission->file_path), 404);
        return Storage::disk('private')->download($submission->file_path, $submission->original_name ?: 'submission');
    }

    private function student(): Student
    {
        $student = auth()->user()?->student;
        abort_unless($student, 403, 'Student access required.');
        return $student;
    }

    private function teacher(): Teacher
    {
        $teacher = Teacher::where('user_id', auth()->id())->first();
        abort_unless($teacher, 403, 'Teacher access required.');
        return $teacher;
    }

    private function isEnrolledOrLegacyClass(Student $student, Assignment $assignment): bool
    {
        return Enrollment::where('student_id', $student->id)
            ->whereHas('courseSection', fn ($query) => $query->where('course_id', $assignment->subject_id))
            ->whereIn('status', Enrollment::ACTIVE_STATUSES)->exists()
            || (int) $student->school_class_id === (int) $assignment->school_class_id;
    }
}
