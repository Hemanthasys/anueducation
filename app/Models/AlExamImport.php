<?php
// ═══════════════════════════════════════════════════════════════
// FILE: app/Models/AlExamImport.php
// ═══════════════════════════════════════════════════════════════
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\Auditable;

class AlExamImport extends Model
{
    use Auditable;
    protected $fillable = [
    'year', 'scope', 'division_id', 'file_name',
    'total_rows', 'matched_rows', 'unmatched_rows', 'imported_by', 'remarks',
];

    protected $casts = ['year' => 'integer'];

    public function results(): HasMany
    {
        return $this->hasMany(AlResult::class, 'import_id');
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public static function availableYears(): array
    {
        return static::orderByDesc('year')->distinct()->pluck('year')->toArray();
    }
}
