<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\Auditable;

class QualityCircleCriteria extends Model
{
    use Auditable;
    protected $table = 'quality_circle_criteria'; // prevent Laravel auto-pluralizing to 'quality_circle_criterias'

    protected $fillable = ['order', 'name_si', 'name_en', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function marks(): HasMany
    {
        return $this->hasMany(QualityCircleMark::class, 'criteria_id');
    }

    public function getLocalizedName(string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        return $locale === 'si' ? $this->name_si : $this->name_en;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }
}