<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class ContactMessage extends Model
{
    use Auditable;
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'ip_address',
        'status',
        'assigned_to',
        'assigned_at',
        'read_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'read_at'     => 'datetime',
    ];

    // Assigned user relationship
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}