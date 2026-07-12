<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Traits\Auditable;

class ExpenditureVote extends Model
{
    use Auditable;
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

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_expenditure_vote');
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
