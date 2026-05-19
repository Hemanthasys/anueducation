<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EssentialLink extends Model
{
    protected $fillable = [
        'name_en',
        'name_si',
        'description_en',
        'description_si',
        'url',
        'logo',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order'     => 'integer',
    ];

    // Scope — only active links, ordered
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }
}
