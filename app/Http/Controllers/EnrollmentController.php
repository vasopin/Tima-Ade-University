<?php

namespace App\Http\Controllers;

use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Services\EnrollmentRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EnrollmentController extends Controller
{
    public function __construct(private EnrollmentRegistrationService $registration) {}

    public function available(Request $request)
    {
        $user = auth()->user();
        abort_unless($user && $user->isStudent(), 403, 'Student access required.');
        $student = Student::where('user_id', $user->id)->firstOrFail();
        $term = \App\Models\Term::find($request->integer('term_id'))
            ?? \App\Models\Term::where('is_current', true)->first()
            ?? AcademicYear::where('is_current', true)->first()?->terms()->latest('starts_on')->first();
        $sections = CourseSection::with(['course.prerequisites.prerequisiteCourse', 'term'])
            ->where('status', 'open')
            ->when($term, fn ($query) => $query->where('term_id', $term->id))
            ->whereHas('course', fn ($query) => $query->where('is_active', true))
            ->withCount(['enrollments as active_enrollment_count' => fn ($query) => $query->whereIn('status', Enrollment::ACTIVE_STATUSES)])
            ->when($request->filled('search'), fn ($query) => $query->whereHas('course', fn ($course) => $course->where('name', 'like', '%'.trim($request->input('search')).'%')->orWhere('code', 'like', '%'.trim($request->input('search')).'%')))
            ->orderBy('course_id')->get();
        $current = $student->enrollments()->with('courseSection.course')->whereIn('status', Enrollment::ACTIVE_STATUSES)->get();
        return view('student.registration', compact('student', 'sections', 'current', 'term'));
    }

    private function studentFromUser(): Student
    {
        $user = auth()->user();
        abort_unless($user && $user->isStudent(), 403, 'Student access required.');
        return Student::where('user_id', $user->id)->firstOrFail();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'course_section_id' => ['required', 'exists:course_sections,id'],
            'status' => ['nullable', Rule::in(Enrollment::STATUSES)],
        ]);
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isRegistrar() || ($user->student && $user->student->id === (int) $data['student_id'])), 403, 'You may only manage your own enrollment.');
        $student = Student::findOrFail($data['student_id']);
        $section = CourseSection::findOrFail($data['course_section_id']);
        $override = $user->isAdmin() || $user->isRegistrar();
        if ($status = $data['status'] ?? null) {
            abort_unless($override, 403, 'Only registrars may set enrollment status.');
        }
        $enrollment = $this->registration->register($student, $section, $user->id, $override);
        if ($override && !empty($data['status']) && $data['status'] !== 'enrolled') {
            $enrollment->update([
                'status' => $data['status'],
                'status_changed_at' => now(),
                'action_reason' => 'Registrar enrollment status override',
            ]);
            $enrollment->refresh();
        }
        return response()->json($enrollment->load(['student', 'courseSection']), 201);
    }

    public function drop(Request $request, Enrollment $enrollment)
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isRegistrar() || $user->student?->id === $enrollment->student_id), 403);
        $this->registration->changeStatus($enrollment->load('courseSection.term'), 'dropped', $user->id, $request->input('reason'));
        return back()->with('success', 'Course dropped successfully.');
    }

    public function withdraw(Request $request, Enrollment $enrollment)
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isRegistrar() || $user->student?->id === $enrollment->student_id), 403);
        $data = $request->validate(['reason' => ['required', 'string', 'max:1000']]);
        $this->registration->changeStatus($enrollment->load('courseSection.term'), 'withdrawn', $user->id, $data['reason']);
        return back()->with('success', 'Course withdrawal recorded.');
    }

    public function history(Request $request)
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isRegistrar()), 403);
        $enrollments = Enrollment::with(['student.user', 'student.program', 'courseSection.course', 'courseSection.term'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('student_id'), fn ($query) => $query->where('student_id', $request->integer('student_id')))
            ->latest()->paginate(30)->withQueryString();
        return view('registrar.enrollment', compact('enrollments'));
    }
}
