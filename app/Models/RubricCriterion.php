<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RubricCriterion extends Model
{
    protected $fillable = ['rubric_id', 'name', 'description', 'max_points', 'sort_order'];
    protected function casts(): array { return ['max_points' => 'decimal:2']; }
    public function rubric(): BelongsTo { return $this->belongsTo(Rubric::class); }
    public function scores(): HasMany { return $this->hasMany(RubricScore::class); }
}
