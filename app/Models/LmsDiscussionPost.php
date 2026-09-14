<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LmsDiscussionPost extends Model
{
    protected $table = 'lms_discussion_posts';
    protected $fillable = ['discussion_id', 'author_id', 'parent_id', 'body', 'status', 'edited_by', 'edited_at'];

    protected function casts(): array { return ['edited_at' => 'datetime']; }
    public function discussion(): BelongsTo { return $this->belongsTo(LmsDiscussion::class, 'discussion_id'); }
    public function author(): BelongsTo { return $this->belongsTo(User::class, 'author_id'); }
    public function parent(): BelongsTo { return $this->belongsTo(self::class, 'parent_id'); }
    public function replies(): HasMany { return $this->hasMany(self::class, 'parent_id'); }
}
