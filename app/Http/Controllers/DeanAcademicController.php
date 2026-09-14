<?php

namespace App\Http\Controllers;

use App\Models\CourseSection;
use App\Models\Department;
use App\Models\Enrollment;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DeanAcademicController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(auth()->check() && auth()->user()->isDean(), 403, 'Dean access required.');
            abort_unless(auth()->user()->faculty_id, 403, 'A faculty assignment is required for Dean access.');
            return $next($request);
        });
    }

    private function faculty(): Faculty
    {
        return Faculty::whereKey(auth()->user()->faculty_id)->where('is_active', true)->firstOrFail();
    }

    private function departmentIds()
    {
        return Department::where('faculty_id', $this->faculty()->id)->select('id');
    }

    public function departments(Request $request)
    {
        $faculty = $this->faculty();
        $query = Department::where('faculty_id', $faculty->id)->with('head')->withCount(['programs', 'subjects']);
        $this->search($query, $request->string('search')->toString(), ['name', 'code']);
        return view('dean.resource', ['type' => 'departments', 'faculty' => $faculty, 'items' => $query->orderBy('name')->paginate(15)->withQueryString()]);
    }

    public function department(Department $department)
    {
        $faculty = $this->faculty();
        abort_unless($department->faculty_id === $faculty->id, 404);
        $department->load('head');
        $programs = $department->programs()->withCount('students')->orderBy('name')->get();
        $courses = $department->subjects()->withCount('courseSections')->orderBy('name')->get();
        return view('dean.department', compact('faculty', 'department', 'programs', 'courses'));
    }

    public function programs(Request $request)
    {
        $faculty = $this->faculty();
        $query = Program::whereHas('department', fn ($q) => $q->where('faculty_id', $faculty->id))->with('department')->withCount('students');
        $this->search($query, $request->string('search')->toString(), ['name', 'code', 'degree_type']);
        $query->when($request->filled('department_id'), fn ($q) => $q->where('department_id', $request->integer('department_id')));
        return view('dean.resource', ['type' => 'programs', 'faculty' => $faculty, 'items' => $query->orderBy('name')->paginate(15)->withQueryString(), 'departments' => Department::where('faculty_id', $faculty->id)->orderBy('name')->get()]);
    }

    public function courses(Request $request)
    {
        $faculty = $this->faculty();
        $query = Subject::whereHas('department', fn ($q) => $q->where('faculty_id', $faculty->id))
            ->with(['department', 'prerequisites.prerequisiteCourse'])->withCount('courseSections');
        $this->search($query, $request->string('search')->toString(), ['name', 'code']);
        $query->when($request->filled('department_id'), fn ($q) => $q->where('department_id', $request->integer('department_id')));
        return view('dean.resource', ['type' => 'courses', 'faculty' => $faculty, 'items' => $query->orderBy('name')->paginate(15)->withQueryString(), 'departments' => Department::where('faculty_id', $faculty->id)->orderBy('name')->get()]);
    }

    public function sections(Request $request)
    {
        $faculty = $this->faculty();
        $query = CourseSection::whereHas('course.department', fn ($q) => $q->where('faculty_id', $faculty->id))
            ->with(['course.department', 'term', 'teacher'])->withCount('enrollments');
        $query->when($request->filled('term_id'), fn ($q) => $q->where('term_id', $request->integer('term_id')));
        $this->search($query, $request->string('search')->toString(), ['code', 'room']);
        return view('dean.resource', ['type' => 'sections', 'faculty' => $faculty, 'items' => $query->latest()->paginate(15)->withQueryString()]);
    }

    public function students(Request $request)
    {
        $faculty = $this->faculty();
        $query = Student::whereHas('program.department', fn ($q) => $q->where('faculty_id', $faculty->id))
            ->with(['user', 'program.department', 'academicStanding'])->withCount('enrollments');
        if ($request->filled('search')) {
            $term = $request->string('search')->toString();
            $query->where(function ($nested) use ($term): void {
                $nested->where('student_id', 'like', "%{$term}%")
                    ->orWhere('roll_number', 'like', "%{$term}%")
                    ->orWhere('admission_number', 'like', "%{$term}%")
                    ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$term}%"));
            });
        }
        $query->when($request->filled('department_id'), fn ($q) => $q->whereHas('program', fn ($p) => $p->where('department_id', $request->integer('department_id'))));
        $query->when($request->filled('program_id'), fn ($q) => $q->where('program_id', $request->integer('program_id')));
        $query->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')));
        return view('dean.resource', ['type' => 'students', 'faculty' => $faculty, 'items' => $query->latest()->paginate(15)->withQueryString(), 'departments' => Department::where('faculty_id', $faculty->id)->orderBy('name')->get(), 'programs' => Program::whereHas('department', fn ($q) => $q->where('faculty_id', $faculty->id))->orderBy('name')->get()]);
    }

    public function staff(Request $request)
    {
        $faculty = $this->faculty();
        $teacherIds = CourseSection::whereHas('course.department', fn ($q) => $q->where('faculty_id', $faculty->id))->whereNotNull('teacher_id')->select('teacher_id')->distinct();
        $query = User::whereIn('id', $teacherIds)->with('teacher')->withCount(['courseSections as assigned_sections_count' => fn ($q) => $q->whereHas('course.department', fn ($d) => $d->where('faculty_id', $faculty->id))]);
        $this->search($query, $request->string('search')->toString(), ['name', 'email']);
        $heads = Department::where('faculty_id', $faculty->id)->with('head')->get()->pluck('head')->filter()->unique('id');
        return view('dean.resource', ['type' => 'staff', 'faculty' => $faculty, 'items' => $query->orderBy('name')->paginate(15)->withQueryString(), 'heads' => $heads]);
    }

    public function analytics()
    {
        $faculty = $this->faculty();
        $departments = Department::where('faculty_id', $faculty->id)->withCount('programs')->get();
        $programs = Program::whereHas('department', fn ($q) => $q->where('faculty_id', $faculty->id))->with('department')->withCount('students')->get();
        $sections = CourseSection::whereHas('course.department', fn ($q) => $q->where('faculty_id', $faculty->id))->withCount('enrollments')->get();
        return view('dean.analytics', compact('faculty', 'departments', 'programs', 'sections'));
    }

    public function reports()
    {
        return view('dean.reports', ['faculty' => $this->faculty()]);
    }

    public function exportStudents(): StreamedResponse
    {
        $faculty = $this->faculty();
        $students = Student::whereHas('program.department', fn ($q) => $q->where('faculty_id', $faculty->id))->with(['user', 'program.department'])->orderBy('id')->get();
        return response()->streamDownload(function () use ($students): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Student ID', 'Name', 'Program', 'Department', 'Status']);
            foreach ($students as $student) {
                fputcsv($handle, [$student->student_id ?? $student->admission_number, $student->user?->name, $student->program?->name, $student->program?->department?->name, $student->status]);
            }
            fclose($handle);
        }, 'faculty-students.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function search($query, string $term, array $columns): void
    {
        if ($term === '') {
            return;
        }
        $query->where(function ($nested) use ($term, $columns): void {
            foreach ($columns as $column) {
                $nested->orWhere($column, 'like', "%{$term}%");
            }
        });
    }
}
