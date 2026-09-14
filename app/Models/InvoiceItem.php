<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = ['invoice_id', 'fee_structure_id', 'fee_type', 'description', 'quantity', 'unit_amount', 'total_amount'];
    protected function casts(): array { return ['quantity' => 'decimal:2', 'unit_amount' => 'decimal:2', 'total_amount' => 'decimal:2']; }
    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }
    public function feeStructure(): BelongsTo { return $this->belongsTo(FeeStructure::class); }
}
