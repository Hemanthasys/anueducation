<?php

namespace App\Exports;

use App\Models\Project;
use App\Models\School;
use App\Models\ProjectAssignment;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ProjectsAnalysisExport implements WithEvents, WithTitle
{
    private array $filters;
    private array $site;

    public function __construct(array $filters, array $site)
    {
        $this->filters = $filters;
        $this->site    = $site;
    }

    public function title(): string { return 'Projects Analysis'; }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $statusFilter = $this->filters['status'] ?? null;
                $typeFilter   = $this->filters['type']   ?? null;
                $divisionId   = $this->filters['division_id'] ?? null;

                // ── Branding header ───────────────────────────────────
                $this->writeBrandingHeader($sheet);

                // ── Section 1: Project Summary ────────────────────────
                $row = 6;
                $this->writeSectionTitle($sheet, $row, 'A', 'H', 'PROJECT SUMMARY');
                $row++;
                $this->writeHeadingRow($sheet, $row, ['A' => 'Reference No', 'B' => 'Title', 'C' => 'Type', 'D' => 'Status', 'E' => 'Budget (Rs.)', 'F' => 'Allocated (Rs.)', 'G' => 'Schools', 'H' => 'Progress %']);
                $row++;

                $projects = Project::with(['fundingSource', 'assignments.school.division', 'milestones'])
                    ->when($statusFilter, fn($q) => $q->where('status', $statusFilter))
                    ->when($typeFilter,   fn($q) => $q->where('project_type', $typeFilter))
                    ->orderByDesc('created_at')->get();

                foreach ($projects as $p) {
                    $assignments = $divisionId
                        ? $p->assignments->filter(fn($a) => $a->school?->division_id == $divisionId)
                        : $p->assignments;

                    $allocated = $assignments->sum('allocated_budget');
                    $progress  = $p->overall_progress ?? 0;

                    $bg = $row % 2 === 0 ? 'f0f4ff' : 'ffffff';
                    $values = [
                        'A' => $p->reference_no,
                        'B' => $p->title,
                        'C' => ucfirst(str_replace('_', ' ', $p->project_type ?? '')),
                        'D' => ucfirst($p->status ?? ''),
                        'E' => (float)$p->budget,
                        'F' => (float)$allocated,
                        'G' => $assignments->count(),
                        'H' => $progress . '%',
                    ];
                    foreach ($values as $col => $val) {
                        $sheet->getCell("{$col}{$row}")->setValue($val);
                        $sheet->getStyle("{$col}{$row}")->applyFromArray([
                            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                            'font'    => ['size' => 10],
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'e5e7eb']]],
                        ]);
                        if (in_array($col, ['E', 'F'])) {
                            $sheet->getStyle("{$col}{$row}")->getNumberFormat()
                                ->setFormatCode('#,##0.00');
                        }
                    }
                    $row++;
                }

                // ── Section 2: School Assignments ─────────────────────
                $row += 2;
                $this->writeSectionTitle($sheet, $row, 'A', 'F', 'SCHOOL ASSIGNMENTS');
                $row++;
                $this->writeHeadingRow($sheet, $row, ['A' => 'School', 'B' => 'Division', 'C' => 'Projects', 'D' => 'Active', 'E' => 'Completed', 'F' => 'Allocated Budget (Rs.)']);
                $row++;

                $assignedSchoolIds = ProjectAssignment::pluck('school_id')->unique()->toArray();
                $allAssignments    = ProjectAssignment::all()->groupBy('school_id');
                School::with('division')
                    ->where('is_active', true)
                    ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
                    ->whereIn('id', $assignedSchoolIds)
                    ->orderBy('name_en')->get()
                    ->each(function ($school) use ($sheet, &$row, $allAssignments) {
                        $assignments = $allAssignments->get($school->id, collect());
                        $bg = $row % 2 === 0 ? 'f0f4ff' : 'ffffff';
                        foreach ([
                            'A' => $school->name_en,
                            'B' => $school->division?->name_en ?? '',
                            'C' => $assignments->count(),
                            'D' => $assignments->where('status', 'active')->count(),
                            'E' => $assignments->where('status', 'completed')->count(),
                            'F' => (float)$assignments->sum('allocated_budget'),
                        ] as $col => $val) {
                            $sheet->getCell("{$col}{$row}")->setValue($val);
                            $sheet->getStyle("{$col}{$row}")->applyFromArray([
                                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                                'font'    => ['size' => 10],
                                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'e5e7eb']]],
                            ]);
                            if ($col === 'F') {
                                $sheet->getStyle("{$col}{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
                            }
                        }
                        $row++;
                    });

                // ── Section 3: Schools Without Projects ───────────────
                $row += 2;
                $this->writeSectionTitle($sheet, $row, 'A', 'C', 'SCHOOLS WITHOUT PROJECTS');
                $row++;
                $this->writeHeadingRow($sheet, $row, ['A' => 'School', 'B' => 'Division', 'C' => 'Type']);
                $row++;

                School::with('division')
                    ->where('is_active', true)
                    ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
                    ->whereNotIn('id', $assignedSchoolIds)
                    ->orderBy('name_en')->get()
                    ->each(function ($school) use ($sheet, &$row) {
                        $bg = $row % 2 === 0 ? 'fff7ed' : 'ffffff';
                        foreach (['A' => $school->name_en, 'B' => $school->division?->name_en ?? '', 'C' => $school->type ?? ''] as $col => $val) {
                            $sheet->getCell("{$col}{$row}")->setValue($val);
                            $sheet->getStyle("{$col}{$row}")->applyFromArray([
                                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                                'font'    => ['size' => 10],
                                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'e5e7eb']]],
                            ]);
                        }
                        $row++;
                    });

                // ── Column widths ─────────────────────────────────────
                foreach (['A' => 30, 'B' => 25, 'C' => 16, 'D' => 14, 'E' => 18, 'F' => 18, 'G' => 12, 'H' => 12] as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                $sheet->freezePane('A5');
            },
        ];
    }

    private function writeBrandingHeader($sheet): void
    {
        $sheet->mergeCells('A1:H1');
        $sheet->getCell('A1')->setValue($this->site['site_name_en']);
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1e1b4b']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->mergeCells('A2:H2');
        $sheet->getCell('A2')->setValue($this->site['site_name_si']);
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['size' => 11, 'color' => ['rgb' => '4b5563']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->mergeCells('A3:H3');
        $sheet->getCell('A3')->setValue('Projects Analysis Report — ' . $this->site['generated_at']);
        $sheet->getStyle('A3')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '4f46e5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->mergeCells('A4:H4');
        $sheet->getCell('A4')->setValue('Generated by: ' . $this->site['generated_by'] . ' | ' . $this->site['address']);
        $sheet->getStyle('A4')->applyFromArray([
            'font'      => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '9ca3af']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
    }

    private function writeSectionTitle($sheet, int $row, string $fromCol, string $toCol, string $title): void
    {
        $sheet->mergeCells("{$fromCol}{$row}:{$toCol}{$row}");
        $sheet->getCell("{$fromCol}{$row}")->setValue($title);
        $sheet->getStyle("{$fromCol}{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4f46e5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
    }

    private function writeHeadingRow($sheet, int $row, array $cols): void
    {
        foreach ($cols as $col => $label) {
            $sheet->getCell("{$col}{$row}")->setValue($label);
            $sheet->getStyle("{$col}{$row}")->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['rgb' => 'ffffff'], 'size' => 10],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '6366f1']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'ffffff']]],
            ]);
        }
    }
}