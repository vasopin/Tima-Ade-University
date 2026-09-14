<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentHold extends Model
{
    protected $fillable = ['student_id', 'type', 'reason', 'status', 'effective_date', 'release_date', 'created_by', 'released_by', 'notes', 'related_type', 'related_id'];
    protected function casts(): array { return ['effective_date' => 'date', 'release_date' => 'date']; }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function releasedBy(): BelongsTo { return $this->belongsTo(User::class, 'released_by'); }
}
