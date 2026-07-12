<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class Slider extends Model
{
    use Auditable;
    protected $fillable = [
        'title_si',
        'title_en',
        'subtitle_si',
        'subtitle_en',
        'image',
        'button_text_si',
        'button_text_en',
        'button_url',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order'     => 'integer',
    ];
}