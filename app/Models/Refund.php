<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    protected $fillable = ['fee_payment_id', 'amount', 'reason', 'status', 'requested_by', 'approved_by', 'processed_at'];
    protected function casts(): array { return ['amount' => 'decimal:2', 'processed_at' => 'datetime']; }
    public function payment(): BelongsTo { return $this->belongsTo(FeePayment::class, 'fee_payment_id'); }
    public function requestedBy(): BelongsTo { return $this->belongsTo(User::class, 'requested_by'); }
    public function approvedBy(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
}
