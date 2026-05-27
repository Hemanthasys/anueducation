<?php

namespace App\Services;

use App\Models\Division;
use App\Models\School;
use App\Models\SchoolCompliance;
use App\Models\SchoolStat;
use App\Models\StatDeadline;
use App\Models\StatSnapshot;
use App\Models\User;

class StatisticsService
{
    // ── Generate a snapshot of current zone-wide statistics ────
    public function generateSnapshot(string $academicYear, ?StatDeadline $deadline = null): StatSnapshot
    {
        // Aggregate total students from school_stats
        // Use latest academic year per school
        $totalStudents = 0;
        $schools = School::where('is_active', true)->get();

        foreach ($schools as $school) {
            $stat = SchoolStat::where('school_id', $school->id)
                ->orderByDesc('academic_year')
                ->first();

            if ($stat) {
                $totalStudents += $stat->total_students;
            }
        }

        // Count active teachers from users table
        $totalTeachers = User::whereNotNull('school_id')
            ->where('staff_type', 'teacher')
            ->where('is_active', true)
            ->count();

        $snapshot = StatSnapshot::updateOrCreate(
            ['academic_year' => $academicYear],
            [
                'stat_deadline_id' => $deadline?->id,
                'total_schools'    => $schools->count(),
                'total_divisions'  => Division::count(),
                'total_students'   => $totalStudents,
                'total_teachers'   => $totalTeachers,
                'generated_at'     => now(),
            ]
        );

        return $snapshot;
    }

    // ── Create compliance records for all schools ──────────────
    public function createComplianceRecords(StatDeadline $deadline): void
    {
        $schools = School::where('is_active', true)->get();

        foreach ($schools as $school) {
            SchoolCompliance::updateOrCreate(
                [
                    'school_id'        => $school->id,
                    'stat_deadline_id' => $deadline->id,
                ],
                ['status' => 'pending']
            );
        }
    }

    // ── Mark overdue schools after deadline passes ─────────────
    public function markOverdueSchools(StatDeadline $deadline): void
    {
        SchoolCompliance::where('stat_deadline_id', $deadline->id)
            ->where('status', 'pending')
            ->update(['status' => 'overdue']);
    }

    // ── Mark a school as submitted ─────────────────────────────
    public function markSchoolSubmitted(int $schoolId): void
    {
        $deadline = StatDeadline::where('is_active', true)
            ->whereNull('triggered_at')
            ->first();

        if (!$deadline) return;

        SchoolCompliance::updateOrCreate(
            [
                'school_id'        => $schoolId,
                'stat_deadline_id' => $deadline->id,
            ],
            [
                'status'       => 'submitted',
                'submitted_at' => now(),
            ]
        );
    }

    // ── Check if principal can still submit ────────────────────
    public function canSubmit(int $schoolId): bool
    {
        $deadline = StatDeadline::where('is_active', true)->first();

        // No active deadline — allow submission
        if (!$deadline) return true;

        // Deadline passed and snapshot triggered — lock submissions
        if ($deadline->triggered_at) return false;

        // Deadline date passed — lock submissions
        if (now()->isAfter($deadline->deadline_date)) return false;

        return true;
    }

    // ── Get compliance report for active deadline ──────────────
    public function getComplianceReport(): array
    {
        $deadline = StatDeadline::where('is_active', true)->first();

        if (!$deadline) return [];

        $records = SchoolCompliance::where('stat_deadline_id', $deadline->id)
            ->with('school.division')
            ->get();

        return [
            'deadline'   => $deadline,
            'total'      => $records->count(),
            'submitted'  => $records->where('status', 'submitted')->count(),
            'pending'    => $records->where('status', 'pending')->count(),
            'overdue'    => $records->where('status', 'overdue')->count(),
            'records'    => $records->sortBy('status'),
        ];
    }

    // ── Get current academic year ──────────────────────────────
    public function getCurrentAcademicYear(): string
    {
        $year = now()->year;
        return $year . '/' . ($year + 1);
    }

    // ── Get latest snapshot ────────────────────────────────────
    public function getCurrentSnapshot(): ?StatSnapshot
    {
        return StatSnapshot::latest()->first();
    }
}