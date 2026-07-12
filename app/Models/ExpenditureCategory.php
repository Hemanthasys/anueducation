<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\Auditable;

class ExpenditureCategory extends Model
{
    use Auditable;
    protected $fillable = [
        'code',
        'label_si',
        'label_en',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function votes(): HasMany
    {
        return $this->hasMany(ExpenditureVote::class);
    }

    public function activeVotes(): HasMany
    {
        return $this->hasMany(ExpenditureVote::class)->where('is_active', true);
    }

    public function getLocalizedLabelAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'si' ? $this->label_si : $this->label_en;
    }
}
