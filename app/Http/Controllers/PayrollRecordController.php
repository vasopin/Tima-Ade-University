<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\PayrollRecord;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PayrollRecordController extends Controller
{
    private function authorizeHR(): void
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isHROfficer() || $user->isFinanceOfficer()), 403, 'HR access required.');
    }

    public function index(Request $request)
    {
        $this->authorizeHR();

        $query = PayrollRecord::with(['employee.user', 'payrollPeriod', 'processedBy']);

        if ($request->filled('payroll_period_id')) {
            $query->where('payroll_period_id', $request->payroll_period_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $records = $query->latest()->paginate(15)->withQueryString();
        $periods = PayrollPeriod::orderByDesc('start_date')->get();
        $allEmployees = Employee::with('user')->orderBy('employee_id')->get();

        return view('hr.payroll-records.index', compact('records', 'periods', 'allEmployees'));
    }

    /**
     * Generate a payroll record from the employee's real base_salary,
     * minus any deductions supplied by HR (statutory/loan/other — never fabricated).
     */
    public function store(Request $request)
    {
        $this->authorizeHR();

        $validated = $request->validate([
            'employee_id'       => ['required', 'exists:employees,id'],
            'payroll_period_id' => [
                'required',
                'exists:payroll_periods,id',
                Rule::unique('payroll_records', 'payroll_period_id')->where('employee_id', $request->input('employee_id')),
            ],
            'total_deductions'  => ['nullable', 'numeric', 'min:0'],
            'notes'             => ['nullable', 'string', 'max:2000'],
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        $period = PayrollPeriod::findOrFail($validated['payroll_period_id']);
        abort_if($period->is_closed, 422, 'Payroll records cannot be created for a closed period.');
        abort_if(is_null($employee->base_salary), 422, "Employee '{$employee->employee_id}' has no base salary on record; payroll cannot be computed.");

        $gross = (float) $employee->base_salary;
        $deductions = (float) ($validated['total_deductions'] ?? 0);
        $net = max(0, $gross - $deductions);

        PayrollRecord::create([
            'employee_id'       => $employee->id,
            'payroll_period_id' => $validated['payroll_period_id'],
            'gross_salary'      => $gross,
            'total_deductions'  => $deductions,
            'net_salary'        => $net,
            'status'            => 'pending',
            'notes'             => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Payroll record generated from the employee\'s real base salary.');
    }

    public function process(PayrollRecord $payrollRecord)
    {
        $this->authorizeHR();

        abort_if($payrollRecord->payrollPeriod?->is_closed, 422, 'Closed payroll periods cannot be modified.');

        abort_unless($payrollRecord->status === 'pending', 422, 'Only pending payroll records can be processed.');

        $payrollRecord->update([
            'status'       => 'processed',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Payroll record processed.');
    }

    public function markPaid(PayrollRecord $payrollRecord)
    {
        $this->authorizeHR();

        abort_if($payrollRecord->payrollPeriod?->is_closed, 422, 'Closed payroll periods cannot be modified.');

        abort_unless($payrollRecord->status === 'processed', 422, 'Only processed payroll records can be marked as paid.');

        $payrollRecord->update(['status' => 'paid']);

        return back()->with('success', 'Payroll record marked as paid.');
    }
}
