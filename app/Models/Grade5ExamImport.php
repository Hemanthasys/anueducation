<?php
// ═══════════════════════════════════════════════════════════════
// FILE: app/Models/Grade5ExamImport.php
// ═══════════════════════════════════════════════════════════════
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\Auditable;

class Grade5ExamImport extends Model
{
    use Auditable;
    protected $fillable = [
        'year', 'scope', 'division_id', 'file_name',
        'total_rows', 'imported', 'skipped', 'unmatched',
        'imported_by', 'imported_at', 'notes',
    ];

    protected $casts = [
        'year'        => 'integer',
        'imported_at' => 'datetime',
    ];

    public function results(): HasMany    { return $this->hasMany(Grade5Result::class, 'import_id'); }
    public function division(): BelongsTo { return $this->belongsTo(Division::class); }
    public function importedBy(): BelongsTo { return $this->belongsTo(User::class, 'imported_by'); }

    public static function availableYears(): array
    {
        return static::orderByDesc('year')->distinct()->pluck('year')->toArray();
    }
}
