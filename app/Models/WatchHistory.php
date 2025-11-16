<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WatchHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'episode_id',
        'progress_seconds',
        'is_completed',
        'watched_at',
    ];

    protected $casts = [
        'progress_seconds' => 'integer',
        'is_completed' => 'boolean',
        'watched_at' => 'datetime',
    ];

    /**
     * Get the user that owns this watch history
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the episode that belongs to this watch history
     */
    public function episode()
    {
        return $this->belongsTo(Episode::class);
    }

    /**
     * Scope for completed episodes
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    /**
     * Scope for in-progress episodes
     */
    public function scopeInProgress($query)
    {
        return $query->where('is_completed', false);
    }

    /**
     * Scope for recent watches
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('watched_at', '>=', now()->subDays($days));
    }
}
