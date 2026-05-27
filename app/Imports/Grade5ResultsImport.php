// ═══════════════════════════════════════════════════════════════
// FILE: app/Imports/Grade5ResultsImport.php
// Adapted from uploaded file — removed zone_id references
// ═══════════════════════════════════════════════════════════════

namespace App\Imports;

use App\Models\Grade5ExamImport;
use App\Models\Grade5Result;
use App\Models\School;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class Grade5ResultsImport implements ToCollection, WithHeadingRow, SkipsOnFailure
{
    use SkipsFailures;

    public int   $imported  = 0;
    public int   $skipped   = 0;
    public int   $unmatched = 0;
    public array $errors    = [];

    private Grade5ExamImport $importRecord;
    private Collection $schoolMap;

    public function __construct(Grade5ExamImport $importRecord)
    {
        $this->importRecord = $importRecord;
        $this->schoolMap    = School::select('id', 'census_no', 'division_id')
            ->get()->keyBy('census_no');
    }

    public function collection(Collection $rows): void
    {
        $inserts = [];

        foreach ($rows as $index => $row) {
            $censusNo   = trim((string)($row['census_no'] ?? ''));
            $medium     = strtolower(trim((string)($row['medium'] ?? 'sinhala')));
            $sex        = trim((string)($row['sex'] ?? '0'));
            $income     = strtolower(trim((string)($row['income'] ?? 'below')));
            $totalMarks = (int)($row['total_marks'] ?? 0);
            $qualified  = trim((string)($row['whether_qualified'] ?? ''));

            if (empty($censusNo)) { $this->skipped++; continue; }

            $mediumMapped = match($medium) {
                'sinhala','si' => 'sinhala',
                'tamil','ta'   => 'tamil',
                'english','en' => 'english',
                default        => 'sinhala',
            };

            $sexMapped    = in_array($sex, ['1', 1, 'male', 'm']) ? 1 : 0;
            $incomeMapped = str_contains(strtolower($income), 'above') ? 'above' : 'below';
            $isQualified  = strtolower(trim($qualified)) === 'qualified';

            $school        = $this->schoolMap->get($censusNo);
            $schoolId      = $school?->id;
            $divisionId    = $school?->division_id;
            $schoolMatched = $school !== null;

            if (!$schoolMatched) $this->unmatched++;

            $this->imported++;
            $now      = now()->toDateTimeString();
            $inserts[] = [
                'import_id'      => $this->importRecord->id,
                'year'           => $this->importRecord->year,
                'school_id'      => $schoolId,
                'census_no'      => $censusNo,
                'schid'          => trim((string)($row['schid'] ?? '')) ?: null,
                'division_id'    => $divisionId,
                'medium'         => $mediumMapped,
                'sex'            => $sexMapped,
                'income'         => $incomeMapped,
                'total_marks'    => $totalMarks,
                'is_qualified'   => $isQualified ? 1 : 0,
                'school_matched' => $schoolMatched ? 1 : 0,
                'created_at'     => $now,
                'updated_at'     => $now,
            ];

            if (count($inserts) >= 500) {
                Grade5Result::insert($inserts);
                $inserts = [];
            }
        }

        if (!empty($inserts)) Grade5Result::insert($inserts);

        $this->importRecord->update([
            'imported'    => $this->imported,
            'skipped'     => $this->skipped,
            'unmatched'   => $this->unmatched,
            'imported_at' => now(),
        ]);
    }
}