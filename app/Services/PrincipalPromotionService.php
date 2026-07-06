<?php

namespace App\Services;

use App\Enums\TeacherStatus;
use App\Models\School;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PrincipalPromotionService
{
    /**
     * Promote a teacher to principal pool.
     * Does NOT assign to a school — that happens separately.
     *
     * Returns array with:
     *   - user: the User model
     *   - username: string
     *   - password: string|null (null if account already existed)
     *   - account_created: bool
     */
    public function promoteToPool(Teacher $teacher, string $note = '', ?string $serviceGrade = null): array
    {
        return DB::transaction(function () use ($teacher, $note, $serviceGrade) {

            $plainPassword  = null;
            $accountCreated = false;

            // ── Step 1: Get or create user account ────────────────────────
            if ($teacher->user_id) {
                // Already has an account — just change role
                $user = User::findOrFail($teacher->user_id);
                $user->syncRoles(['school_principal']);
                $user->school_id          = null;
                $user->previous_school_id = $teacher->school_id;
                $user->pool_entered_at    = now();
                if ($serviceGrade) {
                    $user->service_grade = $serviceGrade;
                }
                $user->save();
            } else {
                // Create new user account
                $plainPassword = $this->generatePassword();
                $username      = $this->generateUsername($teacher);

                $user = User::create([
                    'name'                 => $teacher->name,
                    'username'             => $username,
                    'email'                => $teacher->email,
                    'phone'                => $teacher->phone,
                    'nic'                  => $teacher->nic,
                    'school_id'            => null,
                    'previous_school_id'   => $teacher->school_id,
                    'pool_entered_at'      => now(),
                    'service_grade'        => $serviceGrade,
                    'password'             => Hash::make($plainPassword),
                    'must_change_password' => true,
                    'is_active'            => true,
                ]);

                $user->assignRole('school_principal');
                $accountCreated = true;

                // Link user back to teacher record
                $teacher->user_id = $user->id;
            }

            // ── Step 2: Update teacher record ──────────────────────────────
            $teacher->status            = TeacherStatus::PromotedPrincipal->value;
            $teacher->status_note       = $note ?: 'Promoted to Principal — pending school assignment';
            $teacher->status_changed_at = now()->toDateString();
            $teacher->is_active         = false;
            $teacher->save();

            return [
                'user'            => $user,
                'username'        => $user->username,
                'password'        => $plainPassword,
                'account_created' => $accountCreated,
            ];
        });
    }

    /**
     * Assign a pooled principal to a school.
     * Handles removing the previous principal (sends them to pool).
     */
    public function assignToSchool(User $principal, School $school, bool $isActing = false): void
    {
        DB::transaction(function () use ($principal, $school, $isActing) {

            // ── Step 1: Handle previous principal — send to pool ──────────
            if ($school->principal_id && $school->principal_id !== $principal->id) {
                $previousPrincipal = User::find($school->principal_id);
                if ($previousPrincipal) {
                    $previousPrincipal->previous_school_id = $school->id;
                    $previousPrincipal->school_id          = null;
                    $previousPrincipal->pool_entered_at    = now();
                    $previousPrincipal->save();
                }
            }

            // ── Step 2: Assign new principal to school ─────────────────────
            $principal->school_id          = $school->id;
            $principal->previous_school_id = null;
            $principal->pool_entered_at    = null;
            $principal->save();

            $school->principal_id = $principal->id;
            $school->save();
        });
    }

    /**
     * Remove a principal from their school (transfer out or retire).
     * Sends them to pool or marks as retired.
     */
    public function removeFromSchool(User $principal, string $reason = 'transferred'): void
    {
        DB::transaction(function () use ($principal, $reason) {

            // Find and clear the school's principal_id
            $school = School::where('principal_id', $principal->id)->first();
            if ($school) {
                $school->principal_id = null;
                $school->save();
            }

            $previousSchoolId = $principal->school_id;

            $principal->previous_school_id = $previousSchoolId;
            $principal->pool_entered_at    = now();
            $principal->school_id          = null;

            if ($reason === 'retired') {
                $principal->is_active       = false;
                $principal->pool_entered_at = null; // retired, not in pool
            }

            $principal->save();
        });
    }

    /**
     * Assign principal to a non-school institution (e.g. Zonal Office).
     * They leave the pool but are not assigned a school.
     */
    public function assignToInstitution(User $principal, string $institutionName): void
    {
        DB::transaction(function () use ($principal, $institutionName) {
            $principal->school_id       = null;
            $principal->pool_entered_at = null;
            $principal->designation     = $institutionName;
            $principal->save();
        });
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function generateUsername(Teacher $teacher): string
    {
        $base     = 'P' . substr(preg_replace('/[^0-9]/', '', $teacher->nic ?? rand(100000, 999999)), -6);
        $username = $base;
        $counter  = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }

        return $username;
    }

    private function generatePassword(): string
    {
        return substr(str_shuffle('abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789'), 0, 8);
    }
}