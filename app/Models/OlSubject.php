<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class OlSubject extends Model
{
    use Auditable;
    protected $fillable = [
        'code', 'name_en', 'name_si', 'subject_group',
        'is_mother_language', 'is_mathematics', 'is_active',
    ];

    protected $casts = [
        'is_mother_language' => 'boolean',
        'is_mathematics'     => 'boolean',
        'is_active'          => 'boolean',
    ];

    public function scopeActive($query) { return $query->where('is_active', true); }

    public function getLocalizedName(): string
    {
        $locale = app()->getLocale();
        return $locale === 'si' && $this->name_si ? $this->name_si : $this->name_en;
    }

    public static function motherLanguageCodes(): array
    {
        return static::where('is_mother_language', true)->pluck('code')->toArray();
    }

    public static function mathematicsCode(): ?string
    {
        return static::where('is_mathematics', true)->value('code');
    }
}
