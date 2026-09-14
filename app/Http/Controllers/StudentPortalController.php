<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ExamMark;
use App\Models\FeePayment;
use App\Models\LibraryMember;
use App\Models\Student;
use App\Models\Timetable;
use Illuminate\Http\Request;

class StudentPortalController extends Controller
{
    private function student(): Student
    {
        $user = auth()->user();
        abort_unless($user && $user->isStudent(), 403, 'Student access required.');

        return Student::with(['user', 'schoolClass', 'section', 'schoolClass.subjects'])
            ->where('user_id', $user->id)->firstOrFail();
    }

    public function registration()
    {
        return app(EnrollmentController::class)->available(request());
    }

    public function schedule()
    {
        $student = $this->student();
        $schedule = Timetable::with(['subject', 'teacher.user', 'schoolClass', 'section'])
            ->where('school_class_id', $student->school_class_id)
            ->when($student->section_id, fn ($query) => $query->where(function ($sectionQuery) use ($student) {
                $sectionQuery->whereNull('section_id')->orWhere('section_id', $student->section_id);
            }))
            ->where('is_active', true)->orderBy('start_time')->get()->groupBy('day_of_week');
        return view('student.schedule', compact('student', 'schedule'));
    }

    public function academicRecords()
    {
        $student = $this->student();
        $marks = ExamMark::with(['exam.subject'])->where('student_id', $student->id)
            ->whereHas('exam.resultsApproval', fn ($query) => $query->whereIn('status', ['approved', 'published']))
            ->latest()->get();
        $transcripts = $student->transcripts()->latest('generated_at')->get();
        $totalPossible = $marks->sum(fn ($mark) => (float) ($mark->exam->total_marks ?? 0));
        $totalObtained = $marks->where('is_absent', false)->sum('marks_obtained');
        $cumulativeGpa = $totalPossible > 0 ? round(min(4, max(0, ($totalObtained / $totalPossible) * 4)), 2) : 0;
        return view('student.academic-records', compact('student', 'marks', 'transcripts', 'cumulativeGpa'));
    }

    public function fees()
    {
        $student = $this->student();
        $payments = FeePayment::with('feeStructure')->where('student_id', $student->id)->latest('payment_date')->paginate(15);
        $outstanding = $payments->getCollection()->whereIn('status', ['pending', 'partial', 'overdue'])->sum(fn ($payment) => $payment->total_due - (float) $payment->amount_paid);
        $invoices = $student->invoices()->with(['items', 'term'])->latest('issue_date')->get();
        if ($invoices->isNotEmpty()) {
            $outstanding = (float) $invoices->sum('balance');
        }
        $awards = $student->scholarshipAwards()->with('scholarship')->where('status', 'approved')->get();
        return view('student.fees', compact('student', 'payments', 'outstanding', 'invoices', 'awards'));
    }

    public function library()
    {
        $student = $this->student();
        $member = LibraryMember::with(['borrowings.book'])->where('user_id', $student->user_id)->first();
        return view('student.library', compact('student', 'member'));
    }
}
