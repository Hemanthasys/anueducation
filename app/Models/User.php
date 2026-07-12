<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

use App\Traits\Auditable;

class User extends Authenticatable
{
    use Auditable, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'username', 'email', 'password',
        'phone', 'school_id',
        'nic', 'birthday', 'appointed_date', 'photo',
        'subject_id', 'division_id',
        'must_change_password', 'is_active',
        'service_grade', 'previous_school_id', 'pool_entered_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at'   => 'datetime',
        'birthday'            => 'date',
        'appointed_date'      => 'date',
        'pool_entered_at'     => 'datetime',
        'must_change_password'=> 'boolean',
        'is_active'           => 'boolean',
        'password'            => 'hashed',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function previousSchool(): BelongsTo
    {
        return $this->belongsTo(School::class, 'previous_school_id');
    }

    // Teacher record linked to this user (for promoted principals)
    public function teacherRecord(): HasOne
    {
        return $this->hasOne(Teacher::class, 'user_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function workingHistory(): HasMany
    {
        return $this->hasMany(TeacherWorkingHistory::class)->orderByDesc('appointed_date');
    }

    public function qualifications(): HasMany
    {
        return $this->hasMany(TeacherQualification::class);
    }

    public function mutualTransfer(): HasOne
    {
        return $this->hasOne(MutualTransfer::class)->where('is_active', true);
    }

    // ── Filament panel access ───────────────────────────────────

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        $adminRoles = [
            'super_admin', 'zonal_director', 'divisional_director',
            'zonal_officer', 'zonal_officer_admin', 'zonal_officer_planning',
            'zonal_officer_schools', 'zonal_officer_accounts', 'zonal_officer_development',
            'content_creator',
        ];
        return $this->hasAnyRole($adminRoles) && $this->is_active;
    }

    // ── Display name — initials format e.g. "K. A. Perera" ─────

    public function getDisplayNameAttribute(): string
    {
        $parts = explode(' ', trim($this->name));
        if (count($parts) <= 1) return $this->name;

        $lastName = array_pop($parts);
        $initials = array_map(fn($p) => strtoupper(substr($p, 0, 1)) . '.', $parts);

        return implode(' ', $initials) . ' ' . $lastName;
    }

    // ── Auto-generate username ──────────────────────────────────

    public static function generateUsername(string $name, string $nic, string $role): string
    {
        $prefix  = $role === 'principal' ? 'P' : 'T';
        $nicPart = substr(preg_replace('/[^0-9]/', '', $nic), 0, 5);

        $parts    = explode(' ', trim($name));
        $lastName = array_pop($parts);
        $initials = implode('', array_map(fn($p) => strtoupper(substr($p, 0, 1)), array_slice($parts, 0, 3)));

        $username = $prefix . $nicPart . $initials;

        $base  = $username;
        $count = 1;
        while (static::where('username', $username)->exists()) {
            $username = $base . $count;
            $count++;
        }

        return $username;
    }

    // ── Retirement date ─────────────────────────────────────────

    public function getRetirementDateAttribute(): ?\Carbon\Carbon
    {
        if (!$this->birthday) return null;
        return $this->birthday->copy()->addYears(60);
    }

    // ── Reset password to username ──────────────────────────────

    public function resetToDefaultPassword(): void
    {
        $this->update([
            'password'             => bcrypt($this->username),
            'must_change_password' => true,
        ]);
    }

    // ── Principal pool helpers ──────────────────────────────────

    public function isInPool(): bool
    {
        return $this->hasRole('school_principal') && is_null($this->school_id);
    }

    public function enterPool(?int $previousSchoolId = null): void
    {
        $this->update([
            'school_id'          => null,
            'previous_school_id' => $previousSchoolId ?? $this->school_id,
            'pool_entered_at'    => now(),
        ]);
    }
}