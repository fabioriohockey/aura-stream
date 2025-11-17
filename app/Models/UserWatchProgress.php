<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWatchProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dorama_id',
        'episode_id',
        'total_seconds',
        'last_position',
        'is_completed',
        'views_count',
        'date'
    ];

    protected $casts = [
        'total_seconds' => 'integer',
        'last_position' => 'integer',
        'is_completed' => 'boolean',
        'views_count' => 'integer',
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dorama()
    {
        return $this->belongsTo(Dorama::class);
    }

    public function episode()
    {
        return $this->belongsTo(Episode::class);
    }
}