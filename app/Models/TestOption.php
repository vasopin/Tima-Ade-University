<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestOption extends Model
{
    protected $fillable = [
        'test_question_id', 'option_text', 'is_correct', 'order_index',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'order_index' => 'integer',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(TestQuestion::class, 'test_question_id');
    }
}
