<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\Auditable;

class District extends Model
{
    use Auditable;
    protected $fillable = ['province_id', 'name_en', 'name_si'];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function zonalOffices(): HasMany
    {
        return $this->hasMany(ZonalOffice::class);
    }

    public function getLocalizedName(): string
    {
        $locale = app()->getLocale();
        return ($locale === 'si' && $this->name_si) ? $this->name_si : $this->name_en;
    }
}
