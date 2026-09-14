<?php

namespace App\Http\Controllers;

use App\Models\AcademicStanding;
use App\Models\AdvisorAssignment;
use App\Models\Department;
use App\Models\Student;
use App\Models\StudentHold;
use App\Models\StudentTransfer;
use App\Models\Program;
use App\Services\StudentLifecycleService;
use Illuminate\Http\Request;

class Student360Controller extends Controller
{
    public function __construct(private StudentLifecycleService $lifecycle) {}

    public function index(Request $request)
    {
        $this->authorizeWorkspace();
        $query = Student::with(['user', 'program.department.faculty.campus'])->whereHas('user');
        $this->scope($query);
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('student_id', 'like', "%{$search}%")->orWhere('admission_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }
        if ($request->filled('program_id')) {
            $query->where('program_id', $request->integer('program_id'));
        }
        if ($request->filled('lifecycle_status')) {
            $query->where('lifecycle_status', $request->string('lifecycle_status'));
        }
        $students = $query->latest()->paginate(25)->withQueryString();
        $programs = Program::query()->where('is_active', true)
            ->when(auth()->user()?->isAcademicAdvisor(), fn ($query) => $query->whereIn('id', Student::query()
                ->whereIn('id', AdvisorAssignment::query()
                    ->where('advisor_id', auth()->id())
                    ->where('is_active', true)
                    ->select('student_id'))
                ->select('program_id')))
            ->orderBy('name')->get();
        return view('students.index', [
            'students' => $students,
            'classes' => collect(),
            'programs' => $programs,
            'student360' => true,
        ]);
    }

    public function show(Student $student)
    {
        $this->authorizeStudent($student);
        $student->load([
            'user', 'program.department.faculty.campus', 'schoolClass', 'section',
            'enrollments.courseSection.course', 'enrollments.courseSection.term',
            'examMarks.exam.subject', 'transcripts', 'academicStanding',
            'attendances', 'holds.createdBy', 'statusHistories.actor',
            'programHistories.fromProgram', 'programHistories.toProgram',
            'transfers.fromProgram', 'transfers.toProgram',
            'advisorAssignments.advisor', 'advisingAppointments.advisor', 'advisingNotes.advisor',
        ]);
        $programs = Program::query()->where('is_active', true)
            ->when(auth()->user()?->isAcademicAdvisor(), fn ($query) => $query->whereIn('id', [$student->program_id]))
            ->orderBy('name')->get();
        if ($this->canViewFinancials()) {
            $student->load(['invoices.items', 'feePayments.feeStructure', 'scholarshipAwards.scholarship']);
        }
        $standing = $student->academicStanding ?: AcademicStanding::computeForStudent($student->id);
        $attendanceTotal = $student->attendances->count();
        $attendanceRate = $attendanceTotal ? round($student->attendances->where('status', 'present')->count() / $attendanceTotal * 100, 1) : 0;
        return view('students.student-360', compact('student', 'standing', 'attendanceRate', 'programs'));
    }

    public function status(Request $request, Student $student)
    {
        $this->authorizeRegistrar();
        $data = $request->validate(['status' => ['required', 'string'], 'effective_date' => ['required', 'date'], 'reason' => ['required', 'string', 'max:2000'], 'notes' => ['nullable', 'string', 'max:2000']]);
        $this->lifecycle->changeStatus($student, $data['status'], $data, auth()->id());
        return back()->with('success', 'Student lifecycle status updated.');
    }

    public function hold(Request $request, Student $student)
    {
        $this->authorizeRegistrar();
        $data = $request->validate(['type' => ['required', 'in:finance,academic,disciplinary,registration,library,administrative'], 'reason' => ['required', 'string', 'max:2000'], 'effective_date' => ['nullable', 'date'], 'notes' => ['nullable', 'string', 'max:2000']]);
        $this->lifecycle->placeHold($student, $data, auth()->id());
        return back()->with('success', 'Student hold created.');
    }

    public function releaseHold(StudentHold $hold)
    {
        $this->authorizeRegistrar();
        $this->lifecycle->releaseHold($hold, auth()->id());
        return back()->with('success', 'Student hold released.');
    }

    public function transfer(Request $request, Student $student)
    {
        $this->authorizeRegistrar();
        $data = $request->validate(['to_program_id' => ['required', 'exists:programs,id'], 'type' => ['nullable', 'in:program,department,faculty,campus'], 'effective_date' => ['nullable', 'date'], 'reason' => ['required', 'string', 'max:2000']]);
        $this->lifecycle->requestTransfer($student, Program::findOrFail($data['to_program_id']), $data, auth()->id());
        return back()->with('success', 'Transfer request submitted for review.');
    }

    public function reviewTransfer(Request $request, StudentTransfer $transfer)
    {
        $this->authorizeRegistrar();
        $data = $request->validate(['status' => ['required', 'in:approved,rejected'], 'notes' => ['nullable', 'string', 'max:2000']]);
        $this->lifecycle->reviewTransfer($transfer->load(['student', 'toProgram']), $data['status'], auth()->id(), $data['notes'] ?? null);
        return back()->with('success', 'Transfer request reviewed.');
    }

    private function authorizeWorkspace(): void
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isRegistrar() || $user->isStaff() || $user->isDean() || $user->isDepartmentHead() || $user->isTeacher() || $user->isAcademicAdvisor() || $user->isFinanceOfficer()), 403);
    }

    private function authorizeRegistrar(): void
    {
        abort_unless(auth()->user()?->isAdmin() || auth()->user()?->isRegistrar(), 403, 'Registrar access required.');
    }

    private function canViewFinancials(): bool
    {
        return auth()->user()?->isAdmin() || auth()->user()?->isRegistrar()
            || auth()->user()?->isFinanceOfficer() || auth()->user()?->isStudent()
            || auth()->user()?->isParent();
    }

    private function authorizeStudent(Student $student): void
    {
        $user = auth()->user();
        if ($user?->isStudent()) {
            abort_unless($user->student?->id === $student->id, 403);
            return;
        }
        if ($user?->isParent()) {
            abort_unless($user->guardian?->students()->whereKey($student->id)->exists(), 403);
            return;
        }
        $query = Student::query()->whereKey($student->id);
        $this->scope($query);
        abort_unless($query->exists(), 403);
    }

    private function scope($query): void
    {
        $user = auth()->user();
        if ($user->isDean()) {
            abort_unless($user->faculty_id, 403);
            $query->whereHas('program.department', fn ($q) => $q->where('faculty_id', $user->faculty_id));
        } elseif ($user->isDepartmentHead()) {
            $departmentId = $user->headedDepartment?->id;
            abort_unless($departmentId, 403);
            $query->whereHas('program', fn ($q) => $q->where('department_id', $departmentId));
        } elseif ($user->isTeacher()) {
            $query->whereHas('enrollments.courseSection', fn ($q) => $q->where('teacher_id', $user->id));
        } elseif ($user->isAcademicAdvisor()) {
            $query->whereIn('id', AdvisorAssignment::query()
                ->where('advisor_id', $user->id)
                ->where('is_active', true)
                ->select('student_id'));
        } elseif ($user->isFinanceOfficer()) {
            $query->where(function ($q): void {
                $q->whereHas('invoices')->orWhereHas('feePayments');
            });
        }
    }
}
