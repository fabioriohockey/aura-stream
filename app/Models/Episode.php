<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    use HasFactory;

    protected $fillable = [
        'dorama_id',
        'episode_number',
        'title',
        'description',
        'video_path_480p',
        'video_path_720p',
        'thumbnail_path',
        'subtitles_path',
        'duration_seconds',
        'file_size_480p_mb',
        'file_size_720p_mb',
        'video_format',
        'video_codec',
        'views_count',
        'is_premium_only',
        'is_active',
        'air_date',
    ];

    protected $casts = [
        'dorama_id' => 'integer',
        'episode_number' => 'integer',
        'duration_seconds' => 'integer',
        'file_size_480p_mb' => 'decimal:2',
        'file_size_720p_mb' => 'decimal:2',
        'views_count' => 'integer',
        'is_premium_only' => 'boolean',
        'is_active' => 'boolean',
        'air_date' => 'datetime',
    ];

    /**
     * Get the dorama that owns this episode
     */
    public function dorama()
    {
        return $this->belongsTo(Dorama::class);
    }

    /**
     * Scope para episódios ativos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para episódios gratuitos
     */
    public function scopeFree($query)
    {
        return $query->where('is_premium_only', false);
    }

    /**
     * Scope para episódios premium
     */
    public function scopePremium($query)
    {
        return $query->where('is_premium_only', true);
    }

    /**
     * Increment view count
     */
    public function incrementViews()
    {
        $this->increment('views_count');
        $this->dorama->incrementViews();
    }

    /**
     * Get video URL based on quality
     */
    public function getVideoUrlAttribute($quality = '480p')
    {
        $path = $this->{"video_path_{$quality}"};
        if ($path) {
            return asset('storage/' . $path);
        }
        return null;
    }

    /**
     * Get 480p video URL
     */
    public function getVideoUrl480pAttribute()
    {
        return $this->getVideoUrlAttribute('480p');
    }

    /**
     * Get 720p video URL
     */
    public function getVideoUrl720pAttribute()
    {
        return $this->getVideoUrlAttribute('720p');
    }

    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail_path) {
            return asset('storage/' . $this->thumbnail_path);
        }
        return null;
    }

    /**
     * Get subtitles URL
     */
    public function getSubtitlesUrlAttribute()
    {
        if ($this->subtitles_path) {
            return asset('storage/' . $this->subtitles_path);
        }
        return null;
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute()
    {
        $seconds = $this->duration_seconds;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Get episode title with number
     */
    public function getFullTitleAttribute()
    {
        return "Episódio {$this->episode_number}: {$this->title}";
    }

    /**
     * Check if user can watch this episode
     */
    public function canUserWatch($user)
    {
        if (!$user) {
            return false; // Must be logged in
        }

        if (!$this->is_active) {
            return false; // Episode is not active
        }

        if ($this->is_premium_only && !$user->isPremium()) {
            return false; // Premium only content
        }

        if (!$user->isPremium() && !$user->canWatchMoreEpisodes()) {
            return false; // Free user daily limit reached
        }

        return true;
    }

    /**
     * Get reason why user can/cannot watch this episode.
     * Returns null if user can watch, otherwise a reason code string.
     *
     * @param \App\Models\User|null $user
     * @return string|null
     */
    public function canUserWatchReason($user)
    {
        if (! $user) {
            return 'login_required';
        }

        if (! $this->is_active) {
            return 'episode_inactive';
        }

        if ($this->is_premium_only && ! $user->isPremium()) {
            return 'premium_required';
        }

        if (! $user->isPremium() && ! $user->canWatchMoreEpisodes()) {
            return 'daily_limit_reached';
        }

        return null;
    }

    /**
     * Get file size for quality
     */
    public function getFileSize($quality = '480p')
    {
        return $this->{"file_size_{$quality}_mb"} ?? 0;
    }

    /**
     * Check if video file exists
     */
    public function videoFileExists($quality = '480p')
    {
        $path = $this->{"video_path_{$quality}"};
        if (!$path) {
            return false;
        }

        $fullPath = storage_path('app/public/' . $path);
        return file_exists($fullPath);
    }

    /**
     * Generate streaming path for HLS
     */
    public function getHlsPathAttribute($quality = '480p')
    {
        $path = $this->{"video_path_{$quality}"};
        if (!$path) {
            return null;
        }

        // Replace extension with .m3u8 for HLS
        $pathWithoutExt = preg_replace('/\.[^.]+$/', '', $path);
        return "{$pathWithoutExt}.m3u8";
    }

    /**
     * Get estimated bandwidth requirement (in kbps)
     */
    public function getEstimatedBandwidth($quality = '480p')
    {
        $bandwidth = [
            '480p' => 500,  // 500 kbps
            '720p' => 1500, // 1500 kbps
        ];

        return $bandwidth[$quality] ?? 500;
    }
}
