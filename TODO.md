# anueducation.lk — Task List

_Generated from the pre-launch fix list, session findings, and deployment checklist._

## Pre-Launch Fix List

- [x] Add Create button to Lookup Values page
- [x] Sweep for leftover Filament v3 `Tables\Actions\*` namespaces across other resources (only remaining instance is in the deliberately-dead SubjectResource)
- [x] Fix O/L Results "no results imported" messaging (Blade view was already correct; controller now distinguishes "nothing imported" from "filters matched zero rows")
- [x] Fix `OlResult::buildBaseQuery()` silently ignoring the year filter (and returning all-years data) when a nonexistent year was requested
- [x] Fix raw Laravel error on missing email (new user creation) — also fixed a second gap: password could be left unset (DB crash) if NIC/username were also left blank — ⚠️ **NEEDS YOUR TESTING**: create a user with no NIC/username/password and confirm the credentials notification shows correctly
- [ ] Diagnose and fix photo upload error — ⚠️ **WAITING ON YOU**: couldn't reproduce a crash (GD/image pipeline works fine locally); likely cause is inconsistent/too-strict size limits (1MB on Divisions ISAs/Staff, 2MB on Principal portal + User photo — modern phone photos often exceed these) and no HEIC support. Check the exact error text/circumstances, then we can fix.
- [x] Review and complete Permission Manager — see "Permission Manager overhaul" section below for full detail.
- [ ] Set up and verify reCAPTCHA — ⏸️ **DEFERRED TO DEPLOY TIME**: works locally, failed on staging (europeangate.lk) — almost certainly the site key's registered domain list doesn't include that domain. Revisit when actually deploying, register the real target domain then.
- [x] Fix raw Laravel error on missing Sinhala translation (Notice creation) — systematic sweep of all 27 other resources found the identical bug in Downloads, News, and Programmes (all missing `->required()` on `title_si`); all 4 now fixed. 24 resources confirmed clean.
- [x] Add lat/lng fields to Site Settings for Contact page map (ContactController + public view were already wired for this; only the admin form fields were missing)

## Ops task (deploy-time only)

- [ ] Confirm Directory Privacy disabled on production hosting account

## Loose threads found this session

- [x] ~~Register or backfill `al_subjects` seeder~~ — false alarm, original flag was based on an incomplete check. The migration itself (`2026_05_20_000001_create_al_results_tables.php`) inserts all 59 rows directly, so it auto-populates on every deploy with no separate seeder needed.
- [x] Build missing `PhysicalResourcesExport` "full workbook" class — 7-sheet export (Infrastructure, Water & Sanitation, ICT, Science & Technical, Sports, Security & Safety, Transport), one row per school each. Found the model's `$fillable`/`$casts` still reference financial columns (`annual_budget` etc.) dropped by a later migration — folded that cleanup into the public school page item below, since fixing it means deciding whether to wire the new budget tables in or remove the dead section.
- [ ] Review `anueducation-security-hardening.md` checklist

## Permission Manager overhaul (built this session)

Full audit found 12 real hardcoded bypasses (toggling the permission did nothing) plus a fully-dead legacy permission system. Fixed all of it, verified with real functional tests (not just lint) at every step, and was careful to preserve every role's current real-world access unless the change was an intentional, explicitly-agreed tightening.

- [x] **Deleted the legacy colon-style permission system** — 65 permissions (`news:create`, `schools:view`, etc.) and their 164 role assignments, left over from before the current `module.action` system existed. Confirmed via full codebase search that zero code anywhere checked them — pure dead weight, safe to remove.
- [x] **Deleted 5 more orphaned dot-style `projects.*` permissions** from an even earlier iteration of the projects module (`projects.manage`, `projects.manage_budget`, `projects.update_milestones`, `projects.view_budget`, `projects.reports`) — confirmed unreferenced. Two others from that same batch (`projects.approve_milestones`, `projects.assign_schools`) were kept and wired in, since they mapped exactly onto real hardcoded gates.
- [x] **Projects module** — Create/Edit/Delete, milestone approve/reject, and school-assignment were all hardcoded to a fixed role list; now check `projects.create/edit/delete/assign_schools/approve_milestones`. Also fixed a pre-existing inconsistency: `zonal_director` and `zonal_officer_planning` were hardcoded into managing projects but never actually held `projects.view`, so they couldn't reach the resource's list at all — fixed as part of the same change.
- [x] **Theme Manager** — was hardcoded despite its own `settings.theme` permission sitting unused; now wired correctly.
- [x] **New `budget` permission module** (`budget.view`, `budget.approve`) — the whole Budget Approval workflow (Pending Budget Approvals page + Analysis Dashboard Budget tab) had no permission at all before this, entirely hardcoded.
- [x] **Analysis Dashboard + AnalysisController** — the biggest piece. All ~9 tabs (HR, Students, Schools, Physical Resources, Quality Circles, Projects, Compliance, Results, Budget) shared one hardcoded 7-role check; each now has its own specific permission, and the dashboard's card grid is filtered so nobody sees a card leading to a 403. **Real behavior change, done deliberately**: roles with narrow/no matching permissions (e.g. `zonal_officer_schools`, `zonal_officer_accounts`) now see fewer tabs than before, instead of everything — that's the intended fix, not a bug. `physical_resources` reclassified from "Coming Soon" to "Live" in the seeder since it's a real, built, working feature.
- [x] **Contact Messages** — had no permission module at all; added `contact_messages.manage`.
- [x] **Principal Pool** → wired to `staff.manage`; **Teacher Bulk Upload** → wired to `teachers.manage` (both previously hardcoded, both conceptually overlapped these existing modules).
- [x] **Exam Import Controller** — the actual import/delete actions had *zero* permission check even though the page wrapper checked `results.import`; now consistent.
- [x] **Mutual Transfers** — was `can('transfers.view') OR hardcoded [super_admin, zonal_director]`, and mislabeled "Coming Soon" in the Permission Manager despite gating a live feature. Split into its own new, correctly-labeled `mutual_transfers.view` permission, separate from the still-unbuilt formal Transfer System.
- [x] **News/Programmes editorial workflow** (submit → review → approve/reject/publish) — the approval step had no permission at all, hardcoded to a fixed role list including a `zonal_officer` role that isn't even shown in the Permission Manager UI. New `content.approve` permission covers approve/reject/publish/status-override for both; "submit for review" now uses the existing base `content.news`/`content.programmes` permission instead of being locked to the `content_creator` role specifically.
- [x] Every fix above shipped with a migration granting the specific permissions needed to preserve each affected role's current real-world access (checked precisely per role via direct DB queries, not assumed) — except the Analysis Dashboard tightening, which was an intentional, flagged exception.

