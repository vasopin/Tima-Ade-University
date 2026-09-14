<?php

namespace App\Http\Controllers;

use App\Models\PayrollPeriod;
use Illuminate\Http\Request;

class PayrollPeriodController extends Controller
{
    private function authorizeHR(): void
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isHROfficer() || $user->isFinanceOfficer()), 403, 'HR access required.');
    }

    public function index()
    {
        $this->authorizeHR();

        $periods = PayrollPeriod::withCount('payrollRecords')->latest('start_date')->paginate(15);

        return view('hr.payroll-periods.index', compact('periods'));
    }

    public function store(Request $request)
    {
        $this->authorizeHR();

        $validated = $request->validate([
            'period_name' => ['required', 'string', 'max:255', 'unique:payroll_periods,period_name'],
            'start_date'  => ['required', 'date'],
            'end_date'    => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $validated['is_active'] = true;
        $validated['is_closed'] = false;

        PayrollPeriod::where('is_active', true)->update(['is_active' => false]);
        PayrollPeriod::create($validated);

        return back()->with('success', 'Payroll period created successfully.');
    }

    public function close(PayrollPeriod $payrollPeriod)
    {
        $this->authorizeHR();

        $payrollPeriod->update(['is_closed' => true, 'is_active' => false]);

        return back()->with('success', "Payroll period '{$payrollPeriod->period_name}' has been closed.");
    }
}
