<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestAttemptQuestion extends Model
{
    protected $fillable = [
        'test_attempt_id', 'test_question_id', 'question_text', 'question_type',
        'marks', 'order_index', 'options', 'correct_answer',
    ];

    protected $casts = ['options' => 'array', 'marks' => 'integer', 'order_index' => 'integer'];

    public function attempt(): BelongsTo { return $this->belongsTo(TestAttempt::class, 'test_attempt_id'); }
    public function question(): BelongsTo { return $this->belongsTo(TestQuestion::class, 'test_question_id'); }
}