## Full audit trail + login tracking (built this session)

- [x] `App\Traits\Auditable` — reusable trait hooking Eloquent create/update/delete events into the Audit Log automatically. Applied to 57 models spanning admin panel content (News, Notices, Programmes, Users, Permissions-adjacent, etc.), both portals' data (Teacher/Staff, Budget, Milestones, Profile Change Requests, Attachments), and reference data. Deliberately excluded: `AuditLog` itself, high-volume bulk-import row tables (`Grade5Result`/`OlResult`/`AlResult` — the import batch is audited, not each of potentially thousands of rows), and pure child/pivot tables already captured via their parent (`QualityCircleMark`, `MilestoneUpdatePhoto`, `TeacherTeachingSubject`).
- [x] Login attempt tracking across all 3 portals (Admin/Filament, Teacher, Principal) — successful and failed logins both logged, including the attempted username on failure. Admin panel already fired Laravel's standard auth events; added the same event dispatch to Teacher/Principal's custom login controllers (neither previously logged anything, including failed attempts).
- [x] Suspicious-login detection — 5 failed attempts from the same IP within 15 minutes gets flagged distinctly (`login_failed_suspicious`, shown in red in the Audit Log) instead of blending into normal traffic.
- [x] **Found and fixed a real schema bug while wiring this up**: `audit_logs.module`/`.action` were fixed SQL `ENUM` columns sized for the original 3-form audit trail (11 fixed module values) — completely incompatible with dynamic per-model module names or the new login actions. Widened both to plain strings via migration, and made `user_id` nullable (a failed login against a nonexistent username has no user to attach). Verified end-to-end after the fix: login success/failure/suspicious-threshold detection and model create/update/delete auditing all confirmed working with real data.

## PWA + offline data capture (built this session)

- [x] Both Teacher and Principal portals are now installable PWAs — `manifest-teacher.json`/`manifest-principal.json`, shared `sw.js` service worker (network-first pages, cache-first assets), a custom "Install App" button (Chrome/Edge/Android via `beforeinstallprompt`) and an iOS Safari "tap Share → Add to Home Screen" banner (no native prompt exists on iOS). Icons generated from the existing 60×60 logo — **worth swapping in a higher-resolution source logo later**, current icons will look soft at 512×512.
- [x] Offline data capture — **Principal Portal only** (School Basic Info, Student Statistics, Physical Resources); Teacher Portal has no offline capture. Budget tab and any photo/file upload fields are excluded and disabled with a "requires internet connection" note whenever offline — they stay online-only by design, since Budget validates against live database totals.
- [x] `public/js/offline-queue.js` — IndexedDB-backed queue; opted-in forms (`data-offline-section="..."`) save locally when offline instead of submitting, then auto-replay against the server when connectivity returns. Handles CSRF token refresh on stale-token replay, and surfaces failed syncs (e.g. a submission deadline that locked while offline) in a topbar dropdown with per-item Retry.
- [x] `PrincipalController::updateSchool()` now responds with JSON when the request explicitly asks for it (`Accept: application/json`), used by the offline-sync queue to reliably detect success/failure — normal browser form submits are completely unaffected.
- [x] **Found and fixed a pre-existing, unrelated bug**: the Physical Resources "Save" form posted to `POST /principal/physical-resources` → `PrincipalController::updatePhysicalResources()`, a method that doesn't exist — every save was a fatal 500 error in production. The real, working logic already existed in `updateSchool()` (reachable via the correct `POST /principal/school` route); fixed the form's `action` to point there and removed the dead route. Verified end-to-end: save now returns `{"success":true,...}` and persists correctly.

