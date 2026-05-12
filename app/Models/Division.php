<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $fillable = [
        'name_si',
        'name_en',
        'director_id',
    ];

    public function director()
    {
        return $this->belongsTo(User::class, 'director_id');
    }

    public function schools()
    {
        return $this->hasMany(School::class);
    }

    public function getNameAttribute(): string
    {
        return $this->{'name_' . app()->getLocale()} ?? $this->name_en;
    }
}