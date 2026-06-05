<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenditureVote extends Model
{
    protected $fillable = [
        'expenditure_category_id',
        'code',
        'label_si',
        'label_en',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenditureCategory::class, 'expenditure_category_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function getLocalizedLabelAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'si' ? $this->label_si : $this->label_en;
    }

    public function getDropdownLabelAttribute(): string
    {
        $label = app()->getLocale() === 'si' ? $this->label_si : $this->label_en;
        return $this->code . ' — ' . $label;
    }
}
