<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentStatusHistory extends Model
{
    protected $fillable = ['student_id', 'from_status', 'to_status', 'effective_date', 'reason', 'notes', 'actor_id'];
    protected function casts(): array { return ['effective_date' => 'date']; }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function actor(): BelongsTo { return $this->belongsTo(User::class, 'actor_id'); }
}
