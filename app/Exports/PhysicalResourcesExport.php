<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PhysicalResourcesExport implements WithMultipleSheets
{
    private array $filters;
    private array $site;

    public function __construct(array $filters, array $site)
    {
        $this->filters = $filters;
        $this->site    = $site;
    }

    public function sheets(): array
    {
        return array_map(
            fn (string $category) => new PhysicalResourcesCategoryExport($category, $this->filters, $this->site),
            PhysicalResourcesCategoryExport::categoryKeys()
        );
    }
}
