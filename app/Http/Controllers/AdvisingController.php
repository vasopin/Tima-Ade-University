<?php

namespace App\Http\Controllers;

use App\Models\AdvisingAppointment;
use App\Models\AdvisingNote;
use App\Models\AdvisorAssignment;
use App\Models\AcademicStanding;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\CoursePrerequisite;
use App\Models\Curriculum;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Student;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdvisingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        abort_unless($user, 403);

        if ($user->isStudent()) {
            $student = Student::where('user_id', $user->id)->firstOrFail();
            $assignment = AdvisorAssignment::with('advisor')
                ->where('student_id', $student->id)->where('is_active', true)->latest('assigned_at')->first();
            $appointments = AdvisingAppointment::with('advisor')
                ->where('student_id', $student->id)->latest('scheduled_at')->get();
            $notes = AdvisingNote::with('advisor')
                ->where('student_id', $student->id)->where('is_private', false)->latest()->get();

            return view('advising.index', compact('student', 'assignment', 'appointments', 'notes'));
        }

        abort_unless($this->canManageAdvising($user), 403, 'Academic advising access required.');

        $isGlobalManager = in_array($user->role?->slug, ['admin', 'super_admin', 'staff', 'registrar'], true);
        $assignments = AdvisorAssignment::with(['student.user', 'advisor'])
            ->where('is_active', true)
            ->when(! $isGlobalManager, fn ($query) => $query->where('advisor_id', $user->id))
            ->latest('assigned_at')->get();
        $appointments = AdvisingAppointment::with(['student.user', 'advisor'])
            ->whereIn('status', ['requested', 'confirmed'])
            ->when(! $isGlobalManager, fn ($query) => $query->where('advisor_id', $user->id))
            ->orderBy('scheduled_at')->get();
        $students = Student::with('user')
            ->where('status', 'active')
            ->when(! $isGlobalManager, fn ($query) => $query->whereIn('id', AdvisorAssignment::query()
                ->where('advisor_id', $user->id)
                ->where('is_active', true)
                ->select('student_id')))
            ->orderBy('student_id')->get();
        $advisors = User::with('role')->whereHas('role', fn ($query) => $query->whereIn('slug', [
            'admin', 'super_admin', 'staff', 'teacher', 'registrar', 'academic_advisor',
        ]))->where('is_active', true)
            ->when(! $isGlobalManager, fn ($query) => $query->whereKey($user->id))
            ->orderBy('name')->get();

        return view('advising.index', compact('assignments', 'appointments', 'students', 'advisors'));
    }

    public function dashboard(Request $request)
    {
        $user = auth()->user();
        abort_unless($user && $this->canManageAdvising($user), 403, 'Academic advising access required.');

        $year = AcademicYear::find($request->integer('academic_year_id'))
            ?? AcademicYear::where('is_current', true)->first();
        if ($request->filled('academic_year_id') && !$year) {
            abort(404);
        }
        $term = $request->filled('term_id')
            ? $year?->terms()->find($request->integer('term_id'))
            : $year?->terms()->where('is_current', true)->first();
        if ($request->filled('term_id') && !$term) {
            abort(404);
        }

        $assignedStudentIds = AdvisorAssignment::query()
            ->where('advisor_id', $user->id)
            ->where('is_active', true)
            ->select('student_id');
        $program = Program::whereIn('id', Student::whereIn('id', $assignedStudentIds)->select('program_id'))
            ->where('is_active', true)
            ->find($request->integer('program_id'));
        if ($request->filled('program_id') && !$program) {
            abort(404);
        }

        $students = Student::with([
            'user', 'program.department', 'academicStanding',
            'enrollments.courseSection.course', 'enrollments.courseSection.term',
            'advisingAppointments' => fn ($query) => $query->where('advisor_id', $user->id)->latest('scheduled_at'),
            'advisingNotes' => fn ($query) => $query->where(function ($note) use ($user): void {
                $note->where('advisor_id', $user->id)->orWhere('is_private', false);
            })->latest(),
        ])
            ->whereIn('students.id', $assignedStudentIds)
            ->where('students.status', 'active')
            ->when($program, fn ($query) => $query->where('program_id', $program->id))
            ->when($term, fn ($query) => $query->whereHas('enrollments.courseSection', fn ($section) => $section->where('term_id', $term->id)))
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = trim((string) $request->input('search'));
                $query->where(function ($student) use ($search): void {
                    $student->where('student_id', 'like', "%{$search}%")
                        ->orWhere('admission_number', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderBy('student_id')
            ->get();
        $studentIds = $students->modelKeys();
        $studentScope = Student::whereIn('id', $studentIds);
        $appointments = AdvisingAppointment::with('student.user')
            ->where('advisor_id', $user->id)
            ->whereIn('student_id', $studentIds)
            ->orderBy('scheduled_at')->get();
        $followUps = AdvisingNote::with('student.user')
            ->where('advisor_id', $user->id)
            ->whereNotNull('follow_up_at')
            ->whereDate('follow_up_at', '<=', now()->addDays(14))
            ->whereIn('student_id', $studentIds)
            ->orderBy('follow_up_at')->get();
        $attendance = Attendance::whereIn('student_id', $studentIds)
            ->whereBetween('attendance_date', [
                $term?->starts_on?->toDateString() ?? $year?->starts_on?->toDateString() ?? now()->startOfYear()->toDateString(),
                $term?->ends_on?->toDateString() ?? $year?->ends_on?->toDateString() ?? now()->endOfYear()->toDateString(),
            ])->get();
        $attendanceRates = $attendance->groupBy('student_id')->map(
            fn ($rows) => $rows->count() ? round(($rows->whereIn('status', ['present', 'late'])->count() / $rows->count()) * 100, 1) : 0
        );
        $standingRisk = AcademicStanding::whereIn('student_id', $studentIds)
            ->whereIn('status', ['probation', 'suspended', 'dismissed'])->pluck('student_id')->all();
        $attentionIds = collect($standingRisk)
            ->merge($attendance->whereIn('status', ['absent'])->groupBy('student_id')->filter(fn ($rows) => $rows->count() >= 2)->keys())
            ->merge($students->filter(fn ($student) => $student->advisingNotes->contains(fn ($note) => $note->follow_up_at?->isPast()))->modelKeys())
            ->unique();

        $progress = $students->mapWithKeys(function (Student $student): array {
            $curriculum = Curriculum::where('program_id', $student->program_id)
                ->whereIn('status', ['active', 'published'])->latest('version')->first();
            $required = $curriculum?->courses()->wherePivot('is_required', true)->count() ?? 0;
            $completed = $curriculum
                ? $curriculum->courses()->wherePivot('is_required', true)->whereIn('subjects.id', function ($query) use ($student): void {
                    $query->select('course_sections.course_id')
                        ->from('enrollments')
                        ->join('course_sections', 'course_sections.id', '=', 'enrollments.course_section_id')
                        ->where('enrollments.student_id', $student->id)
                        ->where('enrollments.status', 'completed');
                })->count()
                : 0;
            return [$student->id => ['curriculum' => $curriculum, 'required' => $required, 'completed' => $completed, 'percent' => $required ? round(($completed / $required) * 100, 1) : 0]];
        });
        $recommendations = $students->mapWithKeys(function (Student $student) use ($progress): array {
            $curriculum = $progress[$student->id]['curriculum'];
            if (!$curriculum) {
                return [$student->id => collect()];
            }
            $completedCourseIds = Enrollment::where('student_id', $student->id)->where('status', 'completed')
                ->join('course_sections', 'course_sections.id', '=', 'enrollments.course_section_id')->pluck('course_sections.course_id');
            $currentCourseIds = Enrollment::where('student_id', $student->id)->whereIn('status', Enrollment::ACTIVE_STATUSES)
                ->join('course_sections', 'course_sections.id', '=', 'enrollments.course_section_id')->pluck('course_sections.course_id');
            $items = $curriculum->courses()->wherePivot('is_required', true)
                ->whereNotIn('subjects.id', $completedCourseIds)->whereNotIn('subjects.id', $currentCourseIds)->get();
            return [$student->id => $items->filter(function ($course) use ($completedCourseIds): bool {
                return !CoursePrerequisite::where('course_id', $course->id)->whereNotIn('prerequisite_course_id', $completedCourseIds)->exists();
            })->values()];
        });
        $recentNotes = AdvisingNote::with('student.user')
            ->where('advisor_id', $user->id)->whereIn('student_id', $studentIds)
            ->latest()->take(8)->get();

        return view('advising.dashboard', [
            'user' => $user, 'year' => $year, 'term' => $term, 'program' => $program,
            'years' => AcademicYear::orderByDesc('starts_on')->get(),
            'terms' => $year?->terms()->orderBy('starts_on')->get() ?? collect(),
            'programs' => Program::whereIn('id', Student::whereIn('id', $assignedStudentIds)->select('program_id'))->where('is_active', true)->orderBy('name')->get(),
            'students' => $students, 'appointments' => $appointments, 'followUps' => $followUps,
            'attendanceRates' => $attendanceRates, 'recentNotes' => $recentNotes,
            'progress' => $progress, 'recommendations' => $recommendations,
            'stats' => [
                'assigned' => $students->count(),
                'upcoming' => $appointments->whereIn('status', ['requested', 'confirmed'])->where('scheduled_at', '>=', now())->count(),
                'pending_followups' => $followUps->where('follow_up_at', '<=', now())->count(),
                'attention' => $attentionIds->intersect($studentIds)->count(),
                'recent_activity' => AdvisingNote::where('advisor_id', $user->id)->whereIn('student_id', $studentIds)->where('created_at', '>=', now()->subDays(30))->count(),
            ],
        ]);
    }

    public function storeAppointment(Request $request): RedirectResponse
    {
        $user = auth()->user();
        abort_unless($user, 403);

        $data = $request->validate([
            'scheduled_at' => ['required', 'date', 'after:now'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:120'],
            'mode' => ['required', 'in:in_person,online,phone'],
            'topic' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'student_id' => ['nullable', 'integer', 'exists:students,id'],
            'advisor_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        if ($user->isStudent()) {
            $student = Student::where('user_id', $user->id)->firstOrFail();
            $data['student_id'] = $student->id;
            $data['advisor_id'] = AdvisorAssignment::where('student_id', $student->id)
                ->where('is_active', true)->latest('assigned_at')->value('advisor_id');
        } else {
            abort_unless($this->canManageAdvising($user), 403);
            abort_unless(!empty($data['student_id']), 422, 'A student is required.');
            abort_unless($this->canAccessStudent($user, (int) $data['student_id']), 403, 'You may only advise assigned students.');
            $data['advisor_id'] = $data['advisor_id'] ?? AdvisorAssignment::where('student_id', $data['student_id'])
                ->where('is_active', true)->value('advisor_id');
        }

        AdvisingAppointment::create($data + [
            'requested_by' => $user->id,
            'status' => 'requested',
        ]);

        return back()->with('success', 'Advising appointment requested.');
    }

    public function storeNote(Request $request): RedirectResponse
    {
        $user = auth()->user();
        abort_unless($user && $this->canManageAdvising($user), 403, 'Academic advising access required.');

        $data = $request->validate([
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'body' => ['required', 'string', 'max:10000'],
            'is_private' => ['nullable', 'boolean'],
            'follow_up_at' => ['nullable', 'date'],
        ]);

        $data['advisor_id'] = $user->id;
        abort_unless($this->canAccessStudent($user, (int) $data['student_id']), 403, 'You may only advise assigned students.');
        AdvisingNote::create($data);

        return back()->with('success', 'Advising note saved.');
    }

    public function assign(Request $request): RedirectResponse
    {
        $user = auth()->user();
        abort_unless($user && in_array($user->role?->slug, ['admin', 'super_admin', 'registrar'], true), 403);

        $data = $request->validate([
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'advisor_id' => ['required', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $advisor = User::with('role')->findOrFail($data['advisor_id']);
        abort_unless(in_array($advisor->role?->slug, ['admin', 'super_admin', 'staff', 'teacher', 'registrar', 'academic_advisor'], true), 422, 'Selected user cannot advise students.');

        AdvisorAssignment::where('student_id', $data['student_id'])->where('is_active', true)->update(['is_active' => false]);
        AdvisorAssignment::create($data + [
            'assigned_by' => $user->id,
            'assigned_at' => Carbon::today(),
            'is_active' => true,
        ]);

        return back()->with('success', 'Academic advisor assigned.');
    }

    public function updateAppointment(Request $request, AdvisingAppointment $appointment): RedirectResponse
    {
        $user = auth()->user();
        abort_unless($user && $this->canManageAdvising($user), 403);
        abort_unless($this->canAccessStudent($user, $appointment->student_id), 403, 'You may only update appointments for assigned students.');

        $data = $request->validate([
            'status' => ['required', 'in:confirmed,completed,cancelled'],
            'outcome' => ['nullable', 'string', 'max:5000'],
        ]);
        $appointment->update($data);

        return back()->with('success', 'Appointment updated.');
    }

    private function canManageAdvising(User $user): bool
    {
        return in_array($user->role?->slug, ['admin', 'super_admin', 'staff', 'teacher', 'registrar', 'academic_advisor'], true);
    }

    private function canAccessStudent(User $user, int $studentId): bool
    {
        if (in_array($user->role?->slug, ['admin', 'super_admin', 'registrar'], true)) {
            return true;
        }

        return AdvisorAssignment::where('advisor_id', $user->id)
            ->where('student_id', $studentId)
            ->where('is_active', true)->exists();
    }
}
