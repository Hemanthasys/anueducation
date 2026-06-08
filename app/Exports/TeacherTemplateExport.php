<?php

namespace App\Exports;

use App\Models\LookupValue;
use App\Models\School;
use App\Models\TeachingSubject;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class TeacherTemplateExport implements WithEvents, WithTitle
{
    private array $staffTypes;
    private array $appointmentTypes;
    private array $serviceGrades;
    private array $schools;
    private array $subjects;

    public function __construct()
    {
        $this->staffTypes       = LookupValue::where('category', 'staff_type')->where('is_active', true)->orderBy('order')->pluck('value')->toArray();
        $this->appointmentTypes = LookupValue::where('category', 'appointment_type')->where('is_active', true)->orderBy('order')->pluck('value')->toArray();
        $this->serviceGrades    = LookupValue::where('category', 'service_grade')->where('is_active', true)->orderBy('order')->pluck('value')->toArray();
        $this->schools          = School::where('is_active', true)->orderBy('name_en')->pluck('census_no')->toArray();
        $this->subjects         = TeachingSubject::where('is_active', true)->orderBy('level')->orderBy('name_en')->pluck('name_en')->toArray();
    }

    public function title(): string
    {
        return 'Teacher Data';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet      = $event->sheet->getDelegate();
                $workbook   = $sheet->getParent();

                // ── Create hidden reference sheets ────────────────────

                $this->createRefSheet($workbook, 'REF_StaffType',       $this->staffTypes);
                $this->createRefSheet($workbook, 'REF_AppointmentType', $this->appointmentTypes);
                $this->createRefSheet($workbook, 'REF_ServiceGrade',    $this->serviceGrades);
                $this->createRefSheet($workbook, 'REF_Schools',         $this->schools);
                $this->createRefSheet($workbook, 'REF_Subjects',        $this->subjects);

                // ── Header row ────────────────────────────────────────

                $headers = [
                    'A' => ['label' => 'name',              'title' => 'Full Name *',             'width' => 30],
                    'B' => ['label' => 'nic',               'title' => 'NIC Number *',             'width' => 18],
                    'C' => ['label' => 'gender',            'title' => 'Gender * (M/F)',           'width' => 12],
                    'D' => ['label' => 'birthday',          'title' => 'Birthday * (DD/MM/YYYY)',  'width' => 20],
                    'E' => ['label' => 'phone',             'title' => 'Phone',                    'width' => 16],
                    'F' => ['label' => 'email',             'title' => 'Email',                    'width' => 25],
                    'G' => ['label' => 'school_census_no',  'title' => 'School Census No *',       'width' => 20],
                    'H' => ['label' => 'staff_type',        'title' => 'Staff Type *',             'width' => 18],
                    'I' => ['label' => 'appointed_subject', 'title' => 'Appointed Subject',        'width' => 30],
                    'J' => ['label' => 'appointment_type',  'title' => 'Appointment Type',         'width' => 22],
                    'K' => ['label' => 'service_grade',     'title' => 'Service Grade',            'width' => 18],
                    'L' => ['label' => 'salary_slip_no',    'title' => 'Salary Slip No',           'width' => 18],
                    'M' => ['label' => 'appointed_date',    'title' => 'Appointed Date * (DD/MM/YYYY)', 'width' => 22],
                    'N' => ['label' => 'joined_school_date','title' => 'Joined School Date * (DD/MM/YYYY)', 'width' => 26],
                ];

                // Style header row
                foreach ($headers as $col => $info) {
                    $cell = $sheet->getCell("{$col}1");
                    $cell->setValue($info['title']);

                    $sheet->getStyle("{$col}1")->applyFromArray([
                        'font' => [
                            'bold'  => true,
                            'color' => ['rgb' => 'FFFFFF'],
                            'size'  => 11,
                            'name'  => 'Arial',
                        ],
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '1a3a6b'],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical'   => Alignment::VERTICAL_CENTER,
                            'wrapText'   => true,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color'       => ['rgb' => 'FFFFFF'],
                            ],
                        ],
                    ]);

                    $sheet->getColumnDimension($col)->setWidth($info['width']);
                }

                $sheet->getRowDimension(1)->setRowHeight(40);

                // ── Data rows styling + dropdowns (rows 2–501 = 500 rows) ──

                $dataRows = 500;

                // Zebra striping for readability
                for ($row = 2; $row <= $dataRows + 1; $row++) {
                    $bg = $row % 2 === 0 ? 'F8FAFF' : 'FFFFFF';
                    $sheet->getStyle("A{$row}:N{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'font' => ['name' => 'Arial', 'size' => 10],
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']],
                        ],
                    ]);
                }

                // Gender dropdown (M/F)
                $this->addInlineDropdown($sheet, 'C', 2, $dataRows + 1, '"M,F"');

                // School census_no dropdown from ref sheet
                $schoolCount = count($this->schools);
                if ($schoolCount > 0) {
                    $this->addRefDropdown($sheet, 'G', 2, $dataRows + 1, 'REF_Schools', $schoolCount);
                }

                // Staff type dropdown
                $staffCount = count($this->staffTypes);
                if ($staffCount > 0) {
                    $this->addRefDropdown($sheet, 'H', 2, $dataRows + 1, 'REF_StaffType', $staffCount);
                }

                // Appointed subject dropdown
                $subjectCount = count($this->subjects);
                if ($subjectCount > 0) {
                    $this->addRefDropdown($sheet, 'I', 2, $dataRows + 1, 'REF_Subjects', $subjectCount);
                }

                // Appointment type dropdown
                $apptCount = count($this->appointmentTypes);
                if ($apptCount > 0) {
                    $this->addRefDropdown($sheet, 'J', 2, $dataRows + 1, 'REF_AppointmentType', $apptCount);
                }

                // Service grade dropdown
                $gradeCount = count($this->serviceGrades);
                if ($gradeCount > 0) {
                    $this->addRefDropdown($sheet, 'K', 2, $dataRows + 1, 'REF_ServiceGrade', $gradeCount);
                }

                // ── Instructions row at bottom ────────────────────────

                $instrRow = $dataRows + 3;
                $sheet->mergeCells("A{$instrRow}:N{$instrRow}");
                $sheet->getCell("A{$instrRow}")->setValue(
                    '* Required fields. Date format: DD/MM/YYYY. NIC: 9 digits + V/X (old) or 12 digits (new). Do not modify column headers.'
                );
                $sheet->getStyle("A{$instrRow}")->applyFromArray([
                    'font'      => ['italic' => true, 'color' => ['rgb' => '6B7280'], 'size' => 9],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);

                // ── Freeze header row ─────────────────────────────────
                $sheet->freezePane('A2');

                // ── Set active sheet ──────────────────────────────────
                $workbook->setActiveSheetIndex(0);
            },
        ];
    }

    private function createRefSheet(\PhpOffice\PhpSpreadsheet\Spreadsheet $workbook, string $name, array $values): void
    {
        $refSheet = $workbook->createSheet();
        $refSheet->setTitle($name);

        foreach ($values as $i => $value) {
            $refSheet->getCell('A' . ($i + 1))->setValue($value);
        }

        // Hide the sheet
        $refSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);
    }

    private function addRefDropdown(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        string $col,
        int $startRow,
        int $endRow,
        string $refSheetName,
        int $count
    ): void {
        for ($row = $startRow; $row <= $endRow; $row++) {
            $validation = $sheet->getCell("{$col}{$row}")->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(true);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1("{$refSheetName}!\$A\$1:\$A\${$count}");
        }
    }

    private function addInlineDropdown(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        string $col,
        int $startRow,
        int $endRow,
        string $formula
    ): void {
        for ($row = $startRow; $row <= $endRow; $row++) {
            $validation = $sheet->getCell("{$col}{$row}")->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1($formula);
        }
    }
}