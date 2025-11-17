<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'dorama_id',
        'episode_id',
        'content',
        'likes_count'
    ];

    protected $casts = [
        'likes_count' => 'integer'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dorama(): BelongsTo
    {
        return $this->belongsTo(Dorama::class);
    }

    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }

    public function getTimeAgoAttribute(): string
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    public function scopeForDorama($query, $doramaId)
    {
        return $query->where('dorama_id', $doramaId);
    }

    public function scopeForEpisode($query, $episodeId = null)
    {
        if ($episodeId) {
            return $query->where('episode_id', $episodeId);
        }
        return $query->whereNull('episode_id');
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
