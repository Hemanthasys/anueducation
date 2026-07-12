<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class Notice extends Model
{
    use Auditable;
    protected $fillable = [
        'title_si',
        'title_en',
        'body_si',
        'body_en',
        'file_path',
        'category',
        'target_audience',
        'date',
        'is_active',
        'published_at',
        'expires_at',
    ];

    protected $casts = [
        'date'      => 'date',
        'is_active' => 'boolean',
        'published_at' => 'date',
        'expires_at'   => 'date',
    ];
}