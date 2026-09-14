<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeePayment extends Model
{
    protected $fillable = [
        'student_id', 'fee_structure_id', 'invoice_id', 'receipt_number', 'transaction_reference', 'idempotency_key', 'amount_paid',
        'discount', 'late_fee', 'payment_date', 'payment_method', 'status',
        'remarks', 'received_by', 'reconciled_at', 'reconciled_by',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount_paid'  => 'decimal:2',
            'discount'     => 'decimal:2',
            'late_fee'     => 'decimal:2',
            'reconciled_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function feeStructure(): BelongsTo
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }
    public function reconciledBy(): BelongsTo { return $this->belongsTo(User::class, 'reconciled_by'); }
    public function refunds(): HasMany { return $this->hasMany(Refund::class); }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'paid'    => '<span class="badge bg-success">Paid</span>',
            'partial' => '<span class="badge bg-warning text-dark">Partial</span>',
            'pending' => '<span class="badge bg-secondary">Pending</span>',
            'overdue' => '<span class="badge bg-danger">Overdue</span>',
            default   => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    public function getTotalDueAttribute(): float
    {
        return (float) $this->feeStructure?->amount + (float) $this->late_fee - (float) $this->discount;
    }
}
