<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'title_si',
        'title_en',
        'body_si',
        'body_en',
        'category',
        'image',
        'status',
        'submitted_by',
        'reviewed_by',
        'approved_by',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getTitleAttribute(): string
    {
        return $this->{'title_' . app()->getLocale()} ?? $this->title_en;
    }
}