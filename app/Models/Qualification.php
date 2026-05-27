<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Qualification extends Model
{
    protected $fillable = ['name_en', 'name_si', 'type', 'is_active', 'order'];

    protected $casts = ['is_active' => 'boolean'];

    // Scope — active only
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    // Scope — by type
    public function scopeEducational($query)
    {
        return $query->where('type', 'educational');
    }

    public function scopeProfessional($query)
    {
        return $query->where('type', 'professional');
    }

    public function teacherQualifications(): HasMany
    {
        return $this->hasMany(TeacherQualification::class);
    }

    // Localized name helper
    public function getLocalizedName(): string
    {
        $locale = app()->getLocale();
        return ($locale === 'si' && $this->name_si) ? $this->name_si : $this->name_en;
    }
}
