<?php

namespace App\Http\Controllers;

use App\Models\AdvisorAssignment;
use App\Models\CourseSection;
use App\Models\Department;
use App\Models\ExamMark;
use App\Models\Program;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentHeadController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()?->isDepartmentHead(), 403, 'Department Head access required.');
            $this->department($request);

            return $next($request);
        });
    }

    private function department(Request $request): Department
    {
        $department = Department::where('head_user_id', $request->user()->id)
            ->where('is_active', true)
            ->with('faculty')
            ->first();
        abort_unless($department, 403, 'A department assignment is required for Department Head access.');

        return $department;
    }

    public function programs(Request $request)
    {
        $department = $this->department($request);
        $items = Program::where('department_id', $department->id)
            ->where('is_active', true)
            ->withCount('students')
            ->withCount(['curricula'])
            ->orderBy('name')
            ->get();

        return $this->view('Programs', 'Authoritative active programs in your assigned department.', 'programs', $items, compact('department'));
    }

    public function courses(Request $request)
    {
        $department = $this->department($request);
        $items = Subject::where('department_id', $department->id)
            ->where('is_active', true)
            ->withCount('courseSections')
            ->with('prerequisites.prerequisiteCourse')
            ->orderBy('name')
            ->get();

        return $this->view('Courses', 'Active courses and their department-owned sections.', 'courses', $items, compact('department'));
    }

    public function sections(Request $request)
    {
        $department = $this->department($request);
        $items = CourseSection::whereHas('course', fn ($query) => $query->where('department_id', $department->id))
            ->with(['course', 'term', 'teacher'])
            ->withCount('enrollments')
            ->latest()
            ->get();

        return $this->view('Course Sections', 'Teaching and enrollment activity for department course sections.', 'sections', $items, compact('department'));
    }

    public function students(Request $request)
    {
        $department = $this->department($request);
        $items = Student::whereHas('program', fn ($query) => $query->where('department_id', $department->id))
            ->with(['user', 'program'])
            ->withCount('enrollments')
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where(function ($nested) use ($search): void {
                    $nested->where('student_id', 'like', "%{$search}%")
                        ->orWhere('admission_number', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return $this->view('Students', 'Students belonging to programs in your assigned department.', 'students', $items, compact('department'));
    }

    public function instructors(Request $request)
    {
        $department = $this->department($request);
        $items = CourseSection::whereHas('course', fn ($query) => $query->where('department_id', $department->id))
            ->whereNotNull('teacher_id')
            ->with('teacher')
            ->withCount('enrollments')
            ->get()
            ->groupBy('teacher_id')
            ->map(fn ($sections) => [
                'teacher' => $sections->first()->teacher,
                'sections' => $sections->count(),
                'enrollments' => $sections->sum('enrollments_count'),
            ])->values();

        return $this->view('Instructors', 'Teaching assignments within your assigned department.', 'instructors', $items, compact('department'));
    }

    public function academicPerformance(Request $request)
    {
        $department = $this->department($request);
        $items = ExamMark::whereHas('exam.subject', fn ($query) => $query->where('department_id', $department->id))
            ->whereNotNull('marks_obtained')
            ->with('exam.subject')
            ->get()
            ->groupBy(fn ($mark) => $mark->exam?->subject_id)
            ->map(fn ($marks) => [
                'course' => $marks->first()->exam?->subject,
                'assessments' => $marks->count(),
                'average' => round((float) $marks->avg('marks_obtained'), 1),
                'pass_rate' => round(($marks->where('grade', '!=', 'F')->count() / max(1, $marks->count())) * 100, 1),
            ])->filter(fn ($item) => $item['course'])->values();

        return $this->view('Academic Performance', 'Recorded academic marks for department courses.', 'performance', $items, compact('department'));
    }

    public function advising(Request $request)
    {
        $department = $this->department($request);
        $studentIds = Student::whereHas('program', fn ($query) => $query->where('department_id', $department->id))->select('id');
        $items = AdvisorAssignment::whereIn('student_id', $studentIds)
            ->with(['student.user', 'advisor'])
            ->where('is_active', true)
            ->latest()
            ->get();

        return $this->view('Advising', 'Active advising assignments for department students.', 'advising', $items, compact('department'));
    }

    public function analytics(Request $request)
    {
        $department = $this->department($request);
        $studentIds = Student::whereHas('program', fn ($query) => $query->where('department_id', $department->id))->select('id');
        $markQuery = ExamMark::whereHas('exam.subject', fn ($query) => $query->where('department_id', $department->id))->whereNotNull('marks_obtained');

        return view('department-head.analytics', [
            'department' => $department,
            'programs' => Program::where('department_id', $department->id)->where('is_active', true)->count(),
            'courses' => Subject::where('department_id', $department->id)->where('is_active', true)->count(),
            'sections' => CourseSection::whereHas('course', fn ($query) => $query->where('department_id', $department->id))->count(),
            'students' => (clone $studentIds)->count(),
            'enrollments' => DB::table('enrollments')->whereIn('student_id', $studentIds)->whereIn('status', \App\Models\Enrollment::ACTIVE_STATUSES)->count(),
            'averageMark' => round((float) ($markQuery->avg('marks_obtained') ?? 0), 1),
            'advising' => AdvisorAssignment::whereIn('student_id', $studentIds)->where('is_active', true)->count(),
        ]);
    }

    public function reports(Request $request)
    {
        $department = $this->department($request);

        return view('department-head.reports', compact('department'));
    }

    private function view(string $title, string $description, string $type, $items, array $data)
    {
        return view('department-head.read-only', compact('title', 'description', 'type', 'items') + $data);
    }
}
