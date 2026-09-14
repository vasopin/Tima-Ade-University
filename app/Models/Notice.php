<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $fillable = [
        'title', 'content', 'category', 'published_date', 'expires_at', 'is_pinned', 'is_active',
        'status', 'audience', 'video_path', 'video_original_name', 'video_mime_type',
        'video_file_size', 'thumbnail_path', 'attachment_path', 'external_video_url',
    ];

    protected function casts(): array
    {
        return [
            'published_date' => 'date',
            'expires_at'     => 'date',
            'is_pinned'      => 'boolean',
            'is_active'      => 'boolean',
            'video_file_size'=> 'integer',
        ];
    }

    public function getVideoUrlAttribute(): ?string
    {
        return $this->video_path ? asset('storage/' . $this->video_path) : $this->external_video_url;
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail_path ? asset('storage/' . $this->thumbnail_path) : null;
    }
}
