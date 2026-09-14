<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Campus;
use App\Models\CoursePrerequisite;
use App\Models\CourseSection;
use App\Models\Curriculum;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AcademicFoundationController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isRegistrar()), 403, 'Academic administration privileges required.');
            return $next($request);
        });
    }

    public function index()
    {
        return view('academics.index', [
            'campuses' => Campus::withCount('faculties')->latest()->get(),
            'faculties' => Faculty::with('campus')->withCount('departments')->latest()->get(),
            'departments' => Department::with('faculty')->withCount('programs')->latest()->get(),
            'programs' => Program::with('department')->latest()->get(),
            'years' => AcademicYear::withCount('terms')->latest()->get(),
            'terms' => Term::with('academicYear')->latest()->get(),
            'courses' => Subject::with('department')->latest()->get(),
            'curricula' => Curriculum::with('program')->latest()->get(),
            'sections' => CourseSection::with(['course', 'term', 'teacher'])->latest()->get(),
        ]);
    }

    public function storeCampus(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'code' => ['required', 'string', 'max:50', 'unique:campuses,code'], 'description' => ['nullable', 'string'], 'is_active' => ['sometimes', 'boolean']]);
        $campus = Campus::create($data + ['is_active' => $request->boolean('is_active', true)]);
        return $this->created($request, $campus);
    }

    public function storeFaculty(Request $request)
    {
        $data = $request->validate(['campus_id' => ['nullable', 'exists:campuses,id'], 'name' => ['required', 'string', 'max:255'], 'code' => ['required', 'string', 'max:50'], 'description' => ['nullable', 'string'], 'is_active' => ['sometimes', 'boolean']]);
        $faculty = Faculty::create($data + ['is_active' => $request->boolean('is_active', true)]);
        return $this->created($request, $faculty);
    }

    public function storeDepartment(Request $request)
    {
        $data = $request->validate(['faculty_id' => ['required', 'exists:faculties,id'], 'head_user_id' => ['nullable', 'exists:users,id'], 'name' => ['required', 'string', 'max:255'], 'code' => ['required', 'string', 'max:50'], 'description' => ['nullable', 'string'], 'is_active' => ['sometimes', 'boolean']]);
        return $this->created($request, Department::create($data + ['is_active' => $request->boolean('is_active', true)]));
    }

    public function storeProgram(Request $request)
    {
        $data = $request->validate(['department_id' => ['required', 'exists:departments,id'], 'name' => ['required', 'string', 'max:255'], 'code' => ['required', 'string', 'max:50', 'unique:programs,code'], 'degree_type' => ['required', 'string', 'max:100'], 'duration_years' => ['nullable', 'integer', 'min:1', 'max:20'], 'total_credits' => ['nullable', 'numeric', 'min:0'], 'description' => ['nullable', 'string'], 'is_active' => ['sometimes', 'boolean']]);
        return $this->created($request, Program::create($data + ['is_active' => $request->boolean('is_active', true)]));
    }

    public function updateProgram(Request $request, Program $program)
    {
        $data = $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('programs', 'code')->ignore($program->id)],
            'degree_type' => ['required', 'string', 'max:100'],
            'duration_years' => ['nullable', 'integer', 'min:1', 'max:20'],
            'total_credits' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $program->update($data + ['is_active' => $request->boolean('is_active')]);

        return $this->created($request, $program->fresh());
    }

    public function storeAcademicYear(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100'], 'code' => ['required', 'string', 'max:50', 'unique:academic_years,code'], 'starts_on' => ['required', 'date'], 'ends_on' => ['required', 'date', 'after_or_equal:starts_on'], 'status' => ['required', 'string', 'max:30'], 'is_current' => ['sometimes', 'boolean']]);
        return $this->created($request, AcademicYear::create($data + ['is_current' => $request->boolean('is_current')]));
    }

    public function storeTerm(Request $request)
    {
        $data = $request->validate(['academic_year_id' => ['required', 'exists:academic_years,id'], 'name' => ['required', 'string', 'max:100'], 'code' => ['required', 'string', 'max:50'], 'starts_on' => ['required', 'date'], 'ends_on' => ['required', 'date', 'after_or_equal:starts_on'], 'registration_starts_on' => ['nullable', 'date'], 'registration_ends_on' => ['nullable', 'date', 'after_or_equal:registration_starts_on'], 'status' => ['required', 'string', 'max:30'], 'is_current' => ['sometimes', 'boolean']]);
        return $this->created($request, Term::create($data + ['is_current' => $request->boolean('is_current')]));
    }

    public function storeCourse(Request $request)
    {
        $data = $request->validate(['department_id' => ['nullable', 'exists:departments,id'], 'name' => ['required', 'string', 'max:255'], 'code' => ['required', 'string', 'max:50', 'unique:subjects,code'], 'description' => ['nullable', 'string'], 'credits' => ['nullable', 'numeric', 'min:0'], 'level' => ['nullable', 'string', 'max:50'], 'course_type' => ['required', 'string', 'max:50'], 'is_active' => ['sometimes', 'boolean']]);
        return $this->created($request, Subject::create($data + ['is_active' => $request->boolean('is_active', true)]));
    }

    public function storePrerequisite(Request $request)
    {
        $data = $request->validate(['course_id' => ['required', 'exists:subjects,id'], 'prerequisite_course_id' => ['required', 'exists:subjects,id', 'different:course_id'], 'minimum_grade' => ['nullable', 'string', 'max:10']]);
        return $this->created($request, CoursePrerequisite::create($data));
    }

    public function storeCurriculum(Request $request)
    {
        $data = $request->validate(['program_id' => ['required', 'exists:programs,id'], 'effective_academic_year_id' => ['nullable', 'exists:academic_years,id'], 'name' => ['required', 'string', 'max:255'], 'version' => ['required', 'string', 'max:50'], 'total_credits' => ['nullable', 'numeric', 'min:0'], 'status' => ['required', 'string', 'max:30']]);
        return $this->created($request, Curriculum::create($data));
    }

    public function attachCourse(Request $request, Curriculum $curriculum)
    {
        $data = $request->validate(['course_id' => ['required', 'exists:subjects,id'], 'is_required' => ['sometimes', 'boolean'], 'recommended_term' => ['nullable', 'integer', 'min:1'], 'credits' => ['nullable', 'numeric', 'min:0']]);
        $curriculum->courses()->syncWithoutDetaching([$data['course_id'] => [
            'is_required' => $request->boolean('is_required', true),
            'recommended_term' => $data['recommended_term'] ?? null,
            'credits' => $data['credits'] ?? null,
        ]]);
        return $request->expectsJson()
            ? response()->json($curriculum->load('courses'))
            : back()->with('success', 'Course added to curriculum successfully.');
    }

    public function storeSection(Request $request)
    {
        $data = $request->validate(['course_id' => ['required', 'exists:subjects,id'], 'term_id' => ['required', 'exists:terms,id'], 'teacher_id' => ['nullable', 'exists:users,id'], 'code' => ['required', 'string', 'max:50'], 'room' => ['nullable', 'string', 'max:100'], 'capacity' => ['nullable', 'integer', 'min:1'], 'status' => ['required', 'string', 'max:30']]);
        return $this->created($request, CourseSection::create($data));
    }

    private function created(Request $request, object $model)
    {
        return $request->expectsJson() ? response()->json($model, 201) : back()->with('success', 'Academic record created successfully.');
    }
}
