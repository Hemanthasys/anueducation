<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

use App\Traits\Auditable;

class Event extends Model
{
    use Auditable;
    protected $fillable = [
        'title_en', 'title_si',
        'description_en', 'description_si',
        'start_date', 'end_date',
        'start_time', 'end_time',
        'location', 'color', 'is_active', 'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_active'  => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('end_date', '>=', Carbon::today())->orderBy('start_date');
    }

    public function scopeOnDate($query, $date)
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        return $query->where('start_date', '<=', $date->toDateString())
                      ->where('end_date', '>=', $date->toDateString());
    }

    public function getIsMultiDayAttribute(): bool
    {
        return !$this->start_date->isSameDay($this->end_date);
    }

    public function getTitleAttribute()
    {
        return app()->getLocale() === 'si' && $this->title_si
            ? $this->title_si
            : $this->title_en;
    }

    public function getDescriptionAttribute()
    {
        return app()->getLocale() === 'si' && $this->description_si
            ? $this->description_si
            : $this->description_en;
    }
}