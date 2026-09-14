<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Notice;
use App\Models\PayrollRecord;
use App\Models\StaffAttendanceLog;
use App\Models\StaffWorkItem;
use Illuminate\Http\Request;

class StaffWorkspaceController extends Controller
{
    private function staff(): void
    {
        abort_unless(auth()->user()?->isStaff(), 403, 'Staff access required.');
    }

    public function communications()
    {
        $this->staff();
        $notices = Notice::where('is_active', true)->where('status', 'published')
            ->whereDate('published_date', '<=', today())
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhereDate('expires_at', '>=', today()))
            ->latest('published_date')->paginate(15);

        return view('staff.workspace', ['section' => 'communications', 'notices' => $notices]);
    }

    public function time()
    {
        $this->staff();
        $user = auth()->user();
        $today = StaffAttendanceLog::where('user_id', $user->id)->whereDate('attendance_date', today())->first();
        $logs = StaffAttendanceLog::where('user_id', $user->id)->latest('attendance_date')->paginate(15);
        $employee = $user->employee;
        $leaveRequests = $employee ? $employee->leaveRequests()->with('leaveType')->latest()->get() : collect();
        $leaveTypes = LeaveType::where('is_active', true)->orderBy('name')->get();

        return view('staff.workspace', compact('today', 'logs', 'employee', 'leaveRequests', 'leaveTypes') + ['section' => 'time']);
    }

    public function clockIn()
    {
        $this->staff();
        StaffAttendanceLog::firstOrCreate(
            ['user_id' => auth()->id(), 'attendance_date' => today()],
            ['checked_in_at' => now()]
        );
        return back()->with('success', 'Check-in recorded.');
    }

    public function clockOut()
    {
        $this->staff();
        $log = StaffAttendanceLog::where('user_id', auth()->id())->whereDate('attendance_date', today())->firstOrFail();
        abort_if(!$log->checked_in_at, 422, 'Check in before checking out.');
        $log->update(['checked_out_at' => now()]);
        return back()->with('success', 'Check-out recorded.');
    }

    public function workItems(string $type)
    {
        $this->staff();
        abort_unless(in_array($type, ['support', 'maintenance'], true), 404);
        $items = StaffWorkItem::where('user_id', auth()->id())->where('type', $type)->latest()->paginate(15);
        return view('staff.workspace', compact('items', 'type') + ['section' => $type]);
    }

    public function storeWorkItem(Request $request, string $type)
    {
        $this->staff();
        abort_unless(in_array($type, ['support', 'maintenance'], true), 404);
        $data = $request->validate(['subject' => ['required', 'string', 'max:255'], 'description' => ['required', 'string', 'max:4000']]);
        StaffWorkItem::create($data + ['user_id' => auth()->id(), 'type' => $type, 'status' => 'open']);
        return back()->with('success', ucfirst($type) . ' request submitted.');
    }

    public function hr()
    {
        $this->staff();
        $employee = auth()->user()->employee;
        $payslips = $employee ? $employee->payrollRecords()->with('payrollPeriod')->where('status', 'paid')->latest()->get() : collect();
        $leaveRequests = $employee ? $employee->leaveRequests()->with('leaveType')->latest()->get() : collect();
        return view('staff.workspace', compact('employee', 'payslips', 'leaveRequests') + ['section' => 'hr']);
    }

    public function downloadPayslip(PayrollRecord $payrollRecord)
    {
        $this->staff();
        $employee = auth()->user()->employee;
        abort_unless($employee && $payrollRecord->employee_id === $employee->id && $payrollRecord->status === 'paid', 404);

        return response()->streamDownload(function () use ($payrollRecord) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Payroll period', 'Gross salary', 'Deductions', 'Net salary', 'Status']);
            fputcsv($output, [
                $payrollRecord->payrollPeriod?->name ?? $payrollRecord->payrollPeriod?->start_date?->format('M Y'),
                $payrollRecord->gross_salary,
                $payrollRecord->total_deductions,
                $payrollRecord->net_salary,
                $payrollRecord->status,
            ]);
            fclose($output);
        }, 'payslip-' . $payrollRecord->id . '.csv', ['Content-Type' => 'text/csv']);
    }
}