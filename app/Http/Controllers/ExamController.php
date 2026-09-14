<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\AssessmentGradebookService;

class ExamController extends Controller
{
    public function __construct(private AssessmentGradebookService $gradebook)
    {
        // Admins manage exams; assigned teachers may create and grade assessments.
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || !auth()->user()->isAdmin()) {
                abort(403, 'Unauthorized. Administrative privileges required.');
            }
            return $next($request);
        })->only(['edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $query = Exam::with(['schoolClass', 'subject']);

        if ($request->filled('class_id')) {
            $query->where('school_class_id', $request->class_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $exams   = $query->latest('exam_date')->paginate(15)->withQueryString();
        $classes = SchoolClass::where('is_active', true)->get();

        return view('exams.index', compact('exams', 'classes'));
    }

    public function create()
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isTeacher()), 403, 'Assessment access required.');
        [$classes, $subjects] = $this->assessmentOptions($user);
        return view('exams.create', compact('classes', 'subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'exam_type'        => 'required|in:midterm,final,unit_test,quiz,assignment',
            'school_class_id'  => 'required|exists:school_classes,id',
            'subject_id'       => 'required|exists:subjects,id',
            'course_section_id' => 'nullable|exists:course_sections,id',
            'academic_year_id'  => 'nullable|exists:academic_years,id',
            'term_id'          => 'nullable|exists:terms,id',
            'exam_date'        => 'required|date',
            'start_time'       => 'nullable',
            'duration_minutes' => 'nullable|integer|min:1',
            'total_marks'      => 'required|integer|min:1',
            'pass_marks'       => 'required|integer|min:0',
            'status'           => 'required|in:scheduled,ongoing,completed,cancelled',
            'instructions'     => 'nullable|string',
        ]);

        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isTeacher()), 403, 'Assessment access required.');
        if (!empty($validated['course_section_id'])) {
            $section = \App\Models\CourseSection::findOrFail($validated['course_section_id']);
            abort_unless($user->isAdmin() || (int) $section->teacher_id === (int) $user->id, 403, 'You are not assigned to this course section.');
        }
        if ($user->isTeacher() && (!empty($validated['course_section_id'])
            ? (int) \App\Models\CourseSection::findOrFail($validated['course_section_id'])->teacher_id !== (int) $user->id
            : !$this->teacherMayAssess($user, (int) $validated['school_class_id'], (int) $validated['subject_id']))) {
            abort(403, 'You are not authorized to create an assessment for this class and subject.');
        }

        Exam::create($validated);

        return redirect()->route('exams.index')
            ->with('success', 'Exam scheduled successfully!');
    }

    public function show(Exam $exam)
    {
        $exam->load(['schoolClass', 'subject', 'marks.student.user']);

        $students = Student::where('school_class_id', $exam->school_class_id)
            ->with('user')->get();

        // Stats
        $markedCount = $exam->marks->count();
        $passCount   = $exam->marks->where('marks_obtained', '>=', $exam->pass_marks)->count();
        $avgMarks    = $markedCount > 0 ? round($exam->marks->avg('marks_obtained'), 1) : 0;

        return view('exams.show', compact('exam', 'students', 'markedCount', 'passCount', 'avgMarks'));
    }

    public function edit(Exam $exam)
    {
        $classes  = SchoolClass::where('is_active', true)->get();
        $subjects = Subject::orderBy('name')->get();
        return view('exams.edit', compact('exam', 'classes', 'subjects'));
    }

    public function update(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'exam_type'        => 'required|in:midterm,final,unit_test,quiz,assignment',
            'school_class_id'  => 'required|exists:school_classes,id',
            'subject_id'       => 'required|exists:subjects,id',
            'exam_date'        => 'required|date',
            'start_time'       => 'nullable',
            'duration_minutes' => 'nullable|integer|min:1',
            'total_marks'      => 'required|integer|min:1',
            'pass_marks'       => 'required|integer|min:0',
            'status'           => 'required|in:scheduled,ongoing,completed,cancelled',
            'instructions'     => 'nullable|string',
        ]);

        $exam->update($validated);

        return redirect()->route('exams.index')
            ->with('success', 'Exam updated successfully!');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();
        return redirect()->route('exams.index')
            ->with('success', 'Exam deleted successfully!');
    }

    /**
     * Show form to enter/edit marks for an exam.
     */
    public function marks(Exam $exam)
    {
        $exam->load(['schoolClass', 'subject']);
        $students = $exam->course_section_id
            ? Student::whereIn('id', \App\Models\Enrollment::where('course_section_id', $exam->course_section_id)->whereIn('status', \App\Models\Enrollment::ACTIVE_STATUSES)->pluck('student_id'))->with('user')->get()
            : Student::where('school_class_id', $exam->school_class_id)->with(['user'])->get();
        $existingMarks = ExamMark::where('exam_id', $exam->id)
            ->pluck('marks_obtained', 'student_id');
        $absentStudents = ExamMark::where('exam_id', $exam->id)
            ->where('is_absent', true)
            ->pluck('is_absent', 'student_id');

        return view('exams.marks', compact('exam', 'students', 'existingMarks', 'absentStudents'));
    }

    /**
     * Save marks for all students in an exam.
     */
    public function saveMarks(Request $request, Exam $exam)
    {
        // Only teachers or admins may save marks
        if (!auth()->check() || (!auth()->user()->isTeacher() && !auth()->user()->isAdmin())) {
            abort(403, 'Unauthorized');
        }

        $this->authorizeTeacherExamAccess($exam);

        $request->validate([
            'marks'         => 'required|array',
            'marks.*'       => 'nullable|numeric|min:0',
            'absent'        => 'nullable|array',
        ]);

        $this->gradebook->saveMarks($exam, $request->input('marks', []), $request->input('absent', []), auth()->id(), auth()->user()->isAdmin() || auth()->user()->isRegistrar());

        // Mark exam as completed
        $exam->update(['status' => 'completed', 'workflow_status' => 'draft']);

        return redirect()->route('exams.show', $exam)
            ->with('success', 'Marks saved successfully! Exam marked as completed.');
    }

    public function submitResults(Exam $exam)
    {
        $user = auth()->user();
        abort_unless($this->gradebook->teacherMayAccess($exam, $user), 403, 'You are not authorized to submit these results.');
        abort_if($exam->marks()->count() === 0, 422, 'Record marks before submitting results.');
        $exam->update(['workflow_status' => 'submitted', 'submitted_by' => $user->id, 'submitted_at' => now()]);
        return back()->with('success', 'Results submitted for academic approval.');
    }

    private function assessmentOptions($user): array
    {
        if ($user->isAdmin()) {
            return [SchoolClass::where('is_active', true)->get(), Subject::orderBy('name')->get()];
        }
        $pairs = $this->teacherAssessmentPairs($user);
        return [
            SchoolClass::where('is_active', true)->whereIn('id', $pairs->pluck('school_class_id'))->get(),
            Subject::orderBy('name')->whereIn('id', $pairs->pluck('subject_id'))->get(),
        ];
    }

    private function teacherAssessmentPairs($user)
    {
        $pairs = DB::table('class_subject')->where('teacher_id', $user->id)->select('school_class_id', 'subject_id')->get();
        $teacherClass = $user->teacher?->classTeacherOf;
        if ($teacherClass) {
            $pairs = $pairs->concat($teacherClass->subjects->map(fn ($subject) => (object) ['school_class_id' => $teacherClass->id, 'subject_id' => $subject->id]));
        }
        return $pairs->unique(fn ($pair) => $pair->school_class_id . ':' . $pair->subject_id)->values();
    }

    private function teacherMayAssess($user, int $classId, int $subjectId): bool
    {
        return $this->teacherAssessmentPairs($user)->contains(fn ($pair) => (int) $pair->school_class_id === $classId && (int) $pair->subject_id === $subjectId);
    }

    private function authorizeTeacherExamAccess(Exam $exam): void
    {
        $user = auth()->user();
        if (!$this->gradebook->teacherMayAccess($exam->loadMissing('courseSection'), $user)) {
            abort(403, 'You are not authorized to access this assessment.');
        }
    }
    /**
     * Show results matrix for a student. Protected against IDOR.
     */
    public function results(Request $request)
    {
        $user = auth()->user();
        $this->authorizeStudentResultsAccess($user);

        // Enforce IDOR protection: Students only see their own marks
        if ($user->isStudent()) {
            $studentId = $user->student?->id;
        } elseif ($user->isParent()) {
            $guardian = $user->guardian;
            $allowedIds = $guardian ? $guardian->students->pluck('id')->toArray() : [];
            $studentId = $request->student_id ?: ($allowedIds[0] ?? null);

            if ($studentId && !in_array($studentId, $allowedIds)) {
                abort(403, 'Unauthorized access to student record.');
            }
        } else {
            $studentId = $request->student_id;
        }

        $classes = SchoolClass::with('students.user')->get();
        $results = [];
        $isRestrictedViewer = $user->isStudent() || $user->isParent();

        if ($studentId) {
            $marksQuery = ExamMark::where('student_id', $studentId)
                ->with(['exam.subject', 'exam.schoolClass', 'exam.resultsApproval']);

            if ($isRestrictedViewer) {
                $marksQuery->whereHas('exam.resultsApproval', fn ($q) => $q->where('status', 'approved'));
            }

            $results = $marksQuery->get()->groupBy('exam.subject.name');
        }

        $students = ($user->isStudent() || $user->isParent())
            ? ($user->isStudent() ? collect([$user->student]) : ($user->guardian?->students ?? collect()))
            : Student::with('user')->whereHas('user')->get();

        return view('exams.results', compact('classes', 'results', 'studentId', 'students'));
    }

    /**
     * Generate print-ready official report card.
     */
    public function reportCard(Exam $exam, Student $student)
    {
        $user = auth()->user();
        $this->authorizeStudentResultsAccess($user);

        // IDOR protection
        if ($user->isStudent() && $user->student?->id !== $student->id) {
            abort(403, 'Unauthorized.');
        }

        if ($user->isParent()) {
            $guardian = $user->guardian;
            $allowedIds = $guardian ? $guardian->students->pluck('id')->toArray() : [];
            if (!in_array($student->id, $allowedIds)) {
                abort(403, 'Unauthorized.');
            }
        }

        if (($user->isStudent() || $user->isParent()) && $exam->resultsApproval?->status !== 'approved') {
            abort(403, 'These results have not yet been approved for release.');
        }

        $student->load(['user', 'schoolClass.subjects', 'section']);
        $exam->load(['schoolClass', 'subject']);

        // Marks for this student in this exam or all exams in same term/class
        $marks = ExamMark::where('student_id', $student->id)
            ->whereHas('exam', fn($q) => $q->where('school_class_id', $student->school_class_id))
            ->with(['exam.subject'])
            ->get();

        $totalMarksObtained = $marks->where('is_absent', false)->sum('marks_obtained');
        $totalMaxMarks      = $marks->sum(fn($m) => $m->exam->total_marks ?? 100);
        $overallPercentage  = $totalMaxMarks > 0 ? round(($totalMarksObtained / $totalMaxMarks) * 100, 1) : 0;
        $overallGrade       = ExamMark::computeGrade($totalMarksObtained, $totalMaxMarks ?: 1);

        // Attendance stats
        $totalAttendance = Attendance::where('student_id', $student->id)->count();
        $presentDays     = Attendance::where('student_id', $student->id)->where('status', 'present')->count();
        $attendancePct   = $totalAttendance > 0 ? round(($presentDays / $totalAttendance) * 100, 1) : 100;

        return view('exams.report-card', compact(
            'exam', 'student', 'marks', 'totalMarksObtained', 'totalMaxMarks',
            'overallPercentage', 'overallGrade', 'totalAttendance', 'presentDays', 'attendancePct'
        ));
    }

    private function authorizeStudentResultsAccess($user): void
    {
        abort_unless(
            $user && ($user->isAdmin() || $user->isStudent() || $user->isTeacher() || $user->isParent()),
            403,
            'Student results access required.'
        );
    }

    private function authorizeUniversityPortal($user): void
    {
        abort_unless($user && ($user->isStudent() || $user->isTeacher() || $user->isParent()), 403, 'University Portal access required.');
    }
}
