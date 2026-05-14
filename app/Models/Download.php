<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    protected $fillable = [
        'title_si',
        'title_en',
        'file_path',
        'category',
        'year',
        'download_count',
        'is_active',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'download_count' => 'integer',
        'year'           => 'integer',
    ];
}