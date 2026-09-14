<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LeaveRequestController extends Controller
{
    private function authorizeHR(): void
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isHROfficer()), 403, 'HR access required.');
    }

    public function index(Request $request)
    {
        $this->authorizeHR();

        $query = LeaveRequest::with(['employee.user', 'leaveType', 'approvedBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $leaveRequests = $query->latest()->paginate(15)->withQueryString();
        $leaveTypes = LeaveType::where('is_active', true)->orderBy('name')->get();

        return view('hr.leave-requests.index', compact('leaveRequests', 'leaveTypes'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $employee = $user->employee;
        abort_unless($employee, 403, 'Only linked employee accounts may submit leave requests.');

        $validated = $request->validate([
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'from_date'     => ['required', 'date', 'after_or_equal:today'],
            'to_date'       => ['required', 'date', 'after_or_equal:from_date'],
            'reason'        => ['nullable', 'string', 'max:2000'],
        ]);

        $days = Carbon::parse($validated['from_date'])->diffInDays(Carbon::parse($validated['to_date'])) + 1;

        LeaveRequest::create([
            'employee_id'    => $employee->id,
            'leave_type_id'  => $validated['leave_type_id'],
            'from_date'      => $validated['from_date'],
            'to_date'        => $validated['to_date'],
            'number_of_days' => $days,
            'reason'         => $validated['reason'] ?? null,
            'status'         => 'pending',
        ]);

        return back()->with('success', 'Leave request submitted and awaiting HR approval.');
    }

    public function approve(Request $request, LeaveRequest $leaveRequest)
    {
        $this->authorizeHR();

        abort_unless($leaveRequest->status === 'pending', 422, 'Only pending leave requests can be approved.');

        $leaveRequest->update([
            'status'         => 'approved',
            'approved_by'    => auth()->id(),
            'approved_at'    => now(),
            'approval_notes' => $request->input('approval_notes'),
        ]);

        return back()->with('success', 'Leave request approved.');
    }

    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        $this->authorizeHR();

        abort_unless($leaveRequest->status === 'pending', 422, 'Only pending leave requests can be rejected.');

        $validated = $request->validate([
            'approval_notes' => ['required', 'string', 'max:2000'],
        ]);

        $leaveRequest->update([
            'status'         => 'rejected',
            'approved_by'    => auth()->id(),
            'approved_at'    => now(),
            'approval_notes' => $validated['approval_notes'],
        ]);

        return back()->with('success', 'Leave request rejected.');
    }

    public function mine()
    {
        $user = auth()->user();
        $employee = $user->employee;

        $leaveRequests = $employee
            ? LeaveRequest::where('employee_id', $employee->id)->with('leaveType')->latest()->get()
            : collect();
        $leaveTypes = LeaveType::where('is_active', true)->orderBy('name')->get();

        return view('hr.leave-requests.mine', compact('leaveRequests', 'leaveTypes'));
    }
}
