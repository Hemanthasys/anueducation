<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileChangeRequest extends Model
{
    protected $fillable = [
        'teacher_id',
        'requested_fields',
        'status',
        'reviewed_by',
        'reviewed_at',
        'reviewer_notes',
        'reviewer_confirmed',
        'reference_no',
    ];

    protected $casts = [
        'requested_fields'   => 'array',
        'reviewed_at'        => 'datetime',
        'reviewer_confirmed' => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ── Auto-generate reference number ────────────────────────────
    protected static function booted(): void
    {
        static::creating(function (ProfileChangeRequest $req) {
            $year     = date('Y');
            $count    = static::whereYear('created_at', $year)->count() + 1;
            $req->reference_no = 'PCR-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        });
    }

    // ── Apply approved changes to teacher record ──────────────────
    public function applyChanges(): void
    {
        if ($this->status !== 'approved') return;

        $updates = [];
        foreach ($this->requested_fields as $field => $change) {
            $updates[$field] = $change['new'];
        }

        $this->teacher->update($updates);
    }
}
