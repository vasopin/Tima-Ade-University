<?php

namespace App\Http\Controllers;

use App\Models\AcademicStanding;
use App\Models\ExamMark;
use App\Models\GraduationRecord;
use App\Models\Student;
use Illuminate\Http\Request;

class GraduationRecordController extends Controller
{
    private function authorizeRegistrar(): void
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isRegistrar()), 403, 'Registrar access required.');
    }

    public function index(Request $request)
    {
        $this->authorizeRegistrar();

        $query = GraduationRecord::with(['student.user', 'approvedBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $records = $query->latest()->paginate(15)->withQueryString();

        return view('registrar.graduation.index', compact('records'));
    }

    public function create()
    {
        $this->authorizeRegistrar();

        $students = Student::with('user')->whereHas('user')
            ->whereDoesntHave('graduationRecords', fn ($q) => $q->whereIn('status', ['eligible', 'graduated']))
            ->get();

        return view('registrar.graduation.create', compact('students'));
    }

    public function store(Request $request)
    {
        $this->authorizeRegistrar();

        $validated = $request->validate([
            'student_id'  => ['required', 'exists:students,id'],
            'degree_name' => ['required', 'string', 'max:255'],
            'honors'      => ['nullable', 'string', 'max:500'],
        ]);

        $eligibility = $this->computeEligibility((int) $validated['student_id']);

        $record = GraduationRecord::create([
            'student_id'  => $validated['student_id'],
            'degree_name' => $validated['degree_name'],
            'honors'      => $validated['honors'] ?? null,
            'status'      => $eligibility['eligible'] ? 'eligible' : 'not_eligible',
        ]);

        return redirect()->route('registrar.graduation.index')
            ->with($eligibility['eligible'] ? 'success' : 'error', $eligibility['eligible']
                ? 'Graduation record created — student is eligible based on current academic standing.'
                : 'Graduation record created as NOT eligible: ' . $eligibility['reason']);
    }

    public function approve(Request $request, GraduationRecord $graduationRecord)
    {
        $this->authorizeRegistrar();

        abort_unless($graduationRecord->status === 'eligible', 422, 'Only records marked eligible can be approved for graduation.');

        $graduationRecord->update([
            'status'          => 'graduated',
            'graduation_date' => now()->toDateString(),
            'approved_by'     => auth()->id(),
            'approved_at'     => now(),
        ]);

        return back()->with('success', 'Graduation approved and recorded.');
    }

    private function computeEligibility(int $studentId): array
    {
        $marks = ExamMark::where('student_id', $studentId)->count();

        if ($marks === 0) {
            return ['eligible' => false, 'reason' => 'no examination records exist for this student.'];
        }

        $standing = AcademicStanding::computeForStudent($studentId);

        if ($standing['status'] !== 'good') {
            return ['eligible' => false, 'reason' => "current academic standing is '{$standing['status']}', not 'good'."];
        }

        if ($standing['cumulative_gpa'] < 2.0) {
            return ['eligible' => false, 'reason' => "cumulative GPA ({$standing['cumulative_gpa']}) is below the minimum 2.00 required."];
        }

        return ['eligible' => true, 'reason' => null];
    }
}