## Admin sidebar regroup + dashboard redesign (built this session)

- [x] Regrouped all 42 sidebar items from 15 groups down to 10: Analysis & Reports, Divisions & Schools (renamed), Administration (renamed from School Management, gained Office Sections), Planning & Development (gained Pending Project Reviews + Quality Circles), Website Content (fixed EventResource's mistyped group, lost Sliders + Office Sections), Navigation, Communications, Settings (gained Sliders), Reference Data (new — Lookup Values, Teaching/O-L/A-L Subjects, Qualifications), User Management.
- [x] Fixed all sort-number collisions (5 separate collisions, one 3-way) so ordering within each group is now deterministic.
- [x] Fixed `EventResource` — was missing `canAccess()` entirely (any logged-in user could manage it); now gated on a new `content.events` permission, matching every sibling content resource.
- [x] Deleted `app/Filament/Resources/Users/SubjectResource.php` — confirmed dead/broken code (referenced non-existent Page classes), superseded by Teaching/O-L/A-L Subjects.
- [x] Redesigned the admin dashboard (previously just a lone `AccountWidget` and empty space): new `WelcomeWidget` (emblem/logo/flag branding, bilingual site name + tagline, time-of-day greeting with user's name and role) and `AdminHelpWidget` (permission-aware quick links to major sections + usage tips), replacing `AccountWidget`. Logout/profile still reachable via the top-right user menu as before.

## Audit Log viewer (built this session)

- [x] `AuditLogResource` — read-only Filament resource (List + View only, no create/edit/delete) under Settings nav group, gated on the existing `settings.audit_log` permission. Table: date/time, user, module, action (colored badges), school, IP — filterable by module/action/user/date range. View page shows old-vs-new values diff, IP, user agent, notes. Wires up the `AuditLog` model / `AuditLogService` that were already logging `school_info`, `student_stats`, and `physical_resources` changes from the principal portal but had no viewer.

## School Budget Approval workflow (built this session)

- [x] `school_budget_approvals` table + model — status draft/submitted/approved/rejected, `isEditable()`, `approve()`/`reject()` helpers that send a notification back to the principal
- [x] Principal portal budget UI — status badge, prominent balance check, "Submit Budget for Approval" button (blocked unless income = expenditure), fields locked once submitted/approved, rejection reason shown so the principal can correct and resubmit
- [x] `SchoolBudgetReviewed` notification — principal notifications list made type-aware (`notification_kind`) so old milestone notifications keep rendering correctly
- [x] Admin "Pending Budget Approvals" page (Planning & Development nav group) — queue for `zonal_officer_planning`/`zonal_director`/`super_admin`, Approve or Reject-with-reason (min 10 chars), no editing
- [x] Admin Analysis Dashboard "Budget" tab (`admin/analysis/budget`) — income by funding category/source, expenditure by category/vote, division-wise and school-wise breakdown with approval status, zone-wide balance check, Excel export (Income + Expenditure sheets), academic year filter

## "Coming soon" gaps found this session

- [ ] Build admin Analysis Dashboard "Results" tab
- [ ] Build/clarify teacher portal "Transfers" page (vs Mutual Transfers, already built)
- [ ] Build principal portal term test mark submission
- [ ] Build public school page "Projects" section, remove coming-soon banner — also fix the dead "Financial" physical-resources section on this same page (references `annual_budget`/`sbm_funds`/etc. columns that were dropped by a later migration; silently blank now, not an error). Decide: wire to the new `school_budget_income`/`school_budget_expenditure` tables, or remove the section.

## Bigger picture / deployment

- [ ] Continue general feature testing on staging
- [ ] Repeat deployment process on real anueducation.lk account
- [ ] Production go-live checklist (APP_ENV/DEBUG switch, cache commands, security hardening, mailer setup)

---

## Done this session

- [x] Sync 6 staging-only fixes to local (SiteSetting guard, EventResource namespace, ExamImportController reader type, OlResult ambiguous columns, mysql-schema.sql removal, DatabaseSeeder already complete)
- [x] Real `SuperAdminSeeder` logic
- [x] Self-service password change (admin, teacher, principal) + forced first-login change
- [x] Forgot password flow (teacher/principal portals)
- [x] Fixed `QualificationResource` (was undiscoverable) + new `TeachingSubjectResource`
- [x] Teacher bulk upload: fatal syntax error, phone leading-zero, multi-format date parsing
- [x] Mutual Transfer module (post/browse/match, admin visibility)
- [x] Special Programmes: `submitted_by` auto-stamp, homepage "Read More" button + modal
- [x] Photo Gallery module (Google Drive album links, admin CRUD, public listing + per-album share page)
- [x] Admin login page branding (3 logos, gradient background, tagline)
