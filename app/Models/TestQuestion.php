<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestQuestion extends Model
{
    protected $fillable = [
        'test_id', 'teacher_id', 'school_class_id', 'subject_id', 'question_text', 'question_type',
        'marks', 'order_index', 'correct_answer',
    ];

    protected $casts = [
        'marks' => 'integer',
        'order_index' => 'integer',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function teacher(): BelongsTo { return $this->belongsTo(User::class, 'teacher_id'); }
    public function schoolClass(): BelongsTo { return $this->belongsTo(SchoolClass::class); }
    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }

    public function options(): HasMany
    {
        return $this->hasMany(TestOption::class)->orderBy('order_index');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(TestAnswer::class);
    }

    /**
     * Check if this is an automatically gradable question type
     */
    public function isAutoGradeable(): bool
    {
        return in_array($this->question_type, ['multiple_choice', 'true_false']);
    }
}
