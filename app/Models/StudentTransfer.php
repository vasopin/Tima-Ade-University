<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentTransfer extends Model
{
    protected $fillable = ['student_id', 'from_program_id', 'to_program_id', 'type', 'status', 'effective_date', 'reason', 'requested_by', 'reviewed_by', 'reviewed_at', 'notes'];
    protected function casts(): array { return ['effective_date' => 'date', 'reviewed_at' => 'datetime']; }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function fromProgram(): BelongsTo { return $this->belongsTo(Program::class, 'from_program_id'); }
    public function toProgram(): BelongsTo { return $this->belongsTo(Program::class, 'to_program_id'); }
    public function requestedBy(): BelongsTo { return $this->belongsTo(User::class, 'requested_by'); }
    public function reviewedBy(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }
}
