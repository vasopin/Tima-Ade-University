<?php

namespace App\Http\Controllers;

use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\PayrollRecord;
use App\Models\Scholarship;
use App\Models\Invoice;
use App\Models\Refund;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    private function authorizeFinance(): void
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isFinanceOfficer()), 403, 'Finance access required.');
    }

    public function index()
    {
        $this->authorizeFinance();

        $stats = [
            'collected' => FeePayment::where('status', 'paid')->sum('amount_paid'),
            'outstanding' => FeePayment::with('feeStructure')
                ->whereIn('status', ['pending', 'partial', 'overdue'])
                ->get()
                ->sum(fn (FeePayment $payment) => $payment->total_due),
            'structures' => FeeStructure::where('is_active', true)->count(),
            'payroll_pending' => PayrollRecord::where('status', 'pending')->count(),
            'total_billed' => Invoice::sum('total'),
            'outstanding_balance' => Invoice::sum('balance'),
            'pending_payments' => FeePayment::where('status', 'pending')->count(),
            'refunds' => Refund::whereIn('status', ['requested', 'approved', 'processed'])->sum('amount'),
            'reconciliation_exceptions' => FeePayment::whereNull('reconciled_at')->whereIn('status', ['paid', 'partial'])->count(),
        ];

        $recentPayments = FeePayment::with(['student.user', 'feeStructure'])
            ->latest('payment_date')->take(8)->get();
        $scholarships = Scholarship::where('is_active', true)->orderBy('title')->take(6)->get();

        return view('finance.index', compact('stats', 'recentPayments', 'scholarships'));
    }

    public function budget()
    {
        $this->authorizeFinance();
        return view('finance.budget');
    }

    public function refunds()
    {
        $this->authorizeFinance();
        $refunds = Refund::with(['payment.student.user', 'requestedBy', 'approvedBy'])->latest()->paginate(25);
        return view('finance.refunds', compact('refunds'));
    }

    public function requestRefund(Request $request, FeePayment $payment)
    {
        $this->authorizeFinance();
        $data = $request->validate(['amount' => ['required', 'numeric', 'min:0.01'], 'reason' => ['required', 'string', 'max:1000']]);
        $refunded = $payment->refunds()->whereIn('status', ['requested', 'approved', 'processed'])->sum('amount');
        abort_if($refunded + $data['amount'] > (float) $payment->amount_paid, 422, 'Refund exceeds the refundable payment amount.');
        Refund::create(['fee_payment_id' => $payment->id, 'amount' => $data['amount'], 'reason' => $data['reason'], 'status' => 'requested', 'requested_by' => auth()->id()]);
        return back()->with('success', 'Refund request submitted.');
    }

    public function approveRefund(Refund $refund)
    {
        $this->authorizeFinance();
        abort_if($refund->status !== 'requested', 422, 'Only requested refunds may be approved.');
        DB::transaction(function () use ($refund): void {
            $refund->update(['status' => 'processed', 'approved_by' => auth()->id(), 'processed_at' => now()]);
            $payment = $refund->payment()->lockForUpdate()->firstOrFail();
            if ($payment->invoice_id) {
                $invoice = $payment->invoice()->lockForUpdate()->first();
                if ($invoice) {
                    $invoice->update(['amount_paid' => max(0, (float) $invoice->amount_paid - (float) $refund->amount), 'balance' => min((float) $invoice->total, (float) $invoice->balance + (float) $refund->amount), 'status' => 'partially_paid']);
                }
            }
        });
        return back()->with('success', 'Refund processed.');
    }

    public function reconcilePayment(FeePayment $payment)
    {
        $this->authorizeFinance();
        $payment->update(['reconciled_at' => now(), 'reconciled_by' => auth()->id()]);
        return back()->with('success', 'Payment reconciled.');
    }

    public function audit(Request $request)
    {
        $this->authorizeFinance();

        $payments = FeePayment::with(['student.user', 'receivedBy'])
            ->latest()->paginate(20, ['*'], 'payments_page');

        return view('finance.audit', compact('payments'));
    }
}
