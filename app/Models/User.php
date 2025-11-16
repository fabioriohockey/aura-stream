<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'subscription_type',
        'subscription_expires_at',
        'episodes_watched_today',
        'last_watch_date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'subscription_expires_at' => 'datetime',
            'last_watch_date' => 'date',
        ];
    }

    /**
     * Check if user can watch more episodes today (for free users)
     */
    public function canWatchMoreEpisodes(): bool
    {
        if ($this->subscription_type === 'premium') {
            return true;
        }

        $today = now()->toDateString();

        // Reset counter if it's a new day
        if ($this->last_watch_date !== $today) {
            $this->episodes_watched_today = 0;
            $this->last_watch_date = $today;
            $this->save();
        }

        return $this->episodes_watched_today < 1;
    }

    /**
     * Increment watched episodes count
     */
    public function incrementWatchedEpisodes(): void
    {
        if ($this->subscription_type === 'free') {
            $today = now()->toDateString();

            if ($this->last_watch_date !== $today) {
                $this->episodes_watched_today = 1;
                $this->last_watch_date = $today;
            } else {
                $this->episodes_watched_today++;
            }

            $this->save();
        }
    }

    /**
     * Get remaining episodes for today (for free users)
     */
    public function getRemainingEpisodesToday(): int
    {
        if ($this->subscription_type === 'premium') {
            return -1; // Unlimited
        }

        $this->canWatchMoreEpisodes(); // This will reset if needed

        return max(0, 1 - $this->episodes_watched_today);
    }

    /**
     * Check if user has premium subscription
     */
    public function isPremium(): bool
    {
        return $this->subscription_type === 'premium' &&
               (!$this->subscription_expires_at || $this->subscription_expires_at->isFuture());
    }
}
