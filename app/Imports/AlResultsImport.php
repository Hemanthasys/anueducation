<?php

namespace App\Imports;

use App\Models\AlExamImport;
use App\Models\AlResult;
use App\Models\School;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AlResultsImport
{
    public int $totalRows     = 0;
    public int $matchedRows   = 0;
    public int $unmatchedRows = 0;

    private AlExamImport $import;
    private array $schoolLookup = []; // census_no => [id, division_id]

    public function __construct(AlExamImport $import)
    {
        $this->import = $import;

        // Build school lookup cache
        $this->schoolLookup = School::whereNotNull('census_no')
            ->get(['id', 'census_no', 'division_id'])
            ->keyBy('census_no')
            ->map(fn($s) => ['id' => $s->id, 'division_id' => $s->division_id])
            ->toArray();
    }

    public function import(string $filePath): void
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet       = $spreadsheet->getActiveSheet();
        $rows        = $sheet->toArray(null, true, true, false);

        $batch     = [];
        $batchSize = 500;

        foreach ($rows as $i => $row) {
            if ($i === 0) continue;                             // skip header
            if (empty($row[0]) && empty($row[3])) continue;    // skip empty rows

            $this->totalRows++;

            // Column mapping (0-indexed):
            // 0=seq, 1=seq2, 2=zone_name, 3=school_code, 4=school_name
            // 5=gender, 6=subject1, 7=subject2, 8=subject3
            // 9=passes, 10=total_subs, 11=qualify, 12=stream
            // 13=zscore, 14=dist_rank, 15=island_rank
            // 16=gen_english, 17=cgt_marks, 18=attempt, 19=census_no

            $censusNo   = isset($row[19]) ? trim((string)$row[19]) : null;
            $schoolData = $censusNo && isset($this->schoolLookup[$censusNo]) ? $this->schoolLookup[$censusNo] : null;

            $sub1 = AlResult::parseSubjectField($row[6] ?? null);
            $sub2 = AlResult::parseSubjectField($row[7] ?? null);
            $sub3 = AlResult::parseSubjectField($row[8] ?? null);

            $passes = AlResult::parsePassesField($row[9] ?? null);

            // Parse general english grade
            $genEngRaw   = trim((string)($row[16] ?? ''));
            $genEngGrade = null;
            if (preg_match('/([ABCSW])/', $genEngRaw, $gm)) {
                $genEngGrade = $gm[1];
            }

            // Parse Z-score
            $zScore = null;
            if (isset($row[13]) && is_numeric($row[13])) {
                $zScore = round((float)$row[13], 4);
            }

            // Medium from first subject
            $medium = $sub1['medium'] ?? $sub2['medium'] ?? $sub3['medium'] ?? null;

            // CGT marks — cap at 100
            $cgt = isset($row[17]) && is_numeric($row[17]) ? min(100, (int)$row[17]) : null;

            $matched = $schoolData !== null;
            if ($matched) $this->matchedRows++;
            else $this->unmatchedRows++;

            $batch[] = [
                'import_id'        => $this->import->id,
                'year'             => $this->import->year,
                'census_no'        => $censusNo,
                'school_id'        => $schoolData['id'] ?? null,
                'division_id'      => $schoolData['division_id'] ?? null,
                'gender'           => strtoupper(substr(trim((string)($row[5] ?? '')), 0, 1)) ?: null,
                'medium'           => $medium,
                'stream'           => trim((string)($row[12] ?? '')) ?: null,
                'subject_1_code'   => $sub1['code'],
                'subject_1_grade'  => $sub1['grade'],
                'subject_1_medium' => $sub1['medium'],
                'subject_2_code'   => $sub2['code'],
                'subject_2_grade'  => $sub2['grade'],
                'subject_2_medium' => $sub2['medium'],
                'subject_3_code'   => $sub3['code'],
                'subject_3_grade'  => $sub3['grade'],
                'subject_3_medium' => $sub3['medium'],
                'passes_a'         => $passes['a'],
                'passes_b'         => $passes['b'],
                'passes_c'         => $passes['c'],
                'passes_s'         => $passes['s'],
                'total_subjects'   => isset($row[10]) && is_numeric($row[10]) ? (int)$row[10] : 0,
                'is_qualified'     => strtoupper(trim((string)($row[11] ?? ''))) === 'Y',
                'cgt_marks'        => $cgt,
                'gen_english_grade'=> $genEngGrade,
                'district_rank'    => isset($row[14]) && is_numeric($row[14]) ? (int)$row[14] : null,
                'island_rank'      => isset($row[15]) && is_numeric($row[15]) ? (int)$row[15] : null,
                'z_score'          => $zScore,
                'attempt'          => isset($row[18]) && is_numeric($row[18]) ? (int)$row[18] : 1,
                'school_matched'   => $matched,
                'created_at'       => now(),
                'updated_at'       => now(),
            ];

            if (count($batch) >= $batchSize) {
                DB::table('al_results')->insert($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            DB::table('al_results')->insert($batch);
        }

        // Update import stats
        $this->import->update([
            'total_rows'     => $this->totalRows,
            'matched_rows'   => $this->matchedRows,
            'unmatched_rows' => $this->unmatchedRows,
        ]);
    }
}
