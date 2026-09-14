<?php

namespace App\Services;

use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BillingService
{
    public function createInvoice(Student $student, FeeStructure $structure, ?int $academicYearId = null, ?int $termId = null): Invoice
    {
        return DB::transaction(function () use ($student, $structure, $academicYearId, $termId): Invoice {
            $existing = Invoice::where('student_id', $student->id)
                ->whereHas('items', fn ($query) => $query->where('fee_structure_id', $structure->id))
                ->whereIn('status', ['issued', 'partially_paid', 'overdue'])
                ->first();
            if ($existing) {
                return $existing->load('items');
            }

            $amount = (float) $structure->amount;
            $invoice = Invoice::create([
                'student_id' => $student->id,
                'academic_year_id' => $academicYearId,
                'term_id' => $termId,
                'invoice_number' => 'INV-'.strtoupper(Str::random(10)),
                'issue_date' => now()->toDateString(),
                'due_date' => $structure->due_date,
                'status' => 'issued',
                'subtotal' => $amount,
                'total' => $amount,
                'balance' => $amount,
            ]);
            $invoice->items()->create([
                'fee_structure_id' => $structure->id,
                'fee_type' => $structure->fee_type,
                'description' => $structure->fee_type,
                'quantity' => 1,
                'unit_amount' => $amount,
                'total_amount' => $amount,
            ]);
            return $invoice->load('items');
        });
    }

    public function recordPayment(array $data, int $actorId): FeePayment
    {
        return DB::transaction(function () use ($data, $actorId): FeePayment {
            if (!empty($data['idempotency_key'])) {
                $existing = FeePayment::where('idempotency_key', $data['idempotency_key'])->first();
                if ($existing) {
                    return $existing;
                }
            }
            $invoice = !empty($data['invoice_id']) ? Invoice::lockForUpdate()->findOrFail($data['invoice_id']) : null;
            $amount = (float) $data['amount_paid'];
            if ($invoice && $amount > (float) $invoice->balance) {
                throw ValidationException::withMessages(['amount_paid' => 'Payment cannot exceed the invoice balance.']);
            }
            $payment = FeePayment::create([
                'student_id' => $data['student_id'],
                'fee_structure_id' => $data['fee_structure_id'],
                'invoice_id' => $data['invoice_id'] ?? null,
                'receipt_number' => 'RCPT-'.strtoupper(Str::random(8)),
                'transaction_reference' => $data['transaction_reference'] ?? null,
                'idempotency_key' => $data['idempotency_key'] ?? null,
                'amount_paid' => $amount,
                'discount' => $data['discount'] ?? 0,
                'late_fee' => $data['late_fee'] ?? 0,
                'payment_date' => $data['payment_date'],
                'payment_method' => $data['payment_method'],
                'status' => $data['status'],
                'remarks' => $data['remarks'] ?? null,
                'received_by' => $actorId,
            ]);
            if ($invoice) {
                $paid = (float) $invoice->amount_paid + $amount;
                $balance = max(0, (float) $invoice->total - $paid);
                $invoice->update(['amount_paid' => $paid, 'balance' => $balance, 'status' => $balance <= 0 ? 'paid' : 'partially_paid']);
            }
            return $payment;
        });
    }
}
