<?php

namespace App\Imports;

use App\Models\OlExamImport;
use App\Models\OlResult;
use App\Models\School;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OlResultsImport implements ToCollection, WithHeadingRow
{
    public int   $totalRows     = 0; // rows attempted (valid census, 5+ subjects)
    public int   $matchedRows   = 0; // census matched in DB
    public int   $unmatchedRows = 0; // census not in DB
    public int   $skippedAbsent = 0; // fewer than 5 valid subjects
    public int   $skippedRows   = 0; // empty/invalid census ID
    public array $unmatchedList = []; // list of unmatched census IDs with school name

    private OlExamImport $import;
    private array $schoolCache = []; // census_no → ['id' => x, 'division_id' => y]

    // Grades that count as "sat" — + means pending, not a grade
    private const SAT_GRADES = ['A', 'B', 'C', 'S', 'W'];

    public function __construct(OlExamImport $import)
    {
        $this->import = $import;
        $this->buildSchoolCache();
    }

    public function collection(Collection $rows): void
    {
        $batch = [];

        foreach ($rows as $row) {
            $row = $row->toArray();

            // ── Validate Census ID ────────────────────────────────
            // maatwebsite normalizes "Census ID" → "census_id"
            $censusRaw = $row['census_id'] ?? $row['census id'] ?? null;
            if ($censusRaw === null || $censusRaw === '' || !is_numeric($censusRaw)) {
                $this->skippedRows++;
                continue;
            }

            $censusNo = (string)(int)$censusRaw;

            // ── Parse all 9 subject cells ─────────────────────────
            // maatwebsite normalizes headers: spaces→_, lowercase, & removed
            // "Language & Literature" → "language_literature"
            // "1st Subject Group"    → "1st_subject_group"
            $rawCells = [
                'subj1' => $row['religion']                                          ?? null,
                'subj2' => $row['language_literature'] ?? $row['language__literature'] ?? null,
                'subj3' => $row['english_language']    ?? $row['english language']     ?? null,
                'subj4' => $row['science']                                            ?? null,
                'subj5' => $row['mathematics']                                        ?? null,
                'subj6' => $row['history']                                            ?? null,
                'subj7' => $row['1st_subject_group']   ?? $row['1st subject group']   ?? null,
                'subj8' => $row['2nd_subject_group']   ?? $row['2nd subject group']   ?? null,
                'subj9' => $row['3rd_subject_group']   ?? $row['3rd subject group']   ?? null,
            ];

            // Parse each cell
            $parsed = [];
            foreach ($rawCells as $key => $cell) {
                $parsed[$key] = $this->parseSubjectCell($cell);
            }

            // ── Check if student sat exam (5+ subjects with real grades) ──
            // Count cells where grade is A/B/C/S/W (not null/+/empty)
            $satCount = count(array_filter(
                $parsed,
                fn($p) => in_array($p['grade'], self::SAT_GRADES)
            ));

            if ($satCount < 5) {
                $this->skippedAbsent++;
                continue;
            }

            // ── Match census to school ────────────────────────────
            $schoolData = $this->schoolCache[$censusNo] ?? null;
            $schoolId   = $schoolData['id']          ?? null;
            $divisionId = $schoolData['division_id'] ?? null;
            $matched    = $schoolId !== null;

            if ($matched) {
                $this->matchedRows++;
            } else {
                $this->unmatchedRows++;
                // Record unmatched — include DOE school name for reference
                $schoolName = trim($row['school_name'] ?? $row['school name'] ?? '');
                $key        = $censusNo;
                if (!isset($this->unmatchedList[$key])) {
                    $this->unmatchedList[$key] = [
                        'census_no'   => $censusNo,
                        'school_name' => $schoolName,
                        'count'       => 0,
                    ];
                }
                $this->unmatchedList[$key]['count']++;
            }

            // ── Parse grade count ─────────────────────────────────
            $gradeCountRaw = $row['grade_count'] ?? $row['grade count'] ?? '0 0 0 0 0';
            $gradeCounts   = $this->parseGradeCount((string)$gradeCountRaw);

            // ── Other fields ──────────────────────────────────────
            $attemptNo = isset($row['attempt_no']) && is_numeric($row['attempt_no'])
                ? (int)$row['attempt_no']
                : (isset($row['attempt no']) && is_numeric($row['attempt no']) ? (int)$row['attempt no'] : 1);

            $gender = strtoupper(trim($row['gender'] ?? 'M'));
            $medium = strtoupper(trim($row['medium'] ?? 'S'));

            $examSchoolId = isset($row['school_id']) ? (string)$row['school_id'] : null;

            $batch[] = [
                'import_id'          => $this->import->id,
                'school_id'          => $schoolId,
                'census_no'          => $censusNo,
                'exam_school_id'     => $examSchoolId,
                'division_id'        => $divisionId,
                'attempt_no'         => in_array($attemptNo, [1, 2]) ? $attemptNo : 1,
                'gender'             => in_array($gender, ['M', 'F']) ? $gender : 'M',
                'medium'             => in_array($medium, ['S', 'T', 'E']) ? $medium : 'S',

                // Subject 1 — Religion (has code, no medium — language subject)
                'subj1_code'         => $parsed['subj1']['code'],
                'subj1_grade'        => $parsed['subj1']['grade'],

                // Subject 2 — Language & Literature (no medium — language subject)
                'subj2_code'         => $parsed['subj2']['code'],
                'subj2_grade'        => $parsed['subj2']['grade'],

                // Subject 3 — English Language (no medium — language subject)
                'subj3_grade'        => $parsed['subj3']['grade'],

                // Subject 4 — Science (has medium)
                'subj4_grade'        => $parsed['subj4']['grade'],
                'subj4_medium'       => $parsed['subj4']['medium'],

                // Subject 5 — Mathematics (has medium)
                'subj5_grade'        => $parsed['subj5']['grade'],
                'subj5_medium'       => $parsed['subj5']['medium'],

                // Subject 6 — History (has medium)
                'subj6_grade'        => $parsed['subj6']['grade'],
                'subj6_medium'       => $parsed['subj6']['medium'],

                // Subject 7 — 1st Subject Group (has code + medium)
                'subj7_code'         => $parsed['subj7']['code'],
                'subj7_grade'        => $parsed['subj7']['grade'],
                'subj7_medium'       => $parsed['subj7']['medium'],

                // Subject 8 — 2nd Subject Group (has code + medium)
                'subj8_code'         => $parsed['subj8']['code'],
                'subj8_grade'        => $parsed['subj8']['grade'],
                'subj8_medium'       => $parsed['subj8']['medium'],

                // Subject 9 — 3rd Subject Group (has code + medium)
                'subj9_code'         => $parsed['subj9']['code'],
                'subj9_grade'        => $parsed['subj9']['grade'],
                'subj9_medium'       => $parsed['subj9']['medium'],

                // Grade counts from file
                'grade_a_count'      => $gradeCounts['A'],
                'grade_b_count'      => $gradeCounts['B'],
                'grade_c_count'      => $gradeCounts['C'],
                'grade_s_count'      => $gradeCounts['S'],
                'grade_w_count'      => $gradeCounts['W'],
                'subjects_sat_count' => $gradeCounts['sat'],

                'school_matched'     => $matched,
                'created_at'         => now(),
                'updated_at'         => now(),
            ];

            $this->totalRows++;

            // Batch insert every 500 rows
            if (count($batch) >= 500) {
                OlResult::insert($batch);
                $batch = [];
            }
        }

        // Insert remaining
        if (!empty($batch)) {
            OlResult::insert($batch);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // Parse a single subject cell
    //
    // Formats:
    //   11SS 1   → code=11, medium=S, grade=S  (with medium, with SBA)
    //   21 S 3   → code=21, medium=null, grade=S (no medium, with SBA)
    //   11SS -   → code=11, medium=S, grade=S  (SBA not submitted — ignore -)
    //   11SS     → code=11, medium=S, grade=S  (no SBA info)
    //   11S+     → code=11, medium=S, grade=null (+ = pending, count as sat)
    //   11S+ -   → code=11, medium=S, grade=null, SBA not submitted
    //   21 +     → code=21, medium=null, grade=null (pending, no medium)
    //   empty    → not taken
    // ─────────────────────────────────────────────────────────────
    private function parseSubjectCell(?string $value): array
    {
        $default = ['code' => null, 'grade' => null, 'medium' => null];

        if ($value === null || trim($value) === '' || $value === 'nan') {
            return $default;
        }

        $value = trim($value);

        // Remove trailing SBA info:
        // " 1", " 2", " 3", " 4", " 5" (SBA period) or " -" (not submitted)
        // Strip from end: space followed by digit or dash
        $value = preg_replace('/\s+[\d-]+$/', '', $value);
        $value = trim($value);

        if ($value === '') return $default;

        // Pattern 1: {digits}{medium_letter}{grade_letter}
        // e.g. 11SS, 34SW, 32EC, 11S+
        if (preg_match('/^(\d+)([A-Z])([A-Za-z+])$/', $value, $m)) {
            $code   = $m[1];
            $medium = $m[2]; // S, T, E
            $raw    = strtoupper($m[3]);
            $grade  = in_array($raw, self::SAT_GRADES) ? $raw : null; // + → null
            return ['code' => $code, 'grade' => $grade, 'medium' => $medium];
        }

        // Pattern 2: {digits} {grade_letter} — no medium (language subjects)
        // e.g. "21 S", "31 A", "21 +"
        if (preg_match('/^(\d+)\s+([A-Za-z+])$/', $value, $m)) {
            $code  = $m[1];
            $raw   = strtoupper($m[2]);
            $grade = in_array($raw, self::SAT_GRADES) ? $raw : null; // + → null
            return ['code' => $code, 'grade' => $grade, 'medium' => null];
        }

        return $default;
    }

    // ─────────────────────────────────────────────────────────────
    // Parse grade count: "0 1 2 6 0" → [A=>0, B=>1, C=>2, S=>6, W=>0]
    // Always 5 space-separated integers in order A B C S W
    // "0 0 0 0 0" means all subjects are + (pending) — not all failed
    // ─────────────────────────────────────────────────────────────
    private function parseGradeCount(string $value): array
    {
        $default = ['A' => 0, 'B' => 0, 'C' => 0, 'S' => 0, 'W' => 0, 'sat' => 0];

        $parts = array_values(array_filter(
            explode(' ', trim($value)),
            fn($p) => is_numeric($p)
        ));

        if (count($parts) < 5) return $default;

        $a = (int)$parts[0];
        $b = (int)$parts[1];
        $c = (int)$parts[2];
        $s = (int)$parts[3];
        $w = (int)$parts[4];

        return ['A' => $a, 'B' => $b, 'C' => $c, 'S' => $s, 'W' => $w, 'sat' => $a+$b+$c+$s+$w];
    }

    // ─────────────────────────────────────────────────────────────
    // Build census_no → {school_id, division_id} lookup
    // ─────────────────────────────────────────────────────────────
    private function buildSchoolCache(): void
    {
        $schools = School::whereNotNull('census_no')
            ->select('id', 'census_no', 'division_id')
            ->get();

        foreach ($schools as $school) {
            $this->schoolCache[(string)$school->census_no] = [
                'id'          => $school->id,
                'division_id' => $school->division_id,
            ];
        }
    }
}
