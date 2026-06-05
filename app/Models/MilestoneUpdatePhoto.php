<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MilestoneUpdatePhoto extends Model
{
    protected $fillable = [
        'milestone_update_id',
        'photo_path',
        'caption',
    ];

    protected static function booted(): void
    {
        // Auto-delete photo file from storage when record is deleted
        static::deleting(function (MilestoneUpdatePhoto $photo) {
            if ($photo->photo_path && Storage::disk('public')->exists($photo->photo_path)) {
                Storage::disk('public')->delete($photo->photo_path);
            }
        });
    }

    public function milestoneUpdate(): BelongsTo
    {
        return $this->belongsTo(MilestoneUpdate::class, 'milestone_update_id');
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->photo_path);
    }
}
