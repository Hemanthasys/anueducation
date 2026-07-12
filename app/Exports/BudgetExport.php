<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BudgetExport implements WithMultipleSheets
{
    private string $academicYear;
    private array  $filters;
    private array  $site;

    public function __construct(string $academicYear, array $filters, array $site)
    {
        $this->academicYear = $academicYear;
        $this->filters      = $filters;
        $this->site         = $site;
    }

    public function sheets(): array
    {
        return [
            new BudgetIncomeExport($this->academicYear, $this->filters, $this->site),
            new BudgetExpenditureExport($this->academicYear, $this->filters, $this->site),
        ];
    }
}
