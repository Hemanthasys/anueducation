<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;

class ProjectPdfController extends Controller
{
    /**
     * Preview the PDF in the browser (HTML view with print button).
     */
    public function preview(Project $project)
    {
        $project->load([
            'fundingSource.category',
            'expenditureVotes.category',
            'assignments.school.division',
            'assignments.assignedTo',
            'milestones',
            'createdBy',
        ]);

        $data = [
            'project'        => $project,
            'generatedAt'    => now()->format('d M Y H:i'),
            'generatedBy'    => auth()->user()->name,
            'totalBudget'    => $project->budget ?? 0,
            'totalAllocated' => $project->total_allocated,
            'remaining'      => $project->remaining_budget,
            'schoolsCount'   => $project->assignments->count(),
            'customCount'    => $project->assignments->whereNotNull('allocated_budget')->count(),
        ];

        return view('pdf.project-summary-preview', $data);
    }

    /**
     * Download the PDF directly.
     */
    public function summary(Project $project)
    {
        $project->load([
            'fundingSource.category',
            'expenditureVotes.category',
            'assignments.school.division',
            'assignments.assignedTo',
            'milestones',
            'createdBy',
        ]);

        $data = [
            'project'        => $project,
            'generatedAt'    => now()->format('d M Y H:i'),
            'generatedBy'    => auth()->user()->name,
            'totalBudget'    => $project->budget ?? 0,
            'totalAllocated' => $project->total_allocated,
            'remaining'      => $project->remaining_budget,
            'schoolsCount'   => $project->assignments->count(),
            'customCount'    => $project->assignments->whereNotNull('allocated_budget')->count(),
        ];

        $pdf = Pdf::loadView('pdf.project-summary', $data)
            ->setPaper('a4', 'portrait')
            ->setWarnings(false);

        $filename = 'Project-' . $project->reference_no . '-Summary.pdf';

        return $pdf->download($filename);
    }
}