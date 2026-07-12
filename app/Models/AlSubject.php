<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use App\Traits\Auditable;

class AlSubject extends Model
{
    use Auditable;
    protected $fillable = ['code', 'name_en', 'name_si', 'is_active'];
    protected $casts    = ['is_active' => 'boolean'];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    // Get localized name
    public function getLocalizedName(): string
    {
        $locale = app()->getLocale();
        return $locale === 'si' && $this->name_si
            ? $this->name_si
            : $this->name_en;
    }

    // Stream → subject code mapping
    public static function streamSubjectCodes(): array
    {
        return [
            'PHYSICAL SCIENCE'       => ['1','2','10'],
            'BIOLOGICAL SCIENCE'     => ['1','2','8','9'],
            'ENGINEERING TECHNOLOGY' => ['7','8','20','22','28','29','32','51','65','67'],
            'BIOSYSTEMS TECHNOLOGY'  => ['7','8','20','22','28','66','67'],
            'COMMERCE'               => ['7','20','21','32','33'],
            'ARTS'                   => ['8','17','18','20','21','22','23','24','25A','25B','25C',
                                         '28','29','41','42','43','44','45','46','47','51','52',
                                         '53','54','55','56','57','58','59','71','72','73','74',
                                         '75','78','79','81','82','83','84','86','87'],
            'NON'                    => [],
        ];
    }
}
