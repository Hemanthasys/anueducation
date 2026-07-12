<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class Division extends Model
{
    use Auditable;
    protected $fillable = [
        'name_si',
        'name_en',
        'director_id',
        'acting_director_id',
        'address',
        'phone',
        'email',
        'google_map_url',
    ];

    public function director()
    {
        return $this->belongsTo(User::class, 'director_id');
    }

    public function actingDirector()
    {
        return $this->belongsTo(User::class, 'acting_director_id');
    }

    public function schools()
    {
        return $this->hasMany(School::class);
    }

    public function staff()
    {
        return $this->hasMany(DivisionStaff::class)->orderBy('order');
    }

    public function isas()
    {
        return $this->hasMany(DivisionIsa::class)->orderBy('order');
    }

    public function getNameAttribute(): string
    {
        return $this->{'name_' . app()->getLocale()} ?? $this->name_en;
    }
}