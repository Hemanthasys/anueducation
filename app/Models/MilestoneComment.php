<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MilestoneComment extends Model
{
    protected $fillable = [
        'milestone_update_id',
        'commented_by',
        'comment',
    ];

    public function milestoneUpdate(): BelongsTo
    {
        return $this->belongsTo(MilestoneUpdate::class, 'milestone_update_id');
    }

    public function commentedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'commented_by');
    }
}
