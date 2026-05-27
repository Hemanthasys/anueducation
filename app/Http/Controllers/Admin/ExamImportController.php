<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\AlResultsImport;
use App\Imports\OlResultsImport;
use App\Imports\Grade5ResultsImport;
use App\Models\AlExamImport;
use App\Models\AlResult;
use App\Models\OlExamImport;
use App\Models\OlResult;
use App\Models\Grade5ExamImport;
use App\Models\Grade5Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExamImportController extends Controller
{
    // ── Authorization ─────────────────────────────────────────────
    private function checkAccess(): void
    {
        if (!Auth::user()?->hasAnyRole(['super_admin', 'zonal_director'])) {
            abort(403);
        }
    }

    // ── A/L Import ────────────────────────────────────────────────
    public function importAl(Request $request)
    {
        $this->checkAccess();

        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $request->validate([
            'year'    => 'required|integer|min:2000|max:2099',
            'file'    => 'required|file|mimes:xlsx,xls|max:30720',
            'remarks' => 'nullable|string|max:500',
            'replace' => 'nullable|boolean',
        ]);

        $year    = (int)$request->year;
        $replace = $request->boolean('replace');

        $existing = AlExamImport::where('year', $year)->first();

        if ($existing && !$replace) {
            return back()->with('al_error', "A/L {$year} results already exist. Enable 'Replace existing' to overwrite.");
        }

        if ($existing && $replace) {
            AlResult::where('year', $year)->delete();
            $existing->delete();
        }

        $import = AlExamImport::create([
            'year'        => $year,
            'scope'       => 'province',
            'imported_by' => Auth::user()->name,
            'remarks'     => $request->remarks,
        ]);

        try {
            // Use getRealPath() — works on all OS, no storage path issues
            $fullPath = $request->file('file')->getRealPath();

            $importer = new AlResultsImport($import);
            $importer->import($fullPath);

            return back()->with('al_success', [
                'year'      => $year,
                'total'     => $importer->totalRows,
                'matched'   => $importer->matchedRows,
                'unmatched' => $importer->unmatchedRows,
            ]);

        } catch (\Exception $e) {
            $import->delete();
            return back()->with('al_error', 'Import failed: ' . $e->getMessage());
        }
    }

    // ── O/L Import ────────────────────────────────────────────────
    public function importOl(Request $request)
    {
        $this->checkAccess();

        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $request->validate([
            'year'    => 'required|integer|min:2000|max:2099',
            'file'    => 'required|file|mimes:xlsx,xls|max:30720',
            'remarks' => 'nullable|string|max:500',
            'replace' => 'nullable|boolean',
        ]);

        $year    = (int)$request->year;
        $replace = $request->boolean('replace');

        $existing = OlExamImport::where('year', $year)->first();

        if ($existing && !$replace) {
            return back()->with('ol_error', "O/L {$year} results already exist. Enable 'Replace existing' to overwrite.");
        }

        if ($existing && $replace) {
            OlResult::where('import_id', $existing->id)->delete();
            $existing->delete();
        }

        $import = OlExamImport::create([
            'year'        => $year,
            'scope'       => 'province',
            'imported_by' => Auth::id(),
            'remarks'     => $request->remarks,
        ]);

        try {
            $fullPath = $request->file('file')->getRealPath();

            $importer = new OlResultsImport($import);
            Excel::import($importer, $fullPath);

            $import->update([
                'total_rows'     => $importer->totalRows,
                'matched_rows'   => $importer->matchedRows,
                'unmatched_rows' => $importer->unmatchedRows,
            ]);

            return back()->with('ol_success', [
                'year'          => $year,
                'total'         => $importer->totalRows,
                'matched'       => $importer->matchedRows,
                'unmatched'     => $importer->unmatchedRows,
                'skipped_absent'=> $importer->skippedAbsent,
                'skipped_invalid'=> $importer->skippedRows,
                'unmatched_list'=> array_values($importer->unmatchedList),
            ]);

        } catch (\Exception $e) {
            OlResult::where('import_id', $import->id)->delete();
            $import->delete();
            return back()->with('ol_error', 'Import failed: ' . $e->getMessage());
        }
    }

    // ── Grade 5 Import ────────────────────────────────────────────
    public function importG5(Request $request)
    {
        $this->checkAccess();

        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $request->validate([
            'year'    => 'required|integer|min:2000|max:2099',
            'file'    => 'required|file|mimes:xlsx,xls,csv|max:30720',
            'remarks' => 'nullable|string|max:500',
            'replace' => 'nullable|boolean',
        ]);

        $year    = (int)$request->year;
        $replace = $request->boolean('replace');

        $existing = Grade5ExamImport::where('year', $year)->first();

        if ($existing && !$replace) {
            return back()->with('g5_error', "Grade 5 {$year} results already exist. Enable 'Replace existing' to overwrite.");
        }

        if ($existing && $replace) {
            $existing->results()->delete();
            $existing->delete();
        }

        $import = Grade5ExamImport::create([
            'year'        => $year,
            'scope'       => 'province',
            'total_rows'  => 0,
            'imported'    => 0,
            'imported_by' => Auth::id(),
            'imported_at' => now(),
            'notes'       => $request->remarks,
        ]);

        try {
            $fullPath = $request->file('file')->getRealPath();

            $importer = new Grade5ResultsImport($import);
            Excel::import($importer, $fullPath);

            return back()->with('g5_success', [
                'year'      => $year,
                'total'     => $importer->imported + $importer->skipped + $importer->unmatched,
                'imported'  => $importer->imported,
                'unmatched' => $importer->unmatched,
                'skipped'   => $importer->skipped,
            ]);

        } catch (\Exception $e) {
            $import->results()->delete();
            $import->delete();
            return back()->with('g5_error', 'Import failed: ' . $e->getMessage());
        }
    }

    // ── Template Downloads ────────────────────────────────────────
    public function templateAl()
    {
        $this->checkAccess();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $headers     = [
            'A1' => 'Seq',          'B1' => 'Seq2',           'C1' => 'Zone',
            'D1' => 'School Code',  'E1' => 'School Name',    'F1' => 'Gender',
            'G1' => 'Subject 1',    'H1' => 'Subject 2',      'I1' => 'Subject 3',
            'J1' => 'Passes (A B C S)', 'K1' => 'Total Subjects', 'L1' => 'Qualified (Y/N)',
            'M1' => 'Stream',       'N1' => 'Z-Score',        'O1' => 'District Rank',
            'P1' => 'Island Rank',  'Q1' => 'General English','R1' => 'CGT Marks (0-100)',
            'S1' => 'Attempt',      'T1' => 'Census No',
        ];
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        $writer  = new Xlsx($spreadsheet);
        $tmpFile = tempnam(sys_get_temp_dir(), 'al_tpl_');
        $writer->save($tmpFile);

        return response()->download($tmpFile, 'AL_Results_Import_Template.xlsx')->deleteFileAfterSend(true);
    }

    public function templateOl()
    {
        $this->checkAccess();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        // Column order matches actual DOE OL results export exactly
        $headers = [
            'A1' => 'School ID',
            'B1' => 'School Name',
            'C1' => 'Attempt No',
            'D1' => 'Gender',
            'E1' => 'Medium',
            'F1' => 'Religion',
            'G1' => 'Language & Literature',
            'H1' => 'English language',
            'I1' => 'Science',
            'J1' => 'Mathematics',
            'K1' => 'History',
            'L1' => '1st Subject Group',
            'M1' => '2nd Subject Group',
            'N1' => '3rd Subject Group',
            'O1' => 'Grade Count',
            'P1' => 'Zone',
            'Q1' => 'Census ID',
        ];
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style header row
        $headerStyle = $sheet->getStyle('A1:Q1');
        $headerStyle->getFont()->setBold(true);
        $headerStyle->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('1A3C6E');
        $headerStyle->getFont()->getColor()->setRGB('FFFFFF');

        // Example data row showing expected cell formats
        $examples = [
            'A2' => '1001',
            'B2' => 'A/EXAMPLE MAHA VIDYALAYA',
            'C2' => '1',
            'D2' => 'M',
            'E2' => 'S',
            'F2' => '11SS 1',
            'G2' => '21 S 3',
            'H2' => '31 S 1',
            'I2' => '34SS 3',
            'J2' => '32SS 2',
            'K2' => '33SC 4',
            'L2' => '62SB 3',
            'M2' => '43SS 3',
            'N2' => '86SC 3',
            'O2' => '0 1 2 6 0',
            'P2' => 'ANURADHAPURA',
            'Q2' => '19269',
        ];
        foreach ($examples as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Notes row
        $sheet->setCellValue('A3', 'NOTE: Grade Count format = A B C S W (space separated). Subject cells: {code}{medium}{grade} {SBA} e.g. 34SS 3');
        $sheet->getStyle('A3')->getFont()->setItalic(true)->getColor()->setRGB('888888');
        $sheet->mergeCells('A3:Q3');

        // Auto-width
        foreach (range('A', 'Q') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer  = new Xlsx($spreadsheet);
        $tmpFile = tempnam(sys_get_temp_dir(), 'ol_tpl_');
        $writer->save($tmpFile);

        return response()->download($tmpFile, 'OL_Results_Import_Template.xlsx')->deleteFileAfterSend(true);
    }

    public function templateG5()
    {
        $this->checkAccess();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $headers     = [
            'A1' => 'SCHNAME', 'B1' => 'SCHID',    'C1' => 'CENSUS NO',
            'D1' => 'DATE OF BIRTH', 'E1' => 'MEDIUM', 'F1' => 'SEX',
            'G1' => 'INCOME',  'H1' => 'TOTAL MARKS', 'I1' => 'WHETHER QUALIFIED',
            'J1' => 'REMARKS', 'K1' => 'ZONE',
        ];
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        $writer  = new Xlsx($spreadsheet);
        $tmpFile = tempnam(sys_get_temp_dir(), 'g5_tpl_');
        $writer->save($tmpFile);

        return response()->download($tmpFile, 'Grade5_Results_Import_Template.xlsx')->deleteFileAfterSend(true);
    }

    // ── Delete import ─────────────────────────────────────────────
    public function deleteImport(Request $request, string $type, int $id)
    {
        $this->checkAccess();

        // Direct execution — match with closures never executes the closures
        if ($type === 'al') {
            $import = AlExamImport::findOrFail($id);
            AlResult::where('import_id', $import->id)->delete();
            $import->delete();

        } elseif ($type === 'ol') {
            $import = OlExamImport::findOrFail($id);
            OlResult::where('import_id', $import->id)->delete();
            $import->delete();

        } elseif ($type === 'g5') {
            $import = Grade5ExamImport::findOrFail($id);
            $import->results()->delete();
            $import->delete();
        }

        return back()->with('delete_success', 'Import deleted successfully.');
    }
}