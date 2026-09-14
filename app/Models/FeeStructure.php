<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeStructure extends Model
{
    protected $fillable = [
        'school_class_id', 'fee_type', 'amount', 'academic_year', 'term', 'due_date', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'due_date'  => 'date',
            'is_active' => 'boolean',
            'amount'    => 'decimal:2',
        ];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(FeePayment::class);
    }

    public function invoiceItems(): HasMany { return $this->hasMany(InvoiceItem::class); }
}
