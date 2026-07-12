<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class Menu extends Model
{
    use Auditable;
    protected $fillable = [
        'name',
        'location',
    ];

    public function items()
    {
        return $this->hasMany(MenuItem::class)->whereNull('parent_id')->orderBy('order');
    }
}