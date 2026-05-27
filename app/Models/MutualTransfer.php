<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MutualTransfer extends Model
{
    protected $fillable = [
        'user_id', 'current_school_id', 'preferred_division_id',
        'preferred_subject', 'notes_en', 'notes_si', 'phone', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currentSchool(): BelongsTo
    {
        return $this->belongsTo(School::class, 'current_school_id');
    }

    public function preferredDivision(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'preferred_division_id');
    }
}
