<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProgramHistory extends Model
{
    protected $fillable = ['student_id', 'from_program_id', 'to_program_id', 'effective_date', 'reason', 'actor_id'];
    protected function casts(): array { return ['effective_date' => 'date']; }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function fromProgram(): BelongsTo { return $this->belongsTo(Program::class, 'from_program_id'); }
    public function toProgram(): BelongsTo { return $this->belongsTo(Program::class, 'to_program_id'); }
    public function actor(): BelongsTo { return $this->belongsTo(User::class, 'actor_id'); }
}
