<?php

namespace App\Exports;

use App\Models\Division;
use App\Models\School;
use App\Models\Teacher;
use App\Models\SchoolStaff;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class HrAnalysisExport implements WithEvents, WithTitle
{
    // Format a cell as text to prevent scientific notation
    private function setTextFormat(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, string $col, int $row): void
    {
        $sheet->getStyle("{$col}{$row}")
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
    }

    // Human-readable duration: "5 years 3 months 12 days"
    private function humanDuration(\Carbon\Carbon $from, \Carbon\Carbon $to = null): string
    {
        $to   = $to ?? now();
        $diff = $from->diff($to);
        $parts = [];
        if ($diff->y > 0) $parts[] = $diff->y . ($diff->y === 1 ? ' year'  : ' years');
        if ($diff->m > 0) $parts[] = $diff->m . ($diff->m === 1 ? ' month' : ' months');
        if ($diff->d > 0) $parts[] = $diff->d . ($diff->d === 1 ? ' day'   : ' days');
        return implode(', ', $parts) ?: '0 days';
    }

    private string $section;
    private array  $filters;
    private array  $site;

    public function __construct(string $section, array $filters, array $site)
    {
        $this->section = $section;
        $this->filters = $filters;
        $this->site    = $site;
    }

    public function title(): string
    {
        return match($this->section) {
            'by-division'   => 'By Division',
            'by-grade'      => 'By Service Grade',
            'by-appointment'=> 'By Appointment Type',
            'by-status'     => 'By Status',
            'gender'        => 'Gender Breakdown',
            'subjects'      => 'By Subject',
            'attached'      => 'Attached Teachers',
            'on-leave'      => 'Teachers on Leave',
            'principals'    => 'Schools Without Principal',
            'non-academic'  => 'Non-Academic Staff',
            'retired'       => 'Retired Teachers',
            'retirement-due'=> 'Approaching Retirement',
            'data-quality'  => 'Data Quality',
            'probation'     => 'Probation Due for Permanency',
            'five-year'     => '5-Year Same School Transfer Eligibility',
            default         => 'HR Analysis',
        };
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet    = $event->sheet->getDelegate();
                $divId    = $this->filters['division_id'] ?? null;
                $schoolId = $this->filters['school_id']   ?? null;

                // ── Header branding rows ──────────────────────────────
                $this->writeHeader($sheet, $this->title());

                // ── Data rows ─────────────────────────────────────────
                $startRow = 5;
                match($this->section) {
                    'by-division'    => $this->writeByDivision($sheet, $startRow, $divId),
                    'by-grade'       => $this->writeByGrade($sheet, $startRow, $divId, $schoolId),
                    'by-appointment' => $this->writeByAppointment($sheet, $startRow, $divId, $schoolId),
                    'by-status'      => $this->writeByStatus($sheet, $startRow, $divId, $schoolId),
                    'gender'         => $this->writeGender($sheet, $startRow, $divId, $schoolId),
                    'subjects'       => $this->writeSubjects($sheet, $startRow, $divId, $schoolId),
                    'attached'       => $this->writeAttached($sheet, $startRow, $divId, $schoolId),
                    'on-leave'       => $this->writeOnLeave($sheet, $startRow, $divId, $schoolId),
                    'principals'     => $this->writePrincipals($sheet, $startRow, $divId),
                    'non-academic'   => $this->writeNonAcademic($sheet, $startRow, $divId, $schoolId),
                    'retired'        => $this->writeRetired($sheet, $startRow, $divId, $schoolId),
                    'retirement-due' => $this->writeRetirementDue($sheet, $startRow, $divId, $schoolId),
                    'data-quality'   => $this->writeDataQuality($sheet, $startRow, $divId, $schoolId),
                    'probation'      => $this->writeProbation($sheet, $startRow, $divId, $schoolId),
                    'five-year'      => $this->writeFiveYear($sheet, $startRow, $divId, $schoolId),
                    default          => null,
                };
            },
        ];
    }

    // ── Branding header ───────────────────────────────────────────────
    private function writeHeader($sheet, string $title): void
    {
        $sheet->mergeCells('A1:F1');
        $sheet->getCell('A1')->setValue($this->site['site_name_en']);
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1e1b4b']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells('A2:F2');
        $sheet->getCell('A2')->setValue($this->site['site_name_si']);
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['size' => 11, 'color' => ['rgb' => '4b5563']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells('A3:F3');
        $sheet->getCell('A3')->setValue($title . ' — ' . $this->site['generated_at']);
        $sheet->getStyle('A3')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '4f46e5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells('A4:F4');
        $sheet->getCell('A4')->setValue('Generated by: ' . $this->site['generated_by'] . ' | ' . $this->site['address']);
        $sheet->getStyle('A4')->applyFromArray([
            'font'      => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '9ca3af']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(22);
        $sheet->getRowDimension(2)->setRowHeight(18);
        $sheet->getRowDimension(3)->setRowHeight(18);
        $sheet->getRowDimension(4)->setRowHeight(16);
    }

    // ── Styled heading row helper ─────────────────────────────────────
    private function writeHeadingRow($sheet, int $row, array $cols): void
    {
        foreach ($cols as $i => $label) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->getCell($col . $row)->setValue($label);
            $sheet->getStyle($col . $row)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff'], 'size' => 10],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4f46e5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'ffffff']]],
            ]);
            $sheet->getColumnDimensionByColumn($i + 1)->setWidth(20);
        }
        $sheet->getRowDimension($row)->setRowHeight(20);
    }

    private function writeDataRow($sheet, int $row, array $values, bool $zebra = false): void
    {
        $bg = $zebra ? 'f0f4ff' : 'ffffff';
        foreach ($values as $i => $val) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->getCell($col . $row)->setValue($val);
            $sheet->getStyle($col . $row)->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'font'      => ['size' => 10],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'e5e7eb']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
        }
    }

    // ── Section writers ───────────────────────────────────────────────
    private function schoolIdsForDivision(?int $divId): ?array
    {
        if (!$divId) return null;
        return School::where('division_id', $divId)->where('is_active', true)->pluck('id')->toArray();
    }

    private function baseTeacher(?int $divId, ?int $schoolId)
    {
        $schoolIds = $this->schoolIdsForDivision($divId);
        return Teacher::query()
            ->when($schoolIds,  fn($q) => $q->whereIn('school_id', $schoolIds))
            ->when($schoolId,   fn($q) => $q->where('school_id', $schoolId))
            ->where('is_active', true);
    }

    private function writeByDivision($sheet, int $startRow, ?int $divId): void
    {
        $this->writeHeadingRow($sheet, $startRow, ['Division', 'Teachers', 'Vice Principals', 'Non-Academic', 'On Leave', 'Attached', 'Schools', 'No Principal']);
        $row = $startRow + 1;
        Division::when($divId, fn($q) => $q->where('id', $divId))->orderBy('name_en')->get()
            ->each(function ($div) use ($sheet, &$row) {
                $ids = School::where('division_id', $div->id)->where('is_active', true)->pluck('id');
                $this->writeDataRow($sheet, $row, [
                    $div->name_en,
                    Teacher::whereIn('school_id', $ids)->where('staff_type', 'teacher')->where('is_active', true)->count(),
                    Teacher::whereIn('school_id', $ids)->where('staff_type', 'vice_principal')->where('is_active', true)->count(),
                    SchoolStaff::whereIn('school_id', $ids)->where('is_active', true)->count(),
                    Teacher::whereIn('school_id', $ids)->whereIn('status', ['maternity_leave','medical_leave','other_leave'])->count(),
                    Teacher::whereIn('school_id', $ids)->where('is_attached', true)->where('is_active', true)->count(),
                    $ids->count(),
                    School::whereIn('id', $ids)->whereNull('principal_id')->count(),
                ], $row % 2 === 0);
                $row++;
            });
    }

    private function writeByGrade($sheet, int $startRow, ?int $divId, ?int $schoolId): void
    {
        $this->writeHeadingRow($sheet, $startRow, ['Service Grade', 'Male', 'Female', 'Total']);
        $row = $startRow + 1;
        $schoolIds = $this->schoolIdsForDivision($divId);
        DB::table('teachers')
            ->when($schoolIds, fn($q) => $q->whereIn('school_id', $schoolIds))
            ->when($schoolId,  fn($q) => $q->where('school_id', $schoolId))
            ->where('is_active', true)->whereNotNull('service_grade')
            ->select('service_grade', 'gender', DB::raw('COUNT(*) as count'))
            ->groupBy('service_grade', 'gender')->get()
            ->groupBy('service_grade')
            ->each(function ($rows, $grade) use ($sheet, &$row) {
                $this->writeDataRow($sheet, $row, [
                    str_replace('_', ' ', $grade),
                    $rows->where('gender', 'M')->sum('count'),
                    $rows->where('gender', 'F')->sum('count'),
                    $rows->sum('count'),
                ], $row % 2 === 0);
                $row++;
            });
    }

    private function writeByAppointment($sheet, int $startRow, ?int $divId, ?int $schoolId): void
    {
        $this->writeHeadingRow($sheet, $startRow, ['Appointment Type', 'Count', 'Percentage']);
        $row       = $startRow + 1;
        $schoolIds = $this->schoolIdsForDivision($divId);
        $total     = (clone $this->baseTeacher($divId, $schoolId))->count();
        DB::table('teachers')
            ->when($schoolIds, fn($q) => $q->whereIn('school_id', $schoolIds))
            ->when($schoolId,  fn($q) => $q->where('school_id', $schoolId))
            ->where('is_active', true)->whereNotNull('appointment_type')
            ->select('appointment_type', DB::raw('COUNT(*) as count'))
            ->groupBy('appointment_type')->orderByDesc('count')->get()
            ->each(function ($r) use ($sheet, &$row, $total) {
                $pct = $total > 0 ? round($r->count / $total * 100, 1) : 0;
                $this->writeDataRow($sheet, $row, [ucfirst(str_replace('_', ' ', $r->appointment_type)), $r->count, $pct . '%'], $row % 2 === 0);
                $row++;
            });
    }

    private function writeByStatus($sheet, int $startRow, ?int $divId, ?int $schoolId): void
    {
        $this->writeHeadingRow($sheet, $startRow, ['Status', 'Count']);
        $row       = $startRow + 1;
        $schoolIds = $this->schoolIdsForDivision($divId);
        DB::table('teachers')
            ->when($schoolIds, fn($q) => $q->whereIn('school_id', $schoolIds))
            ->when($schoolId,  fn($q) => $q->where('school_id', $schoolId))
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')->orderByDesc('count')->get()
            ->each(function ($r) use ($sheet, &$row) {
                $this->writeDataRow($sheet, $row, [ucfirst(str_replace('_', ' ', $r->status ?? 'unknown')), $r->count], $row % 2 === 0);
                $row++;
            });
    }

    private function writeGender($sheet, int $startRow, ?int $divId, ?int $schoolId): void
    {
        $this->writeHeadingRow($sheet, $startRow, ['Gender', 'Count', 'Percentage']);
        $row       = $startRow + 1;
        $schoolIds = $this->schoolIdsForDivision($divId);
        $total     = (clone $this->baseTeacher($divId, $schoolId))->count();
        DB::table('teachers')
            ->when($schoolIds, fn($q) => $q->whereIn('school_id', $schoolIds))
            ->when($schoolId,  fn($q) => $q->where('school_id', $schoolId))
            ->where('is_active', true)
            ->select('gender', DB::raw('COUNT(*) as count'))
            ->groupBy('gender')->get()
            ->each(function ($r) use ($sheet, &$row, $total) {
                $label = match($r->gender) { 'M' => 'Male', 'F' => 'Female', default => 'Unknown' };
                $pct   = $total > 0 ? round($r->count / $total * 100, 1) : 0;
                $this->writeDataRow($sheet, $row, [$label, $r->count, $pct . '%'], $row % 2 === 0);
                $row++;
            });
    }

    private function writeSubjects($sheet, int $startRow, ?int $divId, ?int $schoolId): void
    {
        $this->writeHeadingRow($sheet, $startRow, ['#', 'Subject', 'Teachers']);
        $row       = $startRow + 1;
        $schoolIds = $this->schoolIdsForDivision($divId);
        DB::table('teachers')
            ->join('teaching_subjects', 'teachers.appointed_subject_id', '=', 'teaching_subjects.id')
            ->when($schoolIds, fn($q) => $q->whereIn('teachers.school_id', $schoolIds))
            ->when($schoolId,  fn($q) => $q->where('teachers.school_id', $schoolId))
            ->where('teachers.is_active', true)->whereNotNull('teachers.appointed_subject_id')
            ->select('teaching_subjects.name_en', DB::raw('COUNT(*) as count'))
            ->groupBy('teachers.appointed_subject_id', 'teaching_subjects.name_en', 'teaching_subjects.name_si')
            ->orderByDesc('count')->get()
            ->each(function ($r, $i) use ($sheet, &$row) {
                $this->writeDataRow($sheet, $row, [$i + 1, $r->name_en, $r->count], $row % 2 === 0);
                $row++;
            });
    }

    private function writeAttached($sheet, int $startRow, ?int $divId, ?int $schoolId): void
    {
        $this->writeHeadingRow($sheet, $startRow, ['Name', 'NIC', 'Salary School', 'Attached To']);
        $row       = $startRow + 1;
        $schoolIds = $this->schoolIdsForDivision($divId);
        Teacher::with(['school', 'attachedSchool'])
            ->when($schoolIds, fn($q) => $q->whereIn('school_id', $schoolIds))
            ->when($schoolId,  fn($q) => $q->where('school_id', $schoolId))
            ->where('is_attached', true)->where('is_active', true)->orderBy('name')->get()
            ->each(function ($t) use ($sheet, &$row) {
                $this->writeDataRow($sheet, $row, [$t->name, $t->nic ?? '', $t->school?->name_en ?? '', $t->attachedSchool?->name_en ?? ''], $row % 2 === 0);
                $row++;
            });
    }

    private function writeOnLeave($sheet, int $startRow, ?int $divId, ?int $schoolId): void
    {
        $this->writeHeadingRow($sheet, $startRow, ['Name', 'NIC', 'School', 'Leave Type', 'Since', 'Note']);
        $row       = $startRow + 1;
        $schoolIds = $this->schoolIdsForDivision($divId);
        Teacher::with(['school'])
            ->when($schoolIds, fn($q) => $q->whereIn('school_id', $schoolIds))
            ->when($schoolId,  fn($q) => $q->where('school_id', $schoolId))
            ->whereIn('status', ['maternity_leave','medical_leave','other_leave'])
            ->orderBy('status_changed_at','desc')->get()
            ->each(function ($t) use ($sheet, &$row) {
                $status = is_string($t->status) ? $t->status : ($t->status?->value ?? '');
                $this->writeDataRow($sheet, $row, [
                    $t->name, $t->nic ?? '', $t->school?->name_en ?? '',
                    ucfirst(str_replace('_', ' ', $status)),
                    $t->status_changed_at?->format('d M Y') ?? '',
                    $t->status_note ?? '',
                ], $row % 2 === 0);
                $this->setTextFormat($sheet, 'B', $row);
                $row++;
            });
    }

    private function writePrincipals($sheet, int $startRow, ?int $divId): void
    {
        $this->writeHeadingRow($sheet, $startRow, ['School', 'Division', 'Type']);
        $row = $startRow + 1;
        School::with('division')->where('is_active', true)->whereNull('principal_id')
            ->when($divId, fn($q) => $q->where('division_id', $divId))
            ->orderBy('name_en')->get()
            ->each(function ($s) use ($sheet, &$row) {
                $this->writeDataRow($sheet, $row, [$s->name_en, $s->division?->name_en ?? '', $s->type ?? ''], $row % 2 === 0);
                $row++;
            });
    }

    private function writeNonAcademic($sheet, int $startRow, ?int $divId, ?int $schoolId): void
    {
        $this->writeHeadingRow($sheet, $startRow, ['Role', 'Count']);
        $row       = $startRow + 1;
        $schoolIds = $this->schoolIdsForDivision($divId);
        DB::table('school_staff')
            ->when($schoolIds, fn($q) => $q->whereIn('school_id', $schoolIds))
            ->when($schoolId,  fn($q) => $q->where('school_id', $schoolId))
            ->where('is_active', true)->whereNotNull('non_academic_role')
            ->select('non_academic_role', DB::raw('COUNT(*) as count'))
            ->groupBy('non_academic_role')->orderByDesc('count')->get()
            ->each(function ($r) use ($sheet, &$row) {
                $this->writeDataRow($sheet, $row, [ucfirst(str_replace('_', ' ', $r->non_academic_role)), $r->count], $row % 2 === 0);
                $row++;
            });
    }

    private function writeRetired($sheet, int $startRow, ?int $divId, ?int $schoolId): void
    {
        $this->writeHeadingRow($sheet, $startRow, ['Name', 'NIC', 'School', 'Service Grade', 'Retired On']);
        $row       = $startRow + 1;
        $schoolIds = $this->schoolIdsForDivision($divId);
        Teacher::with(['school'])
            ->when($schoolIds, fn($q) => $q->whereIn('school_id', $schoolIds))
            ->when($schoolId,  fn($q) => $q->where('school_id', $schoolId))
            ->where('status', 'retired')->orderBy('status_changed_at', 'desc')->get()
            ->each(function ($t) use ($sheet, &$row) {
                $this->writeDataRow($sheet, $row, [
                    $t->name, $t->nic ?? '', $t->school?->name_en ?? '',
                    str_replace('_', ' ', $t->service_grade ?? ''),
                    $t->status_changed_at?->format('d M Y') ?? '',
                ], $row % 2 === 0);
                $this->setTextFormat($sheet, 'B', $row);
                $row++;
            });
    }

    private function writeRetirementDue($sheet, int $startRow, ?int $divId, ?int $schoolId): void
    {
        $this->writeHeadingRow($sheet, $startRow, ['Name', 'NIC', 'School', 'Service Grade', 'Age', 'Retirement Date', 'Years Left']);
        $row       = $startRow + 1;
        $schoolIds = $this->schoolIdsForDivision($divId);
        Teacher::with(['school'])
            ->when($schoolIds, fn($q) => $q->whereIn('school_id', $schoolIds))
            ->when($schoolId,  fn($q) => $q->where('school_id', $schoolId))
            ->where('is_active', true)->whereNotNull('birthday')
            ->whereRaw('TIMESTAMPDIFF(YEAR, birthday, NOW()) BETWEEN 55 AND 59')
            ->orderByRaw('DATE_ADD(birthday, INTERVAL 60 YEAR) ASC')->get()
            ->each(function ($t) use ($sheet, &$row) {
                $retDate = \Carbon\Carbon::parse($t->birthday)->addYears(60);
                $this->writeDataRow($sheet, $row, [
                    $t->name, $t->nic ?? '', $t->school?->name_en ?? '',
                    str_replace('_', ' ', $t->service_grade ?? ''),
                    \Carbon\Carbon::parse($t->birthday)->age,
                    $retDate->format('d M Y'),
                    max(0, $retDate->diffInYears(now())),
                ], $row % 2 === 0);
                $this->setTextFormat($sheet, 'B', $row);
                $row++;
            });
    }

    private function writeDataQuality($sheet, int $startRow, ?int $divId, ?int $schoolId): void
    {
        $this->writeHeadingRow($sheet, $startRow, ['Field', 'Total', 'Filled', 'Missing', 'Completion %']);
        $row       = $startRow + 1;
        $schoolIds = $this->schoolIdsForDivision($divId);
        $fields    = ['nic','phone','email','birthday','photo','salary_slip_no','appointed_date','joined_school_date','service_grade','appointment_type','appointed_subject_id','gender'];
        $total     = Teacher::when($schoolIds, fn($q) => $q->whereIn('school_id', $schoolIds))
            ->when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->where('is_active', true)->count();

        foreach ($fields as $field) {
            $missing = Teacher::when($schoolIds, fn($q) => $q->whereIn('school_id', $schoolIds))
                ->when($schoolId, fn($q) => $q->where('school_id', $schoolId))
                ->where('is_active', true)->whereNull($field)->count();
            $filled  = $total - $missing;
            $pct     = $total > 0 ? round($filled / $total * 100) : 0;
            $this->writeDataRow($sheet, $row, [
                ucfirst(str_replace(['_id','_'], ['', ' '], $field)),
                $total, $filled, $missing, $pct . '%',
            ], $row % 2 === 0);
            $row++;
        }
    }

    private function writeProbation($sheet, int $startRow, ?int $divId, ?int $schoolId): void
    {
        $this->writeHeadingRow($sheet, $startRow, ['Name', 'NIC', 'School', 'Division', 'Appt. Type', 'Appointed Date', 'Joined School', 'Due Date (3yr)', 'Service Duration']);
        $row       = $startRow + 1;
        $schoolIds = $this->schoolIdsForDivision($divId);
        Teacher::with(['school.division'])
            ->when($schoolIds, fn($q) => $q->whereIn('school_id', $schoolIds))
            ->when($schoolId,  fn($q) => $q->where('school_id', $schoolId))
            ->where('is_active', true)
            ->where(fn($q) => $q->where('appointment_type', '!=', 'permanent')->orWhereNull('appointment_type'))
            ->whereNotNull('appointed_date')
            ->where('appointed_date', '>=', now()->subYears(40)->toDateString())
            ->where('appointed_date', '<=', now()->toDateString())
            ->whereRaw('YEAR(DATE_ADD(appointed_date, INTERVAL 3 YEAR)) = YEAR(NOW())')
            ->orderByRaw('DATE_ADD(appointed_date, INTERVAL 3 YEAR) ASC')
            ->get()
            ->each(function ($t) use ($sheet, &$row) {
                $dueDate  = \Carbon\Carbon::parse($t->appointed_date)->addYears(3)->format('d M Y');
                $duration = $this->humanDuration(\Carbon\Carbon::parse($t->appointed_date));
                $this->writeDataRow($sheet, $row, [
                    $t->name, $t->nic ?? '',
                    $t->school?->name_en ?? '', $t->school?->division?->name_en ?? '',
                    ucfirst(str_replace('_', ' ', $t->appointment_type ?? '')),
                    $t->appointed_date ? \Carbon\Carbon::parse($t->appointed_date)->format('d M Y') : '',
                    $t->joined_school_date ? \Carbon\Carbon::parse($t->joined_school_date)->format('d M Y') : '',
                    $dueDate,
                    $duration,
                ], $row % 2 === 0);
                // Force NIC column as text to prevent scientific notation
                $this->setTextFormat($sheet, 'B', $row);
                $row++;
            });
    }

    private function writeFiveYear($sheet, int $startRow, ?int $divId, ?int $schoolId): void
    {
        $year = request('transfer_year') ? (int)request('transfer_year') : now()->year;
        $this->writeHeadingRow($sheet, $startRow, ['Name', 'NIC', 'Service Grade', 'Salary School', 'Division', 'Joined School Date', '5 Years Completed', 'Years at School']);
        $row       = $startRow + 1;
        $schoolIds = $this->schoolIdsForDivision($divId);
        Teacher::with(['school.division'])
            ->when($schoolIds, fn($q) => $q->whereIn('school_id', $schoolIds))
            ->when($schoolId,  fn($q) => $q->where('school_id', $schoolId))
            ->where('is_active', true)
            ->whereNotNull('joined_school_date')
            ->whereRaw('DATE_ADD(joined_school_date, INTERVAL 5 YEAR) <= ?', ["{$year}-12-31"])
            ->orderByRaw('joined_school_date ASC')
            ->get()
            ->each(function ($t) use ($sheet, &$row) {
                $fiveYearDate  = \Carbon\Carbon::parse($t->joined_school_date)->addYears(5)->format('d M Y');
                $yearsAtSchool = $this->humanDuration(\Carbon\Carbon::parse($t->joined_school_date));
                $this->writeDataRow($sheet, $row, [
                    $t->name, $t->nic ?? '',
                    str_replace('_', ' ', $t->service_grade ?? ''),
                    $t->school?->name_en ?? '', $t->school?->division?->name_en ?? '',
                    \Carbon\Carbon::parse($t->joined_school_date)->format('d M Y'),
                    $fiveYearDate,
                    $yearsAtSchool,
                ], $row % 2 === 0);
                // Force NIC column as text to prevent scientific notation
                $this->setTextFormat($sheet, 'B', $row);
                $row++;
            });
    }
}