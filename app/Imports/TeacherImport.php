<?php

namespace App\Imports;

use App\Models\LookupValue;
use App\Models\School;
use App\Models\Teacher;
use App\Models\TeachingSubject;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TeacherImport implements ToCollection, WithHeadingRow
{
    public array $results = [
        'created' => 0,
        'skipped' => 0,
        'errors'  => [],
    ];

    private array $staffTypes;
    private array $appointmentTypes;
    private array $serviceGrades;
    private array $schools;
    private array $subjects;

    public function __construct()
    {
        $this->staffTypes       = LookupValue::where('category', 'staff_type')->where('is_active', true)->pluck('value', 'value')->toArray();
        $this->appointmentTypes = LookupValue::where('category', 'appointment_type')->where('is_active', true)->pluck('value', 'value')->toArray();
        $this->serviceGrades    = LookupValue::where('category', 'service_grade')->where('is_active', true)->pluck('value', 'value')->toArray();
        $this->schools          = School::where('is_active', true)->pluck('id', 'census_no')->toArray();
        $this->subjects         = TeachingSubject::where('is_active', true)->pluck('id', 'name_en')->toArray();
    }

    public function collection(Collection $rows): void
    {
        $rowNumber = 1;

        foreach ($rows as $row) {
            $rowNumber++;
            $rowData = $row->toArray();

            // Skip rows where full_name is empty or is the instruction row
            $name = trim((string) ($rowData['full_name'] ?? ''));
            if ($name === '' || str_starts_with($name, '* Required')) {
                continue;
            }

            $errors = [];

            // NIC
            $nic = trim((string) ($rowData['nic_number'] ?? ''));
            if (empty($nic)) {
                $errors[] = 'NIC is required';
            } elseif (! preg_match('/^([0-9]{9}[vVxX]|[0-9]{12})$/', $nic)) {
                $errors[] = 'NIC format invalid (use 9 digits + V/X or 12 digits)';
            }

            // Gender
            $gender = strtoupper(trim((string) ($rowData['gender_mf'] ?? '')));
            if (! in_array($gender, ['M', 'F'])) {
                $errors[] = 'Gender must be M or F';
            }


            // Optional: phone — max 10 digits
            $phone = trim((string) ($rowData['phone'] ?? ''));
            if (! empty($phone) && ! preg_match('/^[0-9]{10}$/', $phone)) {
                $errors[] = 'Phone must be exactly 10 digits';
                $phone = '';
            }

            // Birthday
            $birthday = $this->parseDate($rowData['birthday_ddmmyyyy'] ?? '');
            if (! $birthday) {
                $errors[] = 'Birthday is required and must be in DD/MM/YYYY format';
            }

            // School census_no
            $censusNo = trim((string) ($rowData['school_census_no'] ?? ''));
            if (empty($censusNo)) {
                $errors[] = 'School Census No is required';
            } elseif (! isset($this->schools[$censusNo])) {
                $errors[] = "School census no '{$censusNo}' not found in system";
            }

            // Staff type
            $staffType = trim((string) ($rowData['staff_type'] ?? ''));
            if (empty($staffType)) {
                $errors[] = 'Staff Type is required';
            } elseif (! isset($this->staffTypes[$staffType])) {
                $errors[] = "Staff type '{$staffType}' is not valid";
            }

            // Appointed date
            $appointedDate = $this->parseDate($rowData['appointed_date_ddmmyyyy'] ?? '');
            if (! $appointedDate) {
                $errors[] = 'Appointed Date is required and must be in DD/MM/YYYY format';
            }

            // Joined school date
            $joinedSchoolDate = $this->parseDate($rowData['joined_school_date_ddmmyyyy'] ?? '');
            if (! $joinedSchoolDate) {
                $errors[] = 'Joined School Date is required and must be in DD/MM/YYYY format';
            }

            // Optional: appointment type
            $appointmentType = trim((string) ($rowData['appointment_type'] ?? ''));
            if (! empty($appointmentType) && ! isset($this->appointmentTypes[$appointmentType])) {
                $errors[] = "Appointment type '{$appointmentType}' is not valid";
                $appointmentType = '';
            }

            // Optional: service grade
            $serviceGrade = trim((string) ($rowData['service_grade'] ?? ''));
            if (! empty($serviceGrade) && ! isset($this->serviceGrades[$serviceGrade])) {
                $errors[] = "Service grade '{$serviceGrade}' is not valid";
                $serviceGrade = '';
            }

            // Optional: appointed subject
            $subjectName        = trim((string) ($rowData['appointed_subject'] ?? ''));
            $appointedSubjectId = null;
            if (! empty($subjectName)) {
                $appointedSubjectId = $this->subjects[$subjectName] ?? null;
                if (! $appointedSubjectId) {
                    $errors[] = "Subject '{$subjectName}' not found — will be saved without subject";
                }
            }

            // Skip if validation errors
            if (! empty($errors)) {
                $this->results['errors'][] = [
                    'row'    => $rowNumber,
                    'name'   => $name ?: '—',
                    'nic'    => $nic ?? '',
                    'reason' => implode('; ', $errors),
                ];
                $this->results['skipped']++;
                continue;
            }

            // Skip duplicate NIC
            if (Teacher::where('nic', $nic)->exists()) {
                $this->results['errors'][] = [
                    'row'    => $rowNumber,
                    'name'   => $name,
                    'nic'    => $nic,
                    'reason' => "Teacher with NIC {$nic} already exists in the system",
                ];
                $this->results['skipped']++;
                continue;
            }

            // Create teacher
            Teacher::create([
                'name'                 => $name,
                'nic'                  => $nic,
                'gender'               => $gender,
                'birthday'             => $birthday,
                'phone'                => $phone ?: null,,
                'email'                => trim((string) ($rowData['email'] ?? '')) ?: null,
                'school_id'            => $this->schools[$censusNo],
                'staff_type'           => $staffType,
                'appointed_subject_id' => $appointedSubjectId,
                'appointment_type'     => $appointmentType ?: null,
                'service_grade'        => $serviceGrade ?: null,
                'salary_slip_no'       => trim((string) ($rowData['salary_slip_no'] ?? '')) ?: null,
                'appointed_date'       => $appointedDate,
                'joined_school_date'   => $joinedSchoolDate,
                'is_active'            => true,
                'added_by'             => auth()->id(),
            ]);

            $this->results['created']++;
        }
    }

    private function parseDate(mixed $value): ?string
    {
        if (empty($value)) return null;

        if (is_numeric($value)) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        $value = trim((string) $value);

        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $value, $m)) {
            return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        return null;
    }
}