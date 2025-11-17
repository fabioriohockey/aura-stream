<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHighlight extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dorama_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dorama()
    {
        return $this->belongsTo(Dorama::class);
    }
}