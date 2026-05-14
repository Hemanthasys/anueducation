<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Programme extends Model
{
    protected $fillable = [
        'title_si',
        'title_en',
        'description_si',
        'description_en',
        'youtube_url',
        'flier_image',
        'social_artwork',
        'category',
        'is_featured',
        'status',
        'submitted_by',
        'approved_by',
        'published_at',
    ];

    protected $casts = [
        'is_featured'  => 'boolean',
        'published_at' => 'datetime',
    ];

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getTitleAttribute(): string
    {
        return $this->{'title_' . app()->getLocale()} ?? $this->title_en;
    }

    public function getYoutubeIdAttribute(): ?string
    {
        if (!$this->youtube_url) return null;
        preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\s]+)/', $this->youtube_url, $matches);
        return $matches[1] ?? null;
    }
}