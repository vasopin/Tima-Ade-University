<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamMarkHistory extends Model
{
    protected $table = 'exam_mark_history';
    protected $fillable = ['exam_mark_id', 'marks_obtained', 'grade', 'is_absent', 'reason', 'changed_by'];
    protected function casts(): array { return ['marks_obtained' => 'decimal:2', 'is_absent' => 'boolean']; }
    public function mark(): BelongsTo { return $this->belongsTo(ExamMark::class, 'exam_mark_id'); }
    public function changedBy(): BelongsTo { return $this->belongsTo(User::class, 'changed_by'); }
}
