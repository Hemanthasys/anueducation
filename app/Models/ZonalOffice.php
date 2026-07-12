<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\Auditable;

class ZonalOffice extends Model
{
    use Auditable;
    protected $fillable = ['district_id', 'name_en', 'name_si'];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function getLocalizedName(): string
    {
        $locale = app()->getLocale();
        return ($locale === 'si' && $this->name_si) ? $this->name_si : $this->name_en;
    }
}
