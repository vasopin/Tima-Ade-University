<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestQuestionPool extends Model
{
    protected $fillable = [
        'test_id', 'teacher_id', 'school_class_id', 'subject_id',
        'question_type', 'questions_count', 'marks_per_question',
    ];

    protected $casts = [
        'questions_count' => 'integer',
        'marks_per_question' => 'integer',
    ];

    public function test(): BelongsTo { return $this->belongsTo(Test::class); }
    public function teacher(): BelongsTo { return $this->belongsTo(User::class, 'teacher_id'); }
    public function schoolClass(): BelongsTo { return $this->belongsTo(SchoolClass::class); }
    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
}
