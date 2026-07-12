<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class DivisionStaff extends Model
{
    use Auditable;
    protected $table = 'division_staff';

    protected $fillable = [
        'division_id',
        'name',
        'designation',
        'photo',
        'phone',
        'email',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order'     => 'integer',
    ];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }
}