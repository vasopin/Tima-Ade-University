<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\FeePayment;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\ExamMark;
use App\Models\Exam;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    private function authorizeFinance(): void
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isFinanceOfficer()), 403, 'Finance access required.');
    }

    public function attendance(Request $request)
    {
        $classes      = SchoolClass::where('is_active', true)->get();
        $selectedClass = $request->class_id;
        $startDate    = $request->start_date ?? now()->subDays(30)->toDateString();
        $endDate      = $request->end_date   ?? now()->toDateString();

        $query = Attendance::with(['student.user', 'student.schoolClass'])
            ->whereBetween('attendance_date', [$startDate, $endDate]);

        if ($selectedClass) {
            $query->whereHas('student', fn($q) => $q->where('school_class_id', $selectedClass));
        }

        $attendances = $query->orderBy('attendance_date', 'desc')->get();

        // Per-student summary
        $summary = $attendances->groupBy('student_id')->map(function ($records) {
            return [
                'student'  => $records->first()->student,
                'present'  => $records->where('status', 'present')->count(),
                'absent'   => $records->where('status', 'absent')->count(),
                'late'     => $records->where('status', 'late')->count(),
                'total'    => $records->count(),
                'pct'      => $records->count() > 0
                    ? round(($records->where('status', 'present')->count() / $records->count()) * 100, 1)
                    : 0,
            ];
        })->values();

        return view('reports.attendance', compact(
            'classes', 'selectedClass', 'startDate', 'endDate', 'summary', 'attendances'
        ));
    }

    public function exportAttendance(Request $request): StreamedResponse
    {
        $selectedClass = $request->class_id;
        $startDate     = $request->start_date ?? now()->subDays(30)->toDateString();
        $endDate       = $request->end_date   ?? now()->toDateString();

        $query = Attendance::with(['student.user', 'student.schoolClass', 'student.section'])
            ->whereBetween('attendance_date', [$startDate, $endDate]);

        if ($selectedClass) {
            $query->whereHas('student', fn($q) => $q->where('school_class_id', $selectedClass));
        }

        $records = $query->orderBy('attendance_date', 'desc')->get();

        $fileName = 'moon_college_attendance_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($records) {
            $handle = fopen('php://output', 'w');
            // BOM for UTF-8 compatibility in Excel
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Date', 'Student Name', 'Roll Number', 'Class', 'Section', 'Status', 'Remarks']);

            foreach ($records as $r) {
                fputcsv($handle, [
                    $r->attendance_date,
                    $r->student->user->name ?? 'N/A',
                    $r->student->roll_number ?? 'N/A',
                    $r->student->schoolClass->name ?? 'N/A',
                    $r->student->section->name ?? 'N/A',
                    ucfirst($r->status),
                    $r->remarks ?? '',
                ]);
            }
            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    /**
     * CSV export with authorization: students can only export their own attendance.
     */
    public function exportAttendanceCsv(Request $request): StreamedResponse
    {
        $user = $request->user();
        $selectedClass = $request->class_id;
        $startDate     = $request->start_date ?? now()->subDays(30)->toDateString();
        $endDate       = $request->end_date   ?? now()->toDateString();

        $query = Attendance::with(['student.user', 'student.schoolClass', 'student.section'])
            ->whereBetween('attendance_date', [$startDate, $endDate]);

        // If student, restrict to their own student record
        if ($user->isStudent()) {
            $student = $user->student;
            if (!$student) {
                abort(403, 'No student profile found.');
            }
            $query->where('student_id', $student->id);
        } else {
            if ($selectedClass) {
                $query->whereHas('student', fn($q) => $q->where('school_class_id', $selectedClass));
            }
        }

        $records = $query->orderBy('attendance_date', 'desc')->get();
        $fileName = 'moon_college_attendance_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($records) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Date', 'Student Name', 'Roll Number', 'Class', 'Section', 'Status', 'Remarks']);

            foreach ($records as $r) {
                fputcsv($handle, [
                    $r->attendance_date,
                    $r->student->user->name ?? 'N/A',
                    $r->student->roll_number ?? 'N/A',
                    $r->student->schoolClass->name ?? 'N/A',
                    $r->student->section->name ?? 'N/A',
                    ucfirst($r->status),
                    $r->remarks ?? '',
                ]);
            }
            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    public function fees(Request $request)
    {
        $this->authorizeFinance();
        $classes      = SchoolClass::where('is_active', true)->get();
        $selectedClass = $request->class_id;
        $status       = $request->status ?? 'all';
        $startDate    = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate      = $request->end_date   ?? now()->toDateString();

        $query = FeePayment::with(['student.user', 'student.schoolClass', 'feeStructure'])
            ->whereBetween('payment_date', [$startDate, $endDate]);

        if ($selectedClass) {
            $query->whereHas('student', fn($q) => $q->where('school_class_id', $selectedClass));
        }
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $payments     = $query->latest('payment_date')->get();
        $totalAmount  = $payments->where('status', 'paid')->sum('amount_paid');
        $pendingCount = $payments->where('status', 'pending')->count();

        return view('reports.fees', compact(
            'classes', 'selectedClass', 'status', 'startDate', 'endDate',
            'payments', 'totalAmount', 'pendingCount'
        ));
    }

    public function exportFees(Request $request): StreamedResponse
    {
        $this->authorizeFinance();
        $selectedClass = $request->class_id;
        $status        = $request->status ?? 'all';
        $startDate     = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate       = $request->end_date   ?? now()->toDateString();

        $query = FeePayment::with(['student.user', 'student.schoolClass', 'feeStructure'])
            ->whereBetween('payment_date', [$startDate, $endDate]);

        if ($selectedClass) {
            $query->whereHas('student', fn($q) => $q->where('school_class_id', $selectedClass));
        }
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $payments = $query->latest('payment_date')->get();

        $fileName = 'moon_college_fees_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($payments) {
            $handle = fopen('php://output', 'w');
            // BOM for UTF-8 compatibility
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Receipt Number', 'Student Name', 'Class', 'Fee Type', 'Amount Paid', 'Discount', 'Date', 'Method', 'Status']);

            foreach ($payments as $p) {
                fputcsv($handle, [
                    $p->receipt_number,
                    $p->student->user->name ?? 'N/A',
                    $p->student->schoolClass->name ?? 'N/A',
                    $p->feeStructure->fee_type ?? 'Tuition Fee',
                    $p->amount_paid,
                    $p->discount,
                    $p->payment_date,
                    ucfirst(str_replace('_', ' ', $p->payment_method)),
                    ucfirst($p->status),
                ]);
            }
            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    /**
     * CSV export with authorization: students can only export their own fee payments.
     */
    public function exportFeesCsv(Request $request): StreamedResponse
    {
        $user = $request->user();
        $selectedClass = $request->class_id;
        $status        = $request->status ?? 'all';
        $startDate     = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate       = $request->end_date   ?? now()->toDateString();

        $query = FeePayment::with(['student.user', 'student.schoolClass', 'feeStructure'])
            ->whereBetween('payment_date', [$startDate, $endDate]);

        if ($user->isStudent()) {
            $student = $user->student;
            if (!$student) {
                abort(403, 'No student profile found.');
            }
            $query->where('student_id', $student->id);
        } else {
            if ($selectedClass) {
                $query->whereHas('student', fn($q) => $q->where('school_class_id', $selectedClass));
            }
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        $payments = $query->latest('payment_date')->get();

        $fileName = 'moon_college_fees_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($payments) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Receipt Number', 'Student Name', 'Class', 'Fee Type', 'Amount Paid', 'Discount', 'Date', 'Method', 'Status']);

            foreach ($payments as $p) {
                fputcsv($handle, [
                    $p->receipt_number,
                    $p->student->user->name ?? 'N/A',
                    $p->student->schoolClass->name ?? 'N/A',
                    $p->feeStructure->fee_type ?? 'Tuition Fee',
                    $p->amount_paid,
                    $p->discount,
                    $p->payment_date,
                    ucfirst(str_replace('_', ' ', $p->payment_method)),
                    ucfirst($p->status),
                ]);
            }
            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    public function academic(Request $request)
    {
        $classes      = SchoolClass::where('is_active', true)->get();
        $selectedClass = $request->class_id;
        $exams        = [];
        $results      = [];

        if ($selectedClass) {
            $exams = Exam::where('school_class_id', $selectedClass)
                ->where('status', 'completed')
                ->with('subject')
                ->get();

            $results = Student::where('school_class_id', $selectedClass)
                ->with(['user', 'examMarks.exam.subject'])
                ->get()
                ->map(function ($student) use ($exams) {
                    $totalObtained = 0;
                    $totalMax      = 0;
                    $marksByExam   = [];

                    foreach ($exams as $exam) {
                        $mark = $student->examMarks->firstWhere('exam_id', $exam->id);
                        $obtained      = $mark?->marks_obtained ?? '-';
                        $marksByExam[] = $obtained;
                        if ($mark && !$mark->is_absent) {
                            $totalObtained += $obtained;
                            $totalMax      += $exam->total_marks;
                        }
                    }

                    return [
                        'student'    => $student,
                        'marks'      => $marksByExam,
                        'total_pct'  => $totalMax > 0
                            ? round(($totalObtained / $totalMax) * 100, 1)
                            : 0,
                        'pass'       => $totalMax > 0 && ($totalObtained / $totalMax) >= 0.4,
                    ];
                });
        }

        return view('reports.academic', compact('classes', 'selectedClass', 'exams', 'results'));
    }
}
