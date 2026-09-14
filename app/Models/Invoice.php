<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = ['student_id', 'academic_year_id', 'term_id', 'invoice_number', 'issue_date', 'due_date', 'status', 'subtotal', 'discounts', 'scholarship_amount', 'total', 'amount_paid', 'balance'];
    protected function casts(): array { return ['issue_date' => 'date', 'due_date' => 'date', 'subtotal' => 'decimal:2', 'discounts' => 'decimal:2', 'scholarship_amount' => 'decimal:2', 'total' => 'decimal:2', 'amount_paid' => 'decimal:2', 'balance' => 'decimal:2']; }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function academicYear(): BelongsTo { return $this->belongsTo(AcademicYear::class); }
    public function term(): BelongsTo { return $this->belongsTo(Term::class); }
    public function items(): HasMany { return $this->hasMany(InvoiceItem::class); }
    public function payments(): HasMany { return $this->hasMany(FeePayment::class); }
}
