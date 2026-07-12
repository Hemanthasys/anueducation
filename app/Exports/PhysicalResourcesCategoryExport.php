<?php

namespace App\Exports;

use App\Models\School;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PhysicalResourcesCategoryExport implements WithEvents, WithTitle
{
    private string $category;
    private array  $filters;
    private array  $site;

    // category key => [title, [column field => label]]
    private const CATEGORIES = [
        'infrastructure' => [
            'title' => 'Infrastructure',
            'fields' => [
                'classrooms_count'              => 'Classrooms (Total)',
                'classrooms_usable'              => 'Classrooms (Usable)',
                'classrooms_unusable'            => 'Classrooms (Unusable)',
                'classrooms_to_repair'           => 'Classrooms (To Repair)',
                'classrooms_to_demolish'         => 'Classrooms (To Demolish)',
                'smart_classrooms_count'         => 'Smart Classrooms',
                'multi_story_buildings'          => 'Multi-Story Buildings',
                'library'                        => 'Library',
                'staff_room'                     => 'Staff Room',
                'administrative_block'           => 'Administrative Block',
                'canteen'                        => 'Canteen',
                'hostel'                         => 'Hostel',
                'hostel_count'                   => 'Hostel Buildings',
                'hostel_boys'                    => 'Hostel (Boys Capacity)',
                'hostel_girls'                   => 'Hostel (Girls Capacity)',
                'teachers_quarters'              => 'Teachers Quarters',
                'teachers_quarters_count'        => 'Teachers Quarters (Total)',
                'teachers_quarters_usable'       => 'Teachers Quarters (Usable)',
                'teachers_quarters_unusable'     => 'Teachers Quarters (Unusable)',
                'teachers_quarters_to_repair'    => 'Teachers Quarters (To Repair)',
                'teachers_quarters_to_demolish'  => 'Teachers Quarters (To Demolish)',
                'principals_quarters'            => 'Principals Quarters',
                'principals_quarters_count'      => 'Principals Quarters (Total)',
                'principals_quarters_usable'     => 'Principals Quarters (Usable)',
                'principals_quarters_unusable'   => 'Principals Quarters (Unusable)',
                'principals_quarters_to_repair'  => 'Principals Quarters (To Repair)',
                'principals_quarters_to_demolish'=> 'Principals Quarters (To Demolish)',
            ],
        ],
        'water_sanitation' => [
            'title' => 'Water, Sanitation & Utilities',
            'fields' => [
                'electricity'        => 'Electricity',
                'water_supply_type'  => 'Water Supply Type',
                'drinking_water'     => 'Drinking Water',
                'toilets_boys'       => 'Toilets (Boys)',
                'toilets_girls'      => 'Toilets (Girls)',
                'toilets_disabled'   => 'Toilets (Disabled Access)',
                'hand_washing'       => 'Hand Washing Facilities',
                'solar_power'        => 'Solar Power',
                'waste_management'   => 'Waste Management',
            ],
        ],
        'ict' => [
            'title' => 'ICT & Digital',
            'fields' => [
                'computer_lab'        => 'Computer Lab',
                'computers_count'     => 'Computers',
                'laptops_count'       => 'Laptops',
                'internet_access'     => 'Internet Access',
                'internet_speed'      => 'Internet Speed',
                'internet_type'       => 'Internet Type',
                'wifi'                => 'WiFi',
                'smart_boards_count'  => 'Smart Boards',
                'projectors_count'    => 'Projectors',
                'printers_count'      => 'Printers',
                'school_mis'          => 'School MIS',
                'cctv'                => 'CCTV',
                'digital_attendance'  => 'Digital Attendance',
            ],
        ],
        'science_technical' => [
            'title' => 'Science & Technical',
            'fields' => [
                'science_lab'          => 'Science Lab',
                'home_economics_unit'  => 'Home Economics Unit',
                'music_room'           => 'Music Room',
                'dancing_room'         => 'Dancing Room',
            ],
        ],
        'sports' => [
            'title' => 'Sports',
            'fields' => [
                'playground'       => 'Playground',
                'volleyball_court' => 'Volleyball Court',
                'netball_court'    => 'Netball Court',
                'athletic_track'   => 'Athletic Track',
            ],
        ],
        'security' => [
            'title' => 'Security & Safety',
            'fields' => [
                'cctv_monitoring'          => 'CCTV Monitoring',
                'security_fence'           => 'Security Fence',
                'fire_extinguishers'       => 'Fire Extinguishers',
                'emergency_exit_plan'      => 'Emergency Exit Plan',
                'disaster_preparedness'    => 'Disaster Preparedness',
                'student_safety_committee' => 'Student Safety Committee',
            ],
        ],
        'transport' => [
            'title' => 'Transport & Accessibility',
            'fields' => [
                'access_road_condition'    => 'Access Road Condition',
                'public_transport_access'  => 'Public Transport Access',
                'school_van'               => 'School Van',
                'disabled_accessibility'   => 'Disabled Accessibility',
            ],
        ],
    ];

    public function __construct(string $category, array $filters, array $site)
    {
        $this->category = $category;
        $this->filters  = $filters;
        $this->site     = $site;
    }

    public static function categoryKeys(): array
    {
        return array_keys(self::CATEGORIES);
    }

    public function title(): string
    {
        return self::CATEGORIES[$this->category]['title'] ?? 'Physical Resources';
    }

    private function formatValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }
        if ($value === null || $value === '') {
            return '—';
        }
        if (is_string($value)) {
            return ucfirst(str_replace('_', ' ', $value));
        }
        return (string) $value;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet      = $event->sheet->getDelegate();
                $divisionId = $this->filters['division_id'] ?? null;
                $schoolId   = $this->filters['school_id']   ?? null;
                $config     = self::CATEGORIES[$this->category] ?? ['title' => 'Physical Resources', 'fields' => []];
                $fields     = $config['fields'];
                $lastCol    = $this->columnLetter(count($fields) + 2); // School + Division + fields

                // ── Branding rows ──────────────────────────────────
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->getCell('A1')->setValue($this->site['site_name_en']);
                $sheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1e1b4b']], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);
                $sheet->mergeCells("A2:{$lastCol}2");
                $sheet->getCell('A2')->setValue($this->site['site_name_si']);
                $sheet->getStyle('A2')->applyFromArray(['font' => ['size' => 11, 'color' => ['rgb' => '4b5563']], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);
                $sheet->mergeCells("A3:{$lastCol}3");
                $sheet->getCell('A3')->setValue('Physical Resources — ' . $config['title']);
                $sheet->getStyle('A3')->applyFromArray(['font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '4f46e5']], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);
                $sheet->mergeCells("A4:{$lastCol}4");
                $sheet->getCell('A4')->setValue('Generated by: ' . $this->site['generated_by'] . ' | ' . $this->site['generated_at']);
                $sheet->getStyle('A4')->applyFromArray(['font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '9ca3af']], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);

                // ── Header row ──────────────────────────────────────
                $row = 6;
                $sheet->getCell("A{$row}")->setValue('School');
                $sheet->getCell("B{$row}")->setValue('Division');
                $col = 2;
                foreach ($fields as $label) {
                    $col++;
                    $sheet->getCellByColumnAndRow($col, $row)->setValue($label);
                }
                $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff'], 'size' => 10],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4f46e5']],
                ]);
                $row++;

                // ── Data rows ─────────────────────────────────────
                $schools = School::with(['division', 'physicalResources'])
                    ->where('is_active', true)
                    ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
                    ->when($schoolId,   fn($q) => $q->where('id', $schoolId))
                    ->orderBy('name_en')->get();

                foreach ($schools as $school) {
                    $res = $school->physicalResources;

                    $sheet->getCell("A{$row}")->setValue($school->name_en);
                    $sheet->getCell("B{$row}")->setValue($school->division?->name_en ?? '—');

                    $col = 2;
                    foreach (array_keys($fields) as $field) {
                        $col++;
                        $value = $res ? $this->formatValue($res->$field) : 'Not Submitted';
                        $sheet->getCellByColumnAndRow($col, $row)->setValue($value);
                    }

                    $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                        'font'    => ['size' => 10],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'e5e7eb']]],
                    ]);
                    $row++;
                }

                $sheet->getColumnDimension('A')->setWidth(30);
                $sheet->getColumnDimension('B')->setWidth(20);
                foreach (range(3, $col) as $c) {
                    $sheet->getColumnDimensionByColumn($c)->setWidth(18);
                }
                $sheet->freezePane('A7');
            },
        ];
    }

    private function columnLetter(int $index): string
    {
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index);
    }
}
