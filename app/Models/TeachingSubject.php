<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class TeachingSubject extends Model
{
    protected $fillable = ['name_en', 'name_si', 'level', 'order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('order')->orderBy('name_en');
    }

    // ── Relationships ─────────────────────────────────────────────

    public function appointedTeachers()
    {
        return $this->hasMany(Teacher::class, 'appointed_subject_id');
    }

    public function teachers()
    {
        return $this->belongsToMany(
                Teacher::class,
                'teacher_teaching_subjects',
                'teaching_subject_id',
                'teacher_id'
            )
            ->using(TeacherTeachingSubject::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    // ── Helpers ───────────────────────────────────────────────────

    public function getLocalizedName(): string
    {
        $locale = app()->getLocale();
        return ($locale === 'si' && $this->name_si) ? $this->name_si : $this->name_en;
    }

    /**
     * Grouped by level for optgroup dropdowns.
     * ['primary' => [id => name], 'ol' => [...], 'al' => [...]]
     */
    public static function groupedForDropdown(): array
    {
        $locale = app()->getLocale();
        $field  = $locale === 'si' ? 'name_si' : 'name_en';
        return static::active()
            ->get()
            ->groupBy('level')
            ->map(fn($group) => $group->pluck($field, 'id'))
            ->toArray();
    }

    /**
     * Flat [id => name] for simple selects.
     */
    public static function flatForDropdown(): array
    {
        $locale = app()->getLocale();
        $field  = $locale === 'si' ? 'name_si' : 'name_en';
        return static::active()->pluck($field, 'id')->toArray();
    }
}
