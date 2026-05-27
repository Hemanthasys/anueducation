<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $fillable = [
        'census_no',
        'name_si',
        'name_en',
        'division_id',
        'type',
        'class_span_from',
        'class_span_to',
        'established_date',
        'divisional_secretariat',
        'grama_niladari_division',
        'address',
        'address_si',
        'principal_id',
        'phone',
        'email',
        'lat',
        'lng',
        'medium',
        'ownership',
        'convenience_level',
        'is_active',
        'school_logo',
    ];

    protected $casts = [
        'established_date' => 'date',
        'is_active'        => 'boolean',
        'lat'              => 'decimal:8',
        'lng'              => 'decimal:8',
    ];

    // ── Relationships ─────────────────────────────────────────

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function principal()
    {
        return $this->belongsTo(User::class, 'principal_id');
    }

    public function staff()
    {
        return $this->hasMany(User::class)->whereNotNull('staff_type');
    }

    public function teachers()
    {
        return $this->hasMany(User::class)->where('staff_type', 'teacher');
    }

    public function vicePrincipals()
    {
        return $this->hasMany(User::class)->where('staff_type', 'vice_principal');
    }

    public function nonAcademicStaff()
    {
        return $this->hasMany(User::class)->where('staff_type', 'non_academic');
    }

    public function latestStats()
    {
        return $this->hasOne(SchoolStat::class)->latestOfMany('academic_year');
    }

    public function stats()
    {
        return $this->hasMany(SchoolStat::class);
    }

    public function physicalResources()
    {
        return $this->hasOne(SchoolPhysicalResource::class);
    }

    public function resourcePrograms()
    {
        return $this->hasOne(SchoolResourceProgram::class);
    }

    public function qualityCircleRecords()
    {
        return $this->hasMany(QualityCircleRecord::class);
    }

    public function latestQualityCircle()
    {
        return $this->hasOne(QualityCircleRecord::class)->latestOfMany();
    }

    // ── Bilingual name accessor ───────────────────────────────

    public function getNameAttribute(): string
    {
        return $this->{'name_' . app()->getLocale()} ?? $this->name_en;
    }

    // ── Bilingual type labels ─────────────────────────────────

    public function getSchoolTypeLabelsAttribute(): ?array
    {
        return match($this->type) {
            '1AB' => ['en' => 'Type 1AB (Grade 1-13, All Streams)', 'si' => '1AB වර්ගය (ශ්‍රේණිය 1-13, සියලු දොරකා)'],
            '1C'  => ['en' => 'Type 1C (Grade 1-13, Arts/Commerce)', 'si' => '1C වර්ගය (ශ්‍රේණිය 1-13, කලා/වාණිජ)'],
            '2'   => ['en' => 'Type 2 (Grade 1-11)', 'si' => '2 වර්ගය (ශ්‍රේණිය 1-11)'],
            '3'   => ['en' => 'Type 3 (Primary)', 'si' => '3 වර්ගය (ප්‍රාථමික)'],
            default => ['en' => $this->type ?? 'N/A', 'si' => $this->type ?? 'N/A'],
        };
    }

    // ── Bilingual medium labels ───────────────────────────────

    public function getMediumLabelsAttribute(): ?array
    {
        return match($this->medium) {
            'sinhala' => ['en' => 'Sinhala Medium',  'si' => 'සිංහල මාධ්‍ය'],
            'tamil'   => ['en' => 'Tamil Medium',    'si' => 'දෙමළ මාධ්‍ය'],
            'english' => ['en' => 'English Medium',  'si' => 'ඉංග්‍රීසි මාධ්‍ය'],
            'mixed'   => ['en' => 'Mixed Medium',    'si' => 'මිශ්‍ර මාධ්‍ය'],
            default   => ['en' => $this->medium ?? 'N/A', 'si' => $this->medium ?? 'N/A'],
        };
    }

    // ── Class span display ────────────────────────────────────

    public function getClassSpanAttribute(): ?string
    {
        if (!$this->class_span_from && !$this->class_span_to) return null;
        return $this->class_span_from . ' - ' . $this->class_span_to;
    }

    // ── Grades within span (for forms and filtering) ──────────

    public function gradesInSpan(): array
    {
        if (!$this->class_span_from || !$this->class_span_to) return [];
        return range((int)$this->class_span_from, (int)$this->class_span_to);
    }

    // ── Established year ──────────────────────────────────────

    public function getEstablishedYearAttribute(): ?string
    {
        return $this->established_date?->format('Y');
    }

    // ── Compliance badge (Phase 2) ────────────────────────────

    public function getComplianceBadgeAttribute(): ?array
    {
        return null;
    }

    // ── Staff counts from users table ─────────────────────────

    public function getTeacherCountAttribute(): int
    {
        return $this->teachers()->where('is_active', true)->count();
    }

    public function getVicePrincipalCountAttribute(): int
    {
        return $this->vicePrincipals()->where('is_active', true)->count();
    }

    public function getNonAcademicCountAttribute(): int
    {
        return $this->nonAcademicStaff()->where('is_active', true)->count();
    }

    // ── School logo URL ───────────────────────────────────────

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->school_logo) {
            return asset('storage/' . $this->school_logo);
        }
        return null;
    }
}
