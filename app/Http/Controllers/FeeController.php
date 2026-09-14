<?php

namespace App\Http\Controllers;

use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Services\BillingService;

class FeeController extends Controller
{
    public function __construct(private BillingService $billing)
    {
        // Admin and finance officers may manage fee structures and payments.
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->isFinanceOfficer())) {
                abort(403, 'Unauthorized. Finance privileges required.');
            }
            return $next($request);
        })->only(['storeStructure', 'create', 'store', 'edit', 'update', 'destroy']);
    }

    public function createInvoice(Request $request)
    {
        $this->authorizeFinanceManagement();
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'fee_structure_id' => ['required', 'exists:fee_structures,id'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'term_id' => ['nullable', 'exists:terms,id'],
        ]);
        $invoice = $this->billing->createInvoice(Student::findOrFail($data['student_id']), FeeStructure::findOrFail($data['fee_structure_id']), $data['academic_year_id'] ?? null, $data['term_id'] ?? null);
        return redirect()->route('fees.show-invoice', $invoice)->with('success', 'Invoice issued.');
    }

    public function showInvoice(\App\Models\Invoice $invoice)
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isFinanceOfficer() || ($user->isStudent() && $user->student?->id === $invoice->student_id)), 403, 'Finance access required.');
        $invoice->load(['student.user', 'items', 'payments']);
        return view('fees.invoice', compact('invoice'));
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        abort_unless($user->isAdmin() || $user->isFinanceOfficer() || $user->isStudent() || $user->isParent(), 403, 'Finance access required.');

        // If student or parent, redirect to their specific fee view
        if ($user->isStudent()) {
            if ($user->student) {
                return redirect()->route('fees.student', $user->student);
            }
        }

        $query = FeePayment::with(['student.user', 'feeStructure.schoolClass']);

        if ($user->isParent()) {
            $studentIds = $user->guardian?->students?->pluck('id') ?? collect();
            $query->whereIn('student_id', $studentIds);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('receipt_number', 'like', "%$search%")
                ->orWhereHas('student.user', fn($q) => $q->where('name', 'like', "%$search%"));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->latest()->paginate(15)->withQueryString();

        $summary = [
            'total_collected' => FeePayment::where('status', 'paid')->sum('amount_paid'),
            'total_pending'   => FeePayment::where('status', 'pending')->count(),
            'total_overdue'   => FeePayment::where('status', 'overdue')->count(),
        ];

        return view('fees.index', compact('payments', 'summary'));
    }

    public function structures()
    {
        $this->authorizeFinanceManagement();
        $structures = FeeStructure::with('schoolClass')->latest()->paginate(15);
        $classes    = SchoolClass::where('is_active', true)->get();
        return view('fees.structures', compact('structures', 'classes'));
    }

    public function storeStructure(Request $request)
    {
        $validated = $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'fee_type'        => 'required|string|max:100',
            'amount'          => 'required|numeric|min:0',
            'academic_year'   => 'required|string|max:20',
            'term'            => 'nullable|string|max:50',
            'due_date'        => 'nullable|date',
        ]);

        FeeStructure::create(array_merge($validated, ['is_active' => true]));
        return redirect()->route('fees.structures')
            ->with('success', 'Fee structure created successfully!');
    }

    public function create()
    {
        $students   = Student::with(['user', 'schoolClass'])->where('status', 'active')->get();
        $structures = FeeStructure::with('schoolClass')->where('is_active', true)->get();
        return view('fees.create', compact('students', 'structures'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'       => 'required|exists:students,id',
            'fee_structure_id' => 'required|exists:fee_structures,id',
            'amount_paid'      => 'required|numeric|min:0',
            'discount'         => 'nullable|numeric|min:0',
            'late_fee'         => 'nullable|numeric|min:0',
            'payment_date'     => 'required|date',
            'payment_method'   => 'required|in:cash,bank_transfer,cheque,online',
            'invoice_id'       => 'nullable|exists:invoices,id',
            'transaction_reference' => 'nullable|string|max:255',
            'idempotency_key'  => 'nullable|string|max:255',
            'remarks'          => 'nullable|string|max:500',
        ]);

        $feeStructure = FeeStructure::findOrFail($validated['fee_structure_id']);
        $discount     = $validated['discount'] ?? 0;
        $lateFee      = $validated['late_fee'] ?? 0;
        abort_if($discount > ($feeStructure->amount + $lateFee), 422, 'Discount cannot exceed the fee due.');
        $totalDue     = $feeStructure->amount + $lateFee - $discount;
        $amountPaid   = $validated['amount_paid'];

        $status = match (true) {
            $amountPaid >= $totalDue => 'paid',
            $amountPaid > 0          => 'partial',
            default                  => 'pending',
        };

        $paymentData = [
            'student_id'       => $validated['student_id'],
            'fee_structure_id' => $validated['fee_structure_id'],
            'receipt_number'   => 'RCPT-' . strtoupper(Str::random(8)),
            'amount_paid'      => $validated['amount_paid'],
            'discount'         => $discount,
            'late_fee'          => $lateFee,
            'payment_date'     => $validated['payment_date'],
            'payment_method'   => $validated['payment_method'],
            'status'           => $status,
            'remarks'          => $validated['remarks'] ?? null,
            'received_by'      => Auth::id(),
        ];
        if ($request->filled('invoice_id')) {
            $paymentData['invoice_id'] = $request->integer('invoice_id');
        }
        $paymentData['transaction_reference'] = $validated['transaction_reference'] ?? null;
        $paymentData['idempotency_key'] = $validated['idempotency_key'] ?? null;
        $payment = $this->billing->recordPayment($paymentData, Auth::id());

        return redirect()->route('fees.index')
            ->with('success', 'Fee payment recorded successfully!');
    }

    public function show(FeePayment $fee)
    {
        $user = auth()->user();

        abort_unless($user->isAdmin() || $user->isFinanceOfficer() || $user->isStudent() || $user->isParent(), 403, 'Finance access required.');

        // IDOR protection
        if ($user->isStudent() && $user->student?->id !== $fee->student_id) {
            abort(403, 'Unauthorized.');
        }

        if ($user->isParent()) {
            $guardian = $user->guardian;
            $allowedIds = $guardian ? $guardian->students->pluck('id')->toArray() : [];
            if (!in_array($fee->student_id, $allowedIds)) {
                abort(403, 'Unauthorized.');
            }
        }

        $fee->load(['student.user', 'feeStructure.schoolClass', 'receivedBy']);
        return view('fees.show', compact('fee'));
    }

    public function edit(FeePayment $fee)
    {
        $students   = Student::with(['user', 'schoolClass'])->where('status', 'active')->get();
        $structures = FeeStructure::with('schoolClass')->where('is_active', true)->get();
        return view('fees.edit', compact('fee', 'students', 'structures'));
    }

    public function update(Request $request, FeePayment $fee)
    {
        $validated = $request->validate([
            'amount_paid'    => 'required|numeric|min:0',
            'discount'       => 'nullable|numeric|min:0',
            'late_fee'       => 'nullable|numeric|min:0',
            'payment_date'   => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,cheque,online',
            // Do not trust client-supplied status; compute it on the server instead
            'remarks'        => 'nullable|string|max:500',
        ]);

        // Recalculate payment status using the existing fee structure amount
        $feeStructure = $fee->feeStructure()->first();
        $structureAmount = $feeStructure?->amount ?? 0;

        $discount = $validated['discount'] ?? $fee->discount ?? 0;
        $lateFee = $validated['late_fee'] ?? $fee->late_fee ?? 0;
        $amountPaid = $validated['amount_paid'];

        abort_if($discount > ($structureAmount + $lateFee), 422, 'Discount cannot exceed the fee due.');
        $totalDue = $structureAmount + $lateFee - $discount;

        $status = match (true) {
            $amountPaid >= $totalDue => 'paid',
            $amountPaid > 0          => 'partial',
            default                  => 'pending',
        };

        // Only allow updating permitted fields and server-computed status
        $updateData = array_merge($validated, [
            'discount'   => $discount,
            'late_fee'   => $lateFee,
            'status'     => $status,
        ]);

        $fee->update($updateData);

        return redirect()->route('fees.index')
            ->with('success', 'Fee payment details updated successfully.');
    }

    public function destroy(FeePayment $fee)
    {
        $this->authorizeFinanceManagement();
        abort(405, 'Financial records are immutable. Use the refund workflow for corrections.');
    }

    public function studentFees(Student $student)
    {
        $user = auth()->user();

        // IDOR check
        if ($user->isStudent() && $user->student?->id !== $student->id) {
            abort(403, 'Unauthorized.');
        }

        if ($user->isParent()) {
            $guardian = $user->guardian;
            $allowedIds = $guardian ? $guardian->students->pluck('id')->toArray() : [];
            if (!in_array($student->id, $allowedIds)) {
                abort(403, 'Unauthorized.');
            }
        }

        $student->load(['user', 'schoolClass', 'feePayments.feeStructure']);
        return view('fees.student', compact('student'));
    }

    private function authorizeFinanceManagement(): void
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isFinanceOfficer()), 403, 'Finance management access required.');
    }
}
