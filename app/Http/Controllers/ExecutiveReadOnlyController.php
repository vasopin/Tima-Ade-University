<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\ExamMark;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExecutiveReadOnlyController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()?->isExecutive(), 403, 'Executive access required.');

            return $next($request);
        });
    }

    public function faculties()
    {
        $faculties = Faculty::where('is_active', true)
            ->with(['departments' => fn ($query) => $query->where('is_active', true)])
            ->withCount(['departments as active_departments_count' => fn ($query) => $query->where('is_active', true)])
            ->orderBy('name')
            ->get()
            ->each(function (Faculty $faculty): void {
                $departmentIds = $faculty->departments->pluck('id');
                $faculty->programs_count = Program::whereIn('department_id', $departmentIds)->where('is_active', true)->count();
                $faculty->students_count = Student::whereHas('program', fn ($query) => $query->whereIn('department_id', $departmentIds))->count();
            });

        return view('executive.read-only', [
            'title' => 'Faculties',
            'description' => 'University-wide faculty structure and student distribution.',
            'type' => 'faculties',
            'items' => $faculties,
        ]);
    }

    public function departments()
    {
        $departments = Department::where('is_active', true)
            ->with(['faculty', 'head'])
            ->withCount(['programs as active_programs_count' => fn ($query) => $query->where('is_active', true)])
            ->withCount('subjects')
            ->orderBy('name')
            ->get()
            ->each(function (Department $department): void {
                $department->students_count = Student::whereHas('program', fn ($query) => $query->where('department_id', $department->id))->count();
            });

        return view('executive.read-only', [
            'title' => 'Departments',
            'description' => 'University-wide departments, leadership, and academic footprint.',
            'type' => 'departments',
            'items' => $departments,
        ]);
    }

    public function programs()
    {
        $programs = Program::where('is_active', true)
            ->with('department.faculty')
            ->withCount('students')
            ->orderBy('name')
            ->get();

        return view('executive.read-only', [
            'title' => 'Programs',
            'description' => 'The authoritative active program catalogue across the university.',
            'type' => 'programs',
            'items' => $programs,
        ]);
    }

    public function students(Request $request)
    {
        $students = Student::with(['user', 'program.department.faculty', 'academicStanding'])
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

        return view('executive.read-only', [
            'title' => 'Students',
            'description' => 'University-wide student records with program and academic context.',
            'type' => 'students',
            'items' => $students,
        ]);
    }

    public function academicPerformance()
    {
        $performance = ExamMark::query()
            ->join('students', 'students.id', '=', 'exam_marks.student_id')
            ->join('programs', 'programs.id', '=', 'students.program_id')
            ->join('departments', 'departments.id', '=', 'programs.department_id')
            ->join('faculties', 'faculties.id', '=', 'departments.faculty_id')
            ->whereNotNull('exam_marks.marks_obtained')
            ->select(
                'faculties.name as faculty_name',
                'departments.name as department_name',
                'programs.name as program_name',
                DB::raw('COUNT(exam_marks.id) as assessment_count'),
                DB::raw('ROUND(AVG(exam_marks.marks_obtained), 1) as average_mark'),
                DB::raw("SUM(CASE WHEN exam_marks.grade = 'F' THEN 1 ELSE 0 END) as failed_count")
            )
            ->groupBy('faculties.id', 'faculties.name', 'departments.id', 'departments.name', 'programs.id', 'programs.name')
            ->orderBy('faculties.name')
            ->orderBy('departments.name')
            ->orderBy('programs.name')
            ->get();

        return view('executive.read-only', [
            'title' => 'Academic Performance',
            'description' => 'Observed assessment performance from recorded academic marks.',
            'type' => 'academic-performance',
            'items' => $performance,
        ]);
    }

    public function analytics()
    {
        return view('executive.analytics', [
            'faculties' => Faculty::where('is_active', true)->count(),
            'departments' => Department::where('is_active', true)->count(),
            'programs' => Program::where('is_active', true)->count(),
            'students' => Student::count(),
            'activeStudents' => Student::where('status', 'active')->count(),
            'marks' => ExamMark::whereNotNull('marks_obtained')->count(),
            'averageMark' => round((float) (ExamMark::whereNotNull('marks_obtained')->avg('marks_obtained') ?? 0), 1),
        ]);
    }

    public function reports()
    {
        return view('executive.reports');
    }
}
