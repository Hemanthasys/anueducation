<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FundingCategory extends Model
{
    protected $fillable = [
        'code',
        'label_si',
        'label_en',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function sources(): HasMany
    {
        return $this->hasMany(FundingSource::class);
    }

    public function activeSources(): HasMany
    {
        return $this->hasMany(FundingSource::class)->where('is_active', true);
    }

    public function getLocalizedLabelAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'si' ? $this->label_si : $this->label_en;
    }
}
