<?php

namespace App\Services;

use App\Models\Division;
use App\Models\School;
use App\Models\StatDeadline;
use App\Models\StatSnapshot;
use App\Models\SchoolCompliance;

class StatisticsService
{
    // Generate a snapshot of current zone-wide statistics
    public function generateSnapshot(string $academicYear): StatSnapshot
    {
        $snapshot = StatSnapshot::updateOrCreate(
            ['academic_year' => $academicYear],
            [
                'total_schools'   => School::where('is_active', true)->count(),
                'total_divisions' => Division::count(),
                // Students and teachers will come from Phase 2 data
                // For now use placeholder values
                'total_students'  => 0,
                'total_teachers'  => 0,
                'generated_at'    => now(),
            ]
        );

        return $snapshot;
    }

    // Create compliance records for all schools when deadline is set
    public function createComplianceRecords(StatDeadline $deadline): void
    {
        $schools = School::where('is_active', true)->get();

        foreach ($schools as $school) {
            SchoolCompliance::updateOrCreate(
                [
                    'school_id'       => $school->id,
                    'stat_deadline_id' => $deadline->id,
                ],
                ['status' => 'pending']
            );
        }
    }

    // Mark overdue schools after deadline passes
    public function markOverdueSchools(StatDeadline $deadline): void
    {
        SchoolCompliance::where('stat_deadline_id', $deadline->id)
            ->where('status', 'pending')
            ->update(['status' => 'overdue']);
    }

    // Get current academic year snapshot
    public function getCurrentSnapshot(): ?StatSnapshot
    {
        $academicYear = $this->getCurrentAcademicYear();
        return StatSnapshot::where('academic_year', $academicYear)->latest()->first();
    }

    // Get current academic year string e.g. 2025/2026
    public function getCurrentAcademicYear(): string
    {
        $year = now()->month >= 1 ? now()->year : now()->year - 1;
        return $year . '/' . ($year + 1);
    }
}