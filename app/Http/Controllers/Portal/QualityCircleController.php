<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\QualityCircleCriteria;
use App\Models\QualityCircleMark;
use App\Models\QualityCircleRecord;
use App\Models\User;
use App\Helpers\ThemeHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QualityCircleController extends Controller
{
    // ── List all records for this school ──────────────────────────
    public function index()
    {
        $user   = Auth::user();
        $school = $user->school;
        $theme  = ThemeHelper::getTheme();

        if (!$school) {
            return redirect()->route('principal.dashboard')
                ->with('error', 'ඔබේ ගිණුමට පාසලක් සම්බන්ධ කර නොමැත.');
        }

        $records = QualityCircleRecord::where('school_id', $school->id)
            ->with(['inspector', 'approver'])
            ->orderByDesc('academic_year')
            ->get();

        $criteria = QualityCircleCriteria::active()->get();

        // Inspectors — divisional director, zonal director, zonal officers
        $inspectors = User::whereHas('roles', fn($q) =>
            $q->whereIn('name', ['divisional_director', 'zonal_director', 'zonal_officer'])
        )->orderBy('name')->get();

        // Can create if no draft/submitted record for current year
        $canCreate = true;

        return view('principal.quality-circles', compact(
            'user', 'school', 'theme', 'records', 'criteria', 'inspectors', 'canCreate'
        ));
    }

    // ── Store new record ──────────────────────────────────────────
    public function store(Request $request)
    {
        $user   = Auth::user();
        $school = $user->school;

        if (!$school) {
            return back()->with('error', 'ඔබේ ගිණුමට පාසලක් සම්බන්ධ කර නොමැත.');
        }

        $request->validate([
            'academic_year'   => 'required|integer|min:2000|max:2099',
            'inspection_date' => 'required|date',
            'marks'           => 'required|array',
        ]);

        // // Check no duplicate for this year — check all statuses
        // $existing = QualityCircleRecord::where('school_id', $school->id)
        //     ->where('academic_year', $request->academic_year)
        //     ->first();

        // if ($existing) {
        //     if (in_array($existing->status, ['draft', 'rejected'])) {
        //         // Redirect to edit instead
        //         return redirect()->route('principal.quality-circles.edit', $existing->id)
        //             ->with('error', $request->academic_year . ' වර්ෂය සඳහා කෙටුම්පතක් දැනටමත් පවතී. සංස්කරණය කරන්න.');
        //     }
        //     return back()->with('error', $request->academic_year . ' වර්ෂය සඳහා ඇගයීමක් දැනටමත් පවතී.');
        // }

        DB::transaction(function () use ($request, $school, $user) {
            $inspectedBy = $request->inspected_by !== 'other'
                ? ($request->inspected_by ?: null)
                : null;

            $status = $request->action === 'submit' ? 'submitted' : 'draft';

            // Create record
            $record = QualityCircleRecord::create([
                'school_id'              => $school->id,
                'academic_year'          => $request->academic_year,
                'inspection_date'        => $request->inspection_date,
                'inspected_by'           => $inspectedBy,
                'inspector_name'         => $request->inspected_by === 'other' ? $request->inspector_name : null,
                'inspector_designation'  => $request->inspected_by === 'other' ? $request->inspector_designation : null,
                'status'                 => $status,
                'created_by'             => $user->id,
            ]);

            // Save marks for each criteria
            foreach ($request->marks as $criteriaId => $markData) {
                $maximum = (int)($markData['maximum_marks'] ?? 0);
                $obtained = (int)($markData['obtained_marks'] ?? 0);

                QualityCircleMark::create([
                    'record_id'           => $record->id,
                    'criteria_id'         => $criteriaId,
                    'indicators_assessed' => (int)($markData['indicators_assessed'] ?? 0),
                    'maximum_marks'       => $maximum,
                    'obtained_marks'      => $obtained,
                    // percentage auto-calculated in model booted()
                ]);
            }

            // Recalculate final index
            $record->recalculate();
        });

        $msg = $request->action === 'submit'
            ? 'ඇගයීම අනුමැතිය සඳහා ඉදිරිපත් කරන ලදී.'
            : 'ඇගයීම කෙටුම්පතක් ලෙස සුරකින ලදී.';

        return redirect()->route('principal.quality-circles')
            ->with('success', $msg);
    }

    // ── Show single record ────────────────────────────────────────
    public function show(QualityCircleRecord $record)
    {
        $user  = Auth::user();
        $theme = ThemeHelper::getTheme();

        // Ensure principal owns this record
        if ($record->school_id !== $user->school_id) {
            abort(403);
        }

        $record->load(['marks.criteria', 'inspector', 'approver']);

        return view('principal.quality-circles-show', compact('user', 'record', 'theme'));
    }

    // ── Edit record (draft/rejected only) ─────────────────────────
    public function edit(QualityCircleRecord $record)
    {
        $user   = Auth::user();
        $school = $user->school;
        $theme  = ThemeHelper::getTheme();

        if ($record->school_id !== $user->school_id) {
            abort(403);
        }

        if (!in_array($record->status, ['draft', 'rejected'])) {
            return back()->with('error', 'ඉදිරිපත් කළ හෝ අනුමත ඇගයීමක් සංස්කරණය කළ නොහැක.');
        }

        $record->load(['marks.criteria']);
        $criteria   = QualityCircleCriteria::active()->get();
        $inspectors = User::whereHas('roles', fn($q) =>
            $q->whereIn('name', ['divisional_director', 'zonal_director', 'zonal_officer'])
        )->orderBy('name')->get();

        return view('principal.quality-circles-edit', compact(
            'user', 'record', 'criteria', 'inspectors', 'theme', 'school'
        ));
    }

    // ── Update record ─────────────────────────────────────────────
    public function update(Request $request, QualityCircleRecord $record)
    {
        $user = Auth::user();

        if ($record->school_id !== $user->school_id) abort(403);
        if (!in_array($record->status, ['draft', 'rejected'])) {
            return back()->with('error', 'ඉදිරිපත් කළ හෝ අනුමත ඇගයීමක් සංස්කරණය කළ නොහැක.');
        }

        $request->validate([
            'inspection_date' => 'required|date',
            'marks'           => 'required|array',
        ]);

        DB::transaction(function () use ($request, $record) {
            $inspectedBy = $request->inspected_by !== 'other'
                ? ($request->inspected_by ?: null)
                : null;

            $status = $request->action === 'submit' ? 'submitted' : 'draft';

            $record->update([
                'inspection_date'        => $request->inspection_date,
                'inspected_by'           => $inspectedBy,
                'inspector_name'         => $request->inspected_by === 'other' ? $request->inspector_name : null,
                'inspector_designation'  => $request->inspected_by === 'other' ? $request->inspector_designation : null,
                'status'                 => $status,
                'rejection_note'         => null,
            ]);

            foreach ($request->marks as $criteriaId => $markData) {
                QualityCircleMark::updateOrCreate(
                    ['record_id' => $record->id, 'criteria_id' => $criteriaId],
                    [
                        'indicators_assessed' => (int)($markData['indicators_assessed'] ?? 0),
                        'maximum_marks'       => (int)($markData['maximum_marks'] ?? 0),
                        'obtained_marks'      => (int)($markData['obtained_marks'] ?? 0),
                    ]
                );
            }

            $record->recalculate();
        });

        $msg = $request->action === 'submit'
            ? 'ඇගයීම අනුමැතිය සඳහා ඉදිරිපත් කරන ලදී.'
            : 'ඇගයීම යාවත්කාලීන කරන ලදී.';

        return redirect()->route('principal.quality-circles')
            ->with('success', $msg);
    }
}