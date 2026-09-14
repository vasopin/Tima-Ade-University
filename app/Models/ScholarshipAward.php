<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScholarshipAward extends Model
{
    protected $fillable = ['scholarship_id', 'student_id', 'academic_year_id', 'term_id', 'amount', 'percentage', 'status', 'approved_by', 'starts_on', 'ends_on'];
    protected function casts(): array { return ['amount' => 'decimal:2', 'percentage' => 'decimal:2', 'starts_on' => 'date', 'ends_on' => 'date']; }
    public function scholarship(): BelongsTo { return $this->belongsTo(Scholarship::class); }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function approvedBy(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
}
