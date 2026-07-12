<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\Auditable;

class Province extends Model
{
    use Auditable;
    protected $fillable = ['name_en', 'name_si'];

    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    public function getLocalizedName(): string
    {
        $locale = app()->getLocale();
        return ($locale === 'si' && $this->name_si) ? $this->name_si : $this->name_en;
    }
}
