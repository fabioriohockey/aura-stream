<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dorama extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'synopsis',
        'country',
        'year',
        'episodes_total',
        'duration_minutes',
        'poster_path',
        'backdrop_path',
        'trailer_url',
        'status',
        'rating',
        'views_count',
        'is_featured',
        'is_active',
        'release_date',
        'language',
        'genres',
        'imdb_id',
    ];

    protected $casts = [
        'release_date' => 'date',
        'rating' => 'decimal:2',
        'views_count' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'genres' => 'array',
        'episodes_total' => 'integer',
        'duration_minutes' => 'integer',
        'year' => 'integer',
    ];

    /**
     * Get episodes of this dorama
     */
    public function episodes()
    {
        return $this->hasMany(Episode::class)->orderBy('episode_number', 'asc');
    }

    /**
     * Get active episodes only
     */
    public function activeEpisodes()
    {
        return $this->hasMany(Episode::class)->where('is_active', true)->orderBy('episode_number', 'asc');
    }

    /**
     * Get categories of this dorama
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'dorama_category');
    }

    /**
     * Scope para doramas ativos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para doramas em destaque
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope para doramas por país
     */
    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    /**
     * Scope para doramas por ano
     */
    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope para ordenar por popularidade (views)
     */
    public function scopePopular($query)
    {
        return $query->orderBy('views_count', 'desc');
    }

    /**
     * Scope para ordenar por avaliação
     */
    public function scopeTopRated($query)
    {
        return $query->orderBy('rating', 'desc');
    }

    /**
     * Scope para ordenar por mais recentes
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Increment view count
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    /**
     * Get poster URL
     */
    public function getPosterUrlAttribute()
    {
        if ($this->poster_path) {
            return asset('assets/posters/' . basename($this->poster_path));
        }
        return asset('placeholder.svg');
    }

    /**
     * Get backdrop URL
     */
    public function getBackdropUrlAttribute()
    {
        if ($this->backdrop_path) {
            return asset('assets/backdrops/' . basename($this->backdrop_path));
        }
        return $this->poster_url;
    }

    /**
     * Boot para gerar slug automaticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($dorama) {
            if (empty($dorama->slug)) {
                $dorama->slug = \Str::slug($dorama->title);
            }
        });

        static::updating(function ($dorama) {
            if ($dorama->isDirty('title') && empty($dorama->slug)) {
                $dorama->slug = \Str::slug($dorama->title);
            }
        });
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute()
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0) {
            return "{$hours}h {$minutes}min";
        }
        return "{$minutes}min";
    }

    /**
     * Check if dorama is currently airing
     */
    public function isAiring()
    {
        return $this->status === 'em_exibicao';
    }

    /**
     * Get status label in Portuguese
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'em_exibicao' => 'Em Exibição',
            'finalizado' => 'Finalizado',
            'cancelado' => 'Cancelado',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Get country flag emoji
     */
    public function getCountryFlagAttribute()
    {
        $flags = [
            'Coreia' => '🇰🇷',
            'Japão' => '🇯🇵',
            'China' => '🇨🇳',
            'Tailândia' => '🇹🇭',
            'Taiwan' => '🇹🇼',
        ];

        return $flags[$this->country] ?? '';
    }
}
