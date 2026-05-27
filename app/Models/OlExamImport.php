<?php
// ═══════════════════════════════════════════════════════════════
// FILE: app/Models/OlExamImport.php
// ═══════════════════════════════════════════════════════════════
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OlExamImport extends Model
{
    protected $fillable = [
        'year', 'scope', 'division_id', 'file_name',
        'total_rows', 'matched_rows', 'unmatched_rows', 'imported_by',
    ];

    protected $casts = ['year' => 'integer'];

    public function results(): HasMany    { return $this->hasMany(OlResult::class, 'import_id'); }
    public function division(): BelongsTo { return $this->belongsTo(Division::class); }
    public function importedBy(): BelongsTo { return $this->belongsTo(User::class, 'imported_by'); }

    public static function availableYears(): array
    {
        return static::orderByDesc('year')->distinct()->pluck('year')->toArray();
    }
}
