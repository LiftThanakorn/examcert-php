## 2026-05-18 - Install Database Script for Test Machines

Completed:
- Added `install-database.php` as a web-based database install/update helper for test machines.
- Reworked installer text to ASCII/UTF-8-safe English to avoid Thai encoding issues during setup.
- Made installer create/use the configured `DB_NAME`, run `database/schema.sql`, import `database/data_export.sql` when present, and write a setup lock timestamp.
- Added update migrations for `questions.type` rating-scale support and other current schema columns commonly needed by existing databases.

Verification:
- Ran PHP lint on `install-database.php`: passed.

## 2026-05-18 - Rating Scale Survey Questions and Category Scores

Completed:
- Added `rating_scale` to the question type schema and runtime question type support.
- Added fixed 5-4-3-2-1 rating choices for authoring, CSV import, and the public exam UI.
- Updated exam submission scoring so rating-scale answers are validated as 1-5 and stored directly in `answer_logs.score_earned` without comparing `correct_answer`.
- Added prepared category score aggregation from `answer_logs` joined to `questions`.
- Added a result-page category score card using the existing `shadow-card-lg`, `anim-fade-up`, rounded, border, and primary color classes.

Verification:
- Ran PHP lint with Laragon PHP on the touched controllers, models, and views: passed.
- Ran `git diff --check`: passed with Git CRLF normalization warnings only.
- Ran the required mojibake scan on edited Thai files: no matches.

## 2026-05-12 - Overall Report Logic Improvement

Completed:
- Refactored `ReportController.php` to use SQL subqueries instead of `LEFT JOIN`s for more accurate and efficient metric calculation.
- Added `in_progress_count` metric to track participants currently taking exams.
- Updated `views/reports/index.php` to display the "กำลังสอบ" (In Progress) count in the summary table.
- Updated CSV export functionality in `ReportController.php` to include the in-progress count.

Verification:
- Ran PHP lint on `controllers/ReportController.php` and `views/reports/index.php`: passed.
- Verified that the SQL query correctly calculates participants, questions, finalized sessions, and in-progress sessions without Cartesian product inflation.

## 2026-05-12 - Participant Import Enhancements and Exam Integrity

Completed:
- Added "Title" (คำนำหน้า) column support to participant import logic.
- Updated participant import UI documentation and column order to include the "Title" field.
- Set a unique session name (`EXAMCERT_SESS`) to prevent session conflicts with other systems on the same domain.
- Added a "Download Certificate" button to the exam result page for participants who pass.
- Implemented logic to prevent participants from re-taking the exam if they have already passed; they are now redirected to their previous passing result.

Verification:
- Ran PHP lint on `models/Participant.php`, `models/ExamSession.php`, `controllers/PublicExamController.php`, `views/participants/import.php`, and `views/exam/result.php`: passed.
- Verified that the "Title" field is correctly handled during import.
- Verified that the "Download Certificate" button appears and links correctly.
- Verified that re-entry for a passed participant redirects to the result page.

## 2026-05-12 - Participant Attempt Status Display

Completed:
- Added `getParticipantAttemptStatus()` to centralize exam-attempt eligibility logic.
- Updated `startExamSession()` to use the shared attempt status when deciding whether to resume, block, or create a new attempt.
- Added attempt status data to the public participant search API.
- Updated the public exam entry screen to show per-participant attempt status after selecting a name: allowed attempts, used attempts, remaining attempts, and active in-progress status.
- Confirmed the result page retry button still returns to the entry flow instead of opening `take-exam.php` directly.

Verification:
- Ran PHP lint on `models/ExamSession.php`, `api/exam.php`, and `views/exam/entry.php`: passed.
- Tested local attempt status for an exhausted participant: returned `ใช้สิทธิ์สอบครบแล้ว`.
- Tested a temporary in-progress session: returned `กำลังทำข้อสอบอยู่` and `can_start=true`; temporary session was deleted.
- Ran mojibake scan on edited Thai files: no matches.
- Ran `git diff --check`: passed.

## 2026-05-12 - Remove mbstring Runtime Dependency

Completed:
- Removed all direct PHP multibyte string calls from tracked application code.
- Changed text helpers to use PHP core functions and UTF-8 regex handling only.
- Replaced the admin sidebar initials logic with a PHP core implementation.

Verification:
- Ran tracked application search for multibyte string calls under `config`, `models`, `controllers`, `views`, and `api`: no matches.
- Ran PHP lint on `config/config.php` and `views/layout/sidebar.php`: passed.

## 2026-05-12 - Production Exam Hotfix

Completed:
- Added shared text helpers so production does not depend on the PHP multibyte string extension.
- Updated choice normalization, exam grading, CSV true/false import, and participant search to use the fallback helpers.
- Hardened the exam page JSON payload with `JSON_INVALID_UTF8_SUBSTITUTE` via `jsonForScript()` so malformed UTF-8 data cannot break `QUESTIONS` JavaScript rendering.
- Updated the admin exam sessions list so `in_progress` rows display `กำลังสอบ` and score `-` instead of showing the default `fail` result as `ไม่ผ่าน`.

Verification:
- Ran PHP lint on `config/config.php`, `models/Question.php`, `models/ExamSession.php`, `models/Participant.php`, `controllers/QuestionController.php`, `controllers/PublicExamController.php`, `api/exam.php`, `views/exam/start.php`, `views/questions/form.php`, and `views/exam-sessions/index.php`: passed.
- Verified `jsonForScript()` substitutes invalid UTF-8 instead of returning an empty JS payload.
- Re-ran Thai answer submit smoke test: answer `ค` normalized to `c`, marked correct, and awarded score.
- Ran `git diff --check`: passed.

## 2026-05-12 - Exam Resume Logic and Thai Choice Labels

Completed:
- Clarified and fixed exam resume behavior so entering an active session does not auto-fail unless the session itself has expired or auto-submit-on-close is enabled.
- Updated time checks in `PublicExamController` and `api/exam.php` so `project.exam_end` only caps active session time when `auto_submit_on_close` is enabled.
- Added shared choice helpers for internal `a/b/c/d` keys with Thai display labels `ก/ข/ค/ง`.
- Updated exam rendering, question form, CSV import, and answer grading to display Thai labels while accepting both Thai and English choice keys.

Verification:
- Ran PHP lint on `models/Question.php`, `models/ExamSession.php`, `controllers/QuestionController.php`, `controllers/PublicExamController.php`, `api/exam.php`, `views/exam/start.php`, and `views/questions/form.php`: passed.
- Ran submit smoke test with Thai answer `ค`: passed, stored as internal key `c`, marked correct, and awarded score.
- Ran resume smoke test for an active session after project end with `auto_submit_on_close=0`: passed, existing session resumed instead of auto-submitting.
- Checked edited Thai files for mojibake patterns: none found.

## 2026-05-12 - Exam Submit Reliability and Start Responsive Fix

Completed:
- Updated exam submission to include current in-browser answers in the final POST payload instead of relying only on autosave.
- Updated `submitExamSession()` to prefer final POST answers, fall back to autosaved answers, and ignore non-scalar answer payloads.
- Restored subjective submit behavior so subjective answers are stored with `grading_status=pending_manual`, no auto-correct flag, and no automatic score weight.
- Improved `views/exam/start.php` mobile layout for the fixed top bar, question card, option rows, palette, and bottom navigation.

Verification:
- Ran PHP lint on `models/ExamSession.php`: passed.
- Ran PHP lint on `views/exam/start.php`: passed.
- Ran isolated submit smoke test with multiple choice plus subjective questions: passed, POST answer won over stale autosave, subjective was `pending_manual`.
- Confirmed temporary smoke-test project cleanup: `tmp_projects=0`.
- Ran `git diff --check`: passed.

## 2026-05-12 - Certificate Numbering Settings UI
Completed:
- Added `cert_sequence` to `projectPayload` in `models/Project.php` to support saving certificate sequences.
- Updated `createProject` and `updateProject` in `models/Project.php` to include `cert_sequence` in database operations.
- Added UI fields for "คำนำหน้าเลขที่" (Prefix) and "ลำดับเลขถัดไป" (Next Sequence) in the project creation and editing form (`views/projects/create.php`).
- Reconstructed `models/Project.php` to fix a corruption caused by an incorrect tool call during the previous step.

Verification:
- Verified `models/Project.php` file integrity and syntax.
- Verified that `cert_number_prefix` and `cert_sequence` are correctly passed to and saved by the database via form submission.

## 2026-05-12 - Certificate Template Element Selection Fix

Completed:
- Fixed template builder element IDs so newly added or loaded elements cannot share the same selection ID.
- Normalized missing/duplicate element IDs before rendering existing templates.
- Offset newly added elements when their default position overlaps an existing element, making the new item visible and separately clickable.

Verification:
- Ran PHP lint on `views/certificates/template_builder.php`: passed.

## 2026-05-12 - User-Friendly Closed Exam Message

Completed:
- Changed the manual override expired-exam message from the technical `exam_end` wording to the user-facing text `หมดเวลาการสอบแล้ว`.

Verification:
- Ran PHP lint on `models/Project.php`: passed.
- Checked `getProjectRuntimeStatus()` for manual override with a past end time: returned `allowed=false`, `status=closed`, and `message=หมดเวลาการสอบแล้ว`.

## 2026-05-12 - Manual Override Entry Close Enforcement

Completed:
- Updated `PublicExamController::entry()` so an existing in-progress session redirects back to the exam only while `getProjectRuntimeStatus()` still allows entry.
- Updated the public entry status banner to show the runtime close reason for manual override projects.
- Blocked the public participant autocomplete API when the project runtime status is not allowed, keeping entry behavior and API access aligned.

Verification:
- Ran PHP lint on `controllers/PublicExamController.php`: passed.
- Ran PHP lint on `api/exam.php`: passed.
- Ran PHP lint on `views/exam/entry.php`: passed.
- Checked `getProjectRuntimeStatus()` for `manual_override=1`, `status=active`, and a past end time: returned `allowed=false`, `status=closed`.

## 2026-05-11 - API Compatibility Fix (Production 404)

Completed:
- Added compatibility routes in `index.php` for `/api/upload_asset.php` and `/api/save_template.php`.
- Resolved the 404 Not Found error when uploading assets in production where legacy paths might still be called or cached.

## 2026-05-11 - TCPDF Template Builder Schema

Completed:
- Reworked `cert_templates` schema for millimeter-based certificate builder data: orientation, size, background, and `elements` JSON.
- Added `views/certificates/template_builder.php` with A4 794px canvas, draggable/resizable elements, properties panel, background controls, and save/preview actions.
- Added `api/save_template.php` and `api/upload_asset.php` with login and CSRF checks.
- Routed template create/edit/list actions to the new builder flow.
- Added TCPDF certificate export and inline preview routes.
- Added certificate element rendering helpers for text, image, QR code, line, background color, and background image.
- Added public `/verify?t=TOKEN` route compatibility.
- Added `views/public/verify.php` as the public verification entrypoint backed by `getCertificateData()`.

Verification:
- Ran PHP lint on `models/CertTemplate.php`: passed.
- Ran PHP lint on `models/Certificate.php`: passed.
- Ran PHP lint on `controllers/TemplateController.php`: passed.
- Ran PHP lint on `controllers/CertificateController.php`: passed.
- Ran PHP lint on `controllers/PublicExamController.php`: passed.
- Ran PHP lint on `views/public/verify.php`: passed.
- Ran PHP lint on `views/certificates/template_builder.php`: passed.
- Ran PHP lint on `api/save_template.php`: passed.
- Ran PHP lint on `api/upload_asset.php`: passed.
- Ran PHP lint on `index.php`: passed.

Checkpoint:
- Updated `docs/ai-handoff/CURRENT_STATE.md` with the current certificate builder/export checkpoint.
- Git checkpoint commit was not created because the repository has many unrelated existing dirty changes.

## 2026-05-11 - Certificate Thai Text Encoding Repair

Completed:
- Restored certificate template/admin UI files to readable UTF-8 Thai text.
- Reapplied certificate render QR fallback and image-waiting PDF generation logic after the encoding repair.
- Re-added certificate template preview handling in `TemplateController`.

Verification:
- Ran PHP lint on `views/certificates/render.php`: passed.
- Ran PHP lint on `views/certificates/templates.php`: passed.
- Ran PHP lint on `controllers/TemplateController.php`: passed.
- Ran PHP lint on `controllers/PublicExamController.php`: passed.
- Ran `git diff --check` on the touched certificate files: passed.

## 2026-05-11 - Certificate QR and Verify Download Fix

Completed:
- Fixed certificate render auto-download by defining and reusing the image list before calling html2pdf.
- Added a QR image fallback provider so certificate QR codes have a second source if the first QR service fails.
- Added preview certificate fields needed by verify/download JavaScript to avoid missing token data.

Verification:
- Ran PHP lint on `views/certificates/render.php`: passed.
- Ran PHP lint on `controllers/PublicExamController.php`: passed.



## 2026-05-10 - Priority 3 UX and Completeness Pass

Completed:

- Added exam schedule status banners to the public entry page for scheduled, closed, and draft states.
- Passed `getProjectRuntimeStatus()` from `PublicExamController::entry()` and blocked POST starts when the runtime status is not allowed.
- Updated exam warning timers to use each project's `warning_before` threshold instead of fixed warning text/timing.
- Cleaned up the unused empty `BaseModel` inheritance pattern by removing empty model classes and deleting `models/BaseModel.php`.

Verification:

- Ran PHP lint across all PHP files in the project: passed.
- Ran `node --check assets/js/exam.js`: passed.
- Confirmed no runtime `BaseModel` references remain.
- Ran `git diff --check`: passed; only existing Windows LF/CRLF conversion warnings remain.

## 2026-05-10 - Priority 2 Logic Bugfix Pass

Completed:

- Aligned project `warning_before` defaults and fallbacks to the schema value of 30 minutes.
- Updated project runtime status logic so `manual_override` can force open draft or closed projects until `exam_end` has passed.
- Reworked participant duplicate checks into separate name, email, and ID-card queries with reason-specific messages.
- Fixed exam progress counting by converting the selector result to an array before filtering checked/filled answers.
- Changed session fingerprint redirects so admin sessions go to admin login while public sessions return to the landing page.

Verification:

- Ran PHP lint on `models/Project.php`, `models/Participant.php`, and `config/session.php`: passed.
- Ran `node --check assets/js/exam.js`: passed.
- Ran `git diff --check`: passed; only existing Windows LF/CRLF conversion warnings remain.

## 2026-05-10 - Priority 1 Security Token and Certificate Race Hotfix

Completed:

- Historical note: participant access token generation was temporarily changed to long random hex tokens; this is superseded by the 2026-05-11 rule requiring 6-digit numeric exam access codes.
- Wrapped `importParticipants()` in a transaction and added per-row savepoints so failed rows can be skipped without committing a partial broken import state.
- Replaced certificate `verify_token` generation with 64-character secure random hex tokens.
- Added `SELECT ... FOR UPDATE` locking around project certificate sequence reads before certificate number generation.
- Historical note: `setup/reset-tokens.php` previously migrated participant tokens to long values; this is superseded by the 2026-05-11 reset logic that migrates non-6-digit values to 6-digit numeric codes.
- Verified `database/schema.sql` already defines `access_token VARCHAR(64)` and `verify_token VARCHAR(64)`.

Verification:

- Ran PHP lint on `models/Participant.php`, `models/Certificate.php`, and `setup/reset-tokens.php`: passed.
- Ran `git diff --check`: passed; only existing Windows LF/CRLF conversion warnings remain.
- Confirmed no legacy `generateToken()` helper calls remain in `models/` or `setup/`.

Next:

- Run `php setup/reset-tokens.php` once in the target database environment before go-live to migrate old participant tokens to 6-digit numeric codes.

## 2026-05-09 - Master Prompt Structure Alignment Pass 2

Completed:

- Moved admin login rendering into `views/auth/login.php` and implemented `AuthController::login()`.
- Kept `admin/login.php` as a thin compatibility wrapper through `AuthController`.
- Moved public exam rendering to master prompt paths under `views/exam/`: `entry.php`, `start.php`, and `result.php`.
- Moved public certificate verification rendering to `views/certificates/verify.php`.
- Removed obsolete `views/public/` compatibility wrappers after controllers were updated to master prompt view paths.
- Added reserved master prompt view files: `views/participants/import.php`, `views/questions/form.php`, and `views/certificates/templates.php`.
- Added master prompt asset/library folders and placeholders: `assets/img/`, `assets/img/cert-templates/`, `lib/tcpdf/`, `lib/phpqrcode/`, and `lib/PhpSpreadsheet/`.
- Added `assets/css/globals.css` and `assets/css/custom.css` alongside the existing Tailwind `input.css`.

Next:

- Continue replacing compatibility wrappers with direct controller/view actions where useful.
- Run PHP syntax checks and full local browser testing through login, exam submission, certificate issue, and verification.

## 2026-05-09 - Master Prompt Structure Cleanup Pass 3

Completed:

- Removed obsolete `views/public/` files after controllers were already pointing at master prompt view paths.
- Changed root `index.php` from the legacy dashboard HTML passthrough to the app entry redirect.
- Removed legacy `views/dashboard/index.html`.
- Consolidated project create/edit rendering into `views/projects/create.php` and removed `views/projects/edit.php` plus `views/projects/_form.php`.
- Consolidated question create/edit rendering into `views/questions/form.php` and removed `views/questions/create.php`, `views/questions/edit.php`, and `views/questions/_form.php`.
- Added missing master prompt view placeholder `views/exam/verify.php`.

Notes:

- `admin/`, `public/`, `setup/`, and `src/` contain runtime entry wrappers, setup scripts, and service helpers used by the current app; they are not in the master prompt structure block but should not be deleted without replacing routing/service usage.
- `views/participants/create.php`, `views/participants/edit.php`, and `views/participants/_form.php` are still used by `ParticipantController`; keep until participant create/edit is redesigned into a master-prompt-aligned flow.
- `assets/css/input.css` is still kept because the master prompt's Tailwind build section references it, even though the structure block lists `globals.css` and `custom.css`.
- `assets/img/logo.png` is still missing; add a real logo asset when available.

Next:

- Decide whether to keep compatibility route wrappers (`admin/*`, `public/*`) or replace them with a single front controller router.
- Refactor participant create/edit views if strict view tree matching is required.

## 2026-05-09 - Master Prompt Structure Cleanup Pass 4

Completed:

- Replaced `admin/*` and `public/*` compatibility route wrappers with a central route table in root `index.php`.
- Updated `.htaccess` so missing file/directory requests route through `index.php`.
- Removed empty `admin/` and `public/` route wrapper directories after deleting their PHP wrappers.
- Consolidated participant list/create/edit rendering into `views/participants/index.php`.
- Removed `views/participants/create.php`, `views/participants/edit.php`, and `views/participants/_form.php`.
- Added `assets/img/logo.png` so the master prompt asset path now has a concrete logo file.

Notes:

- Existing URLs such as `/admin/projects/edit.php` and `/public/exam.php` are preserved by the front controller route map, not by physical wrapper files.
- `src/` remains because current controllers depend on the service/helper layer. Removing it requires moving that logic into models/controllers first.
- `setup/`, `.agents/`, `WORKLOG.md`, and setup docs remain as development tooling outside the app runtime structure.

Next:

- Move service functions from `src/` into models/controllers if strict master prompt structure is required.
- Browser-test the front controller routing through admin login, project CRUD, participant CRUD, exam taking, result, certificate issue, and verify.

## 2026-05-09 - Master Prompt Structure Cleanup Pass 5

Completed:

- Moved shared helper functions from `src/helpers.php` into `config/config.php`.
- Moved auth/session helper functions from `src/Auth.php` into `models/Admin.php`.
- Moved project, participant, question, certificate, and exam service functions into their corresponding model files.
- Moved dashboard query helpers into `DashboardController.php`.
- Moved report query helpers into `ReportController.php`.
- Updated controllers, API endpoints, setup scripts, and `index.php` to stop requiring `src/*`.
- Removed the `src/` directory.
- Cleaned duplicate config requires created during the helper migration.

Notes:

- The runtime app structure now follows the master prompt folders: `index.php`, `config/`, `controllers/`, `models/`, `views/`, `assets/`, `uploads/`, `lib/`, `api/`, `database/`, Tailwind/PostCSS config, and `.htaccess`.
- Development-only files remain: `.agents/`, `setup/`, `WORKLOG.md`, `SETUP-LOCAL.md`, `.gitignore`, and `master-prompt-examcert.md`.

Next:

- Run full browser testing with a local database because syntax checks cannot verify DB-backed routes or the rewritten front controller flow end to end.

## 2026-05-09 - Master Prompt 2 Alignment

Completed:

- Updated `master-prompt-examcert.md` with design-system details that existed only in `master-prompt-examcert2.md`.
- Added Tailwind CDN setup, Google Font, Font Awesome, SweetAlert2, and custom CSS guidance.
- Added color reference, Prompt 2 component patterns, custom CSS notes, and footer JS CDN include guidance.
- Fixed the folder-structure code block so the app tree and architecture note are separated cleanly.
- Updated the app tree to reflect the current front-controller structure and runtime folders.

Next:

- Keep `frontend-guide-examcert.md` as the detailed UI component reference linked from the master prompt.

## 2026-05-09 - Remaining Feature Implementation + Bug Hardening

Completed:

- Fixed shared layout structure so `views/layout/header.php` opens the HTML/body and `views/layout/footer.php` closes scripts/body/html without duplicate script includes.
- Updated `views/layout/admin.php` to use the corrected header/footer contract.
- Added front-controller routes in `index.php` for participant import, project schedule/manual controls, certificate templates, certificate download, and revoke/restore actions.
- Added project schedule/runtime helpers in `models/Project.php` for manual override, open/closed/scheduled status, warning time, auto-submit on close, force status, and exam extension.
- Added schedule/manual-control actions to `ProjectController` and rebuilt `views/projects/detail.php` with runtime status, schedule settings, force open/close, extend exam, and delete controls.
- Added participant CSV import workflow in `ParticipantController` and `views/participants/import.php`, including duplicate/invalid row summaries and `import_batch` support.
- Added PhpSpreadsheet-aware Excel handling that works when `lib/PhpSpreadsheet` is installed; CSV remains available with current local placeholder libs.
- Added certificate template model/controller/view CRUD through `models/CertTemplate.php`, `TemplateController`, and `views/certificates/templates.php`.
- Added certificate PDF generation in `models/Certificate.php`, with TCPDF/phpqrcode usage when local libs are installed and a minimal PDF fallback when they are not.
- Added certificate download and revoke/restore actions in `CertificateController` and updated `views/certificates/index.php`.
- Replaced reserved API placeholders with live endpoints:
  - `api/exam.php?action=check_time`
  - `api/participant.php?action=lookup`
  - `api/certificate.php?action=verify`
- Fixed admin sidebar session keys to match the current auth session structure.
- Fixed public exam controller result/take context so participant/certificate data is available to views.
- Extended `jsonResponse()` with an optional HTTP status code.

Verification:

- Ran `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe -l` across all PHP files: passed.
- Ran `git diff --check`: no whitespace errors; only Git LF/CRLF warnings from Windows.
- Ran setup scripts:
  - `setup/install.php`
  - `setup/create-admin.php admin Admin@123456 Admin admin@example.com`
  - `setup/seed-sample.php`
- Browser-smoke tested via `Invoke-WebRequest`:
  - `/admin/login.php` returned 200.
  - Admin login with `admin / Admin@123456` returned 302 redirect.
  - `/admin/projects/` after login returned 200.
  - `/admin/projects/detail.php?id=1` after login returned 200.
  - `/admin/participants/import.php?project_id=1` after login returned 200.
  - `/admin/certificates/templates.php` after login returned 200.
  - `/public/exam.php?project=TEST-001` returned 200.

Notes:

- Local library folders currently contain placeholders only (`.gitkeep`), so TCPDF/phpqrcode/PhpSpreadsheet integration paths are implemented but fallbacks are used until the real libraries are installed under `lib/`.
- Many older Thai UI strings still contain mojibake in untouched legacy views/controllers; touched views were stabilized enough for runtime use, but a dedicated encoding cleanup pass is still recommended.
- `git status` remains intentionally dirty from the larger master-prompt refactor; no destructive cleanup or revert was performed.

Next:

- Do a full browser walkthrough for project CRUD, participant CRUD/import, question CRUD, exam submit/result, certificate issue/download/verify/revoke, and report/export.
- Install real local libraries into `lib/tcpdf`, `lib/phpqrcode`, and `lib/PhpSpreadsheet` if production-grade PDF/QR/Excel support is required.
- Run a focused mojibake cleanup pass across remaining Thai UI strings.

## 2026-05-09 - Browser Flow Hardening Pass 1

Completed:

- Read `.agents/skills/examcert-php/SKILL.md` before continuing work, per project workflow.
- Ran a public exam HTTP flow using sample project `TEST-001` and the seeded participant token.
- Fixed public exam start failure caused by `models/ExamSession.php` not loading `Project.php`, `Participant.php`, and `Question.php` dependencies before calling runtime/project/question helpers.
- Submitted a sample exam through `/public/take-exam.php` and reached `/public/result.php` with a passing score.
- Fixed malformed public exam view output caused by literal `` `r`n `` text in `views/exam/entry.php` and `views/exam/result.php`.
- Fixed `views/exam/start.php` so the shared footer is included after the page inline script instead of before it.
- Wired `views/exam/start.php` to poll `api/exam.php?action=check_time` every 30 seconds during the exam and auto-submit when the API says the session/project should close.
- Tested certificate PDF download and public verify URL after issuing a certificate.
- Fixed certificate revoke/restore fatal error by aligning `markCertificateRevoked()` with the current `certificates` schema, which has `is_revoked` and `revoke_reason` but no `revoked_at` column.
- Tested certificate revoke and restore through admin HTTP POSTs.
- Tested admin report and exam-session CSV export routes.
- Tested API responses:
  - `api/exam.php?action=check_time&session_id=2`
  - `api/certificate.php?action=verify&token=...`

Verification:

- `php -l` across all PHP files: passed.
- `git diff --check`: no whitespace errors; only Git LF/CRLF warnings from Windows.
- HTTP smoke tests passed:
  - `/public/exam.php?project=TEST-001`
  - `/public/take-exam.php?session_id=1`
  - `/public/result.php?session_id=2`
  - `/admin/questions/?project_id=1`
  - `/admin/reports/`
  - `/admin/reports/export.php`
  - `/admin/exam-sessions/export.php`
  - `/admin/certificates/download.php?token=...`
  - `/public/verify.php?token=...`

Notes:

- A certificate was issued in the local database for session `2`: `CERT-2026-00001`.
- Revoke/restore was exercised and restored to valid state afterward.
- Some Thai UI strings remain mojibake in legacy/touched views and controllers. They are not syntax blockers, but they should be cleaned in a dedicated UI text pass.

Next:

- Continue the full browser walkthrough for project CRUD, participant CRUD/import with uploaded CSV, question CRUD via forms, template CRUD, and invalid/edge cases.
- Run invalid CSRF/upload/expired exam/max attempts tests.
- Continue replacing mojibake strings in user-facing UI.

## 2026-05-09 - Browser Flow Hardening Pass 2

Task Status:

- Completed: Project CRUD HTTP form flow.
- Completed: Question CRUD HTTP form flow.
- Completed: Participant create/edit HTTP form flow.
- Completed: Participant CSV multipart upload import.
- Completed: Certificate template create/edit HTTP form flow.
- Completed: Invalid CSRF, invalid upload type, closed exam, and max-attempts checks.
- Completed: PHP lint and whitespace hardening checks.

Completed:

- Read `.agents/skills/examcert-php/SKILL.md` before starting the work session.
- Confirmed the working tree is still intentionally dirty from the master-prompt refactor and avoided reverting any existing changes.
- Created a project through `/admin/projects/create.php` with an HTTP form POST and confirmed redirect to project detail.
- Created and edited a question through `/admin/questions/create.php` and `/admin/questions/edit.php`, then confirmed the question list reflected the changes.
- Created and edited a participant through `/admin/participants/create.php` and `/admin/participants/edit.php`, then confirmed the participant list reflected the changes.
- Uploaded a real CSV file through `/admin/participants/import.php?project_id=4` with multipart form data and confirmed the import summary rendered.
- Created and edited a certificate template through `/admin/certificates/template-create.php` and `/admin/certificates/template-edit.php`, then confirmed the template list reflected the changes.
- Tested invalid login CSRF. The request stayed on login and showed an error instead of authenticating.
- Tested invalid participant import upload extension. The import page showed `Only CSV, XLSX, or XLS files are allowed.`
- Tested max-attempt blocking on seeded project `TEST-001` after submitted attempts.
- Tested closed exam blocking by force-closing project `4`, attempting public exam entry, then restoring the project to active status.

Verification:

- `php -l` across all PHP files: passed.
- `git diff --check`: no whitespace errors; only Git LF/CRLF warnings from Windows.
- `git diff --stat` reviewed after the pass.

Notes:

- Local test data was added during HTTP workflow testing, including an automated project, participants, questions, and a certificate template.
- Temporary CSV/cookie files under `tmp/` were removed after the upload test.
- No source code changes were required in this pass; it was primarily route/form/browser-flow validation.

Next:

- Continue with focused cleanup of mojibake UI strings in user-facing views/controllers.
- Add tighter runtime checks for upload MIME sniffing if production hardening is desired beyond extension validation.
- Consider a cleanup/reset strategy for local test data only after an explicit checkpoint or user approval.

## 2026-05-09 - Localization and UI Stabilization

Completed:

- Read `.agents/skills/examcert-php/SKILL.md` before starting the work session.
- Performed a comprehensive cleanup of Thai character encoding (mojibake) across controllers and models.
- Standardized UI labels, system messages, and feedback notifications into proper UTF-8 Thai.
- Modernized administrative interfaces (Participants, Questions, Certificates) using the 'Orange-White Premium' design system.
- Implemented a "Project Selector" view to handle context-less navigation from the sidebar to project-scoped modules.
- Refactored `ParticipantController` and `QuestionController` to use the new project selector instead of redirecting or exiting on missing `project_id`.
- Updated `Certificate Template` management view with full Thai translation and premium styling.
- Modernized `Certificate Verification` public-facing page for better user experience and mobile responsiveness.
- Fixed mojibake in `models/Question.php` and `models/Participant.php` validation messages.
- Updated `WORKLOG.md` with recent progress.

Notes:

- The application is now visually consistent and localized for Thai users.
- Known issues with context-less navigation have been resolved by the project picker.
- CSV/Excel library integration is pending real file installation but the logic is ready.

Next:

- Final E2E testing of the full workflow: Project Creation -> Participant Import -> Question Setup -> Exam Flow -> Certificate Issuance.
- Install real libraries for TCPDF and PhpSpreadsheet.
- Review any remaining edge cases in Thai encoding for newly added features.
## 2026-05-09 - Certificate Designer Studio & UI Optimization

Completed:

- Read `.agents/skills/examcert-php/SKILL.md` and `master-prompt-examcert.md` to align with the professional designer requirements.
- Implemented **Certificate Designer Studio (Studio Mode)**: A 3-column visual drag-and-drop workspace for certificate templates.
- Integrated **Google Fonts (Thai)**: Added Sarabun, Noto Sans Thai, Kanit, Prompt, Mitr, Chakra Petch, and Srisakdi with real-time font preview.
- Added **Precision Property Panel**: A right-side menu for pixel-perfect adjustment of coordinates (X, Y) and font sizes (pt) with two-way sync.
- Performed **SweetAlert Audit**: Standardized notifications across `AuthController`, `ProjectController`, `ParticipantController`, `QuestionController`, and `TemplateController`.
- Restored **Delete Confirmation** for participants and questions using SweetAlert modals.
- Fixed **CSRF & Redirection** logic to ensure flash messages trigger the correct alert types.
- Aligned UI feedback with Master Prompt: Using **Modals for Errors** and **Toasts for Success/Info**.

Notes:

- The Designer Studio now converts pixels on the canvas to millimeters (A4) accurately for TCPDF.
- Added support for QR Code resizing directly from the designer properties panel.
- Standardized the use of `setFlash()` for all CRUD operations to ensure user feedback is never missed.

Next:

- Finalize Thai Font (TTF) installation in `lib/tcpdf/fonts/` to match the Google Fonts selected in the UI.
- Conduct a full end-to-end test of the PDF generation with the new coordinates.
- Update documentation for the new Studio workflow.
## 2026-05-09 - Client-side Excel & System Hardening

Completed:

- Read `.agents/skills/examcert-php/SKILL.md` before starting the work session.
- **Master Prompt Update**: Changed the Excel processing requirement from server-side (PhpSpreadsheet) to client-side using **SheetJS (xlsx.full.min.js)**.
- **Global Excel Helpers**: Implemented `window.excel.export()` and `window.excel.parse()` in `assets/js/app.js`.
- **Excel Export**: Added "Export to Excel" buttons to Participant List and Overall Reports views.
- **Excel Import**: Rewrote the Participant Import workflow to use client-side file parsing and JSON-based AJAX import, removing the dependency on PhpSpreadsheet for v1.
- **Security Hardening**: Implemented **Session Fingerprinting** (User-Agent check) in `config/session.php` to prevent session hijacking.
- **Exam Integrity**: Added **Auto-save** logic to `assets/js/exam.js` that records student answers in real-time via `api/exam.php?action=save_answer`.
- **Exam Heartbeat**: Improved the exam heartbeat to sync state and detect disconnection.
- **Validation Feedback**: Updated SweetAlert logic in `app.js` and `footer.php` to support detailed error lists (Array feedback).
- **Cleanup**: Removed obsolete `$flash` variable checks from views and controllers to prevent PHP warnings.

Notes:

- The system is now significantly more responsive for data-heavy tasks like importing/exporting.
- Student progress is now protected against accidental page refreshes or network drops.
- Permission checks for admin-only features remain enforced via `requireLogin()`.

## 2026-05-09 - UI Polish & Critical Bug Fixes

Completed:
- **UI Architecture**: Embedded Tailwind components directly in views to bypass CDN processing limits. Added premium shadows to global `header.php` config to prevent `@apply` errors.
- **Exam Engine Fix**: Resolved **404 Not Found** errors for `save_answer` AJAX calls by implementing dynamic `BASE_URL` resolution.
- **JS Stability**: Fixed "Identifier 'BASE_URL' has already been declared" by moving global constants to `window` properties in `app.js`.
- **Exam Session Management**: Added the ability for admins to delete individual exam sessions and their associated logs.
- **Auto-Issuance**: Implemented automatic certificate generation logic.
- **Client-Side PDF**: Transitioned from server-side TCPDF to client-side `html2pdf.js` for both score reports and certificates, ensuring premium UI fidelity and zero server dependencies.
- **UI Refactoring**: Cleaned up redundant HTML structures and standardized centering.
- **Bug Fix**: Resolved Fatal Error "Call to undefined function getExamSession()".
- **Bug Fix**: Fixed syntax error in `PublicExamController.php` (missing closing brace).
- **Question Logic**: Added the missing `is_active` toggle to the question management form.
- **Workflow Documentation**: Created **`workflow.md`** to outline the complete operational lifecycle of the system.
- **Branding & Alignment**: Fixed centering issues and background rendering on entry and result pages.

Notes:
- The system is now significantly more robust against Tailwind CDN quirks.
- All core exam flows are verified and stable.

Next:
- Security audit of file uploads.
- Thai font rendering optimization for TCPDF.

## 2026-05-09 - Result Interface Modernization

Completed:
- Read `.agents/skills/examcert-php/SKILL.md` to ensure workflow compliance.
- **Result UI Overhaul**: Modernized `views/exam/result.php` to match the "Orange-White Premium" mockup.
- **Score Visualization**: Implemented an animated score ring (SVG) and a threshold-aware progress bar with a 70% marker line.
- **Conditional UI**: Added logic to switch between "Pass" (success theme with certificate access) and "Fail" (error theme with retry guidance) states.
- **Answer Logging**: Added `getSessionAnswerLogs()` to `models/ExamSession.php` and updated `PublicExamController` to fetch session-specific response data.
- **Premium Aesthetics**: Integrated `shadow-card-lg`, `shadow-cert`, and animated background meshes.
- **Confetti Effect**: Added an animated confetti burst for successful exam completions.
- **Feature Toggle**: Temporarily disabled the "Answer Review" section per user request.
- **System Stability**: Verified PHP syntax and ensured all data mapping from the session model is accurate.

Notes:
- The result page now features a live "Certificate Preview" for passing participants.
- Threshold marker is dynamically calculated based on the project's `pass_score` setting.

Next:
- Re-enable and style the "Answer Review" section when requested.
- Optimize certificate PDF generation to match the high-fidelity web preview.

## 2026-05-09 - Bug Fix: Answer Verification Case Sensitivity

Completed:
- **Fixed Answer Logic**: Updated `isAnswerCorrect()` in `models/ExamSession.php` to use case-insensitive normalization.
- **Improved Robustness**: Standardized answer comparison to handle variations in case (e.g., 'a' vs 'A') which previously caused correct multiple-choice answers to be marked as wrong if the database used a different case than the frontend keys.
- **Unicode Support**: Ensured UTF-8 compatibility for case-insensitive comparisons in fill-in-the-blank questions.

## 2026-05-09 - Rollback Before Architecture Refactor

Completed:
- Rolled the repository back from `784ce7d checkpoint: harden examcert architecture phase 1` to `c14a213 fix: ensure scoring uses auto-saved answers from DB and re-enable review tab for verification`.
- Removed the uncommitted architecture/refactor work that was created after the checkpoint, including temporary service/model/controller-helper files.
- Confirmed the working tree was clean immediately after rollback.

Notes:
- This is now the intended resume point before the architecture review/refactor attempt.
- Open IDE tabs may still show files that no longer exist after rollback; refresh the editor if needed.

Next:
- Continue from `c14a213` and re-apply only targeted fixes intentionally.
- If refactoring is requested again, create a smaller checkpoint before each phase.

## 2026-05-09 - Entry Name Search Privacy Fix

Completed:
- Updated the public exam entry page so participant names are not loaded into the browser on initial page load.
- Added a project-scoped participant search API that only returns matching names after the user types at least 2 characters.
- Reworked `views/exam/entry.php` autocomplete to populate the datalist from the search endpoint instead of displaying everyone.
- Cleaned the visible Thai text on the entry page while preserving the existing 6-character exam token field.
- Replaced the browser-native datalist tooltip with a custom styled dropdown panel under the name field.

Verification:
- Ran PHP lint on `views/exam/entry.php`, `api/participant.php`, and `models/Participant.php`: passed.
- Ran `git diff --check`: passed; only Windows LF/CRLF warnings remain.
- Re-ran PHP lint on `views/exam/entry.php` after the custom dropdown change: passed.

Next:
- Browser-test the entry page by typing part of a participant name, selecting the matching name, entering the 6-digit numeric exam code, and starting the exam.

## 2026-05-09 - Feature Bugfix and Security Hotfix Pass

Completed:
- Added runtime constants and helpers in `config/config.php`, including `APP_ENV`, `CERT_UPLOAD_PATH`, `TEMPLATE_UPLOAD_PATH`, `isLocalEnvironment()`, and `abortResponse()`.
- Changed `index.php` so `display_errors` is enabled only in local/development mode.
- Hardened session fingerprinting to tolerate missing user-agent values.
- Standardized new participant tokens and sample seed token to 6 characters to match the public entry form.
- Locked `api/participant.php?action=list` behind admin API login with JSON 401 responses while keeping the limited public `search` endpoint for entry autocomplete.
- Added CSRF validation and session-question ownership validation to `api/exam.php?action=save_answer`.
- Added `sessionHasQuestion()` to `models/ExamSession.php`.
- Fixed certificate generation risks by defining the upload path dependency and moving certificate render/download failures to controlled responses.
- Fixed certificate signature font scope in `models/Certificate.php`.
- Hardened certificate template uploads with upload error checks, 5MB size limit, MIME/image validation, allowed PNG/JPG/WEBP types, and randomized filenames.

Verification:
- Ran PHP lint across all PHP files: passed.
- Ran `git diff --check`: passed; only Windows LF/CRLF warnings remain.
- HTTP smoke checked unauthenticated participant `list` API: returns 401.

Notes:
- Existing participants with old 32-character tokens may need `setup/reset-tokens.php` run once if the local database still contains long tokens.
- Entry page search remains public but limited to project-scoped, term-based results after at least 2 characters.


## 2026-05-09 - Final Stabilization & UX Hardening

### Completed:
- **Navigation Integrity**:
  - Implemented multi-layered **Browser Back Button Locking** using `popstate` and `beforeunload`.
  - **Enhanced**: Moved locking script to immediate execution (IIFE) and used double-push history state to handle rapid navigation attempts.
  - **Server-Side Lockdown**: Added automatic redirect in `entry()` controller to force participants back to an active session if they try to navigate away.
  - Handled "back-spamming" behavior to ensure students stay on the exam page.
  - Added `isSubmitting` flag to allow seamless navigation during legitimate form submissions.
- **Session Resumption**:
  - Refactored `startExamSession` to automatically resume `in_progress` sessions for participants.
  - This prevents accidental attempt loss if a user navigates away or closes the tab.
- **Project Visibility**:
  - Fixed Landing Page issue where active projects weren't appearing by correctly fetching data in `index.php`.
- **API & Routing Stability**:
  - Resolved Fatal Errors in `index.php` routing by hardening the match-condition check.
  - Corrected view paths for the landing page and integrated it with the global `header.php`.
  - Re-integrated `search_participants` API action to support participant autocomplete.

### Verification:
- Verified that pressing the back button triggers a warning and stays on the page.
- Verified that active projects appear on the landing page.
- Verified that logging back in after navigating away resumes the existing session.
- Performed final code audit for redundant HTML boilerplate: all public views now share a single, clean header/footer system.

- **Landing Page Refinements**:
  - Updated hero text and badges to match organizational branding ("เธเธฒเธเธเธฃเธดเธซเธฒเธฃเธ—เธฃเธฑเธเธขเธฒเธเธฃเธเธธเธเธเธฅเนเธฅเธฐเธเธดเธ•เธดเธเธฒเธฃ").
  - Implemented **Smooth Scrolling** for the "Enter Exam Room" navigation button.
  - Standardized landing page styles and animations for a more premium feel.

## 2026-05-09 - Routing Stabilization & UI Polish

### Completed:
- **Routing Stability**: Restored and hardened the front-controller system after troubleshooting routing conflicts in the Windows/Laragon environment.
- **Forbidden Error Fix**: Resolved 403 Forbidden issues by normalizing URI detection and trimming trailing slashes to prevent Apache directory interference.
- **UI Aesthetics**: Modernized the Result and Verification pages by removing gradients from key success/fail icons, opting for a cleaner premium solid-color design.
- **Environment Parity**: Ensured `index.php` and `.htaccess` align with the master-prompt architecture while remaining compatible with local subdirectories.

### Verification:
- Confirmed that `/admin/projects` and other core routes resolve correctly without triggering 403 errors.
- Verified that UI icons in `views/exam/result.php` and `views/certificates/verify.php` render with the new solid-color theme.
- Performed `git restore` on core routing files to ensure a clean state based on the GitHub master.

### Next:
- Conduct full end-to-end testing of all modules (Projects, Participants, Questions, Exams, Certificates) to ensure no regressions.
- Verify certificate issuance flow with the new UI changes.

## 2026-05-11 - Priority 4 Production Hardening

Completed:
- Added session-based public exam entry rate limiting: 10 attempts per IP within 5 minutes.
- Added a Content Security Policy header in `config/session.php`.
- Added `uploads/.htaccess` to block script execution and restrict allowed file types.

Verification:
- Ran PHP lint on `controllers/PublicExamController.php`: passed.
- Ran PHP lint on `config/session.php`: passed.

Notes:
- Existing dirty files `config/config.php` and `setup_db.php` were present before this pass and were not modified for this task.

## 2026-05-11 - Production Readiness Blocker Fixes

Completed:
- Removed root web database installer `setup_db.php`.
- Made `BASE_URL` and database credentials configurable through environment variables.
- Enabled secure session cookies automatically outside local/dev environments or when HTTPS is active.
- Added root `.htaccess` deny rules for source/config/setup/docs/log/dev files.
- Added CSRF validation to exam-session deletion and participant import API.

Verification:
- Ran PHP lint on modified PHP files: passed.
- Ran PHP lint across all PHP files: passed.
- Ran `git diff --check`: passed; only Windows LF/CRLF conversion warnings remain.

## 2026-05-11 - Production Config Hardcode

Completed:
- Changed `config/config.php` to use hardcoded production mode and production `BASE_URL` placeholder.
- Changed `config/database.php` to use hardcoded production database constants instead of environment variables.

Notes:
- Replace `https://your-domain.com`, `examcert_user`, and `CHANGE_THIS_STRONG_PASSWORD` with the real production values before upload.

## 2026-05-11 - Guarded Web Database Installer

Completed:
- Added `install-database.php` for production web-based database setup when CLI is unavailable.
- Added `SETUP_WEB_TOKEN` in `config/config.php` as a required setup secret.
- Added a lock file mechanism at `logs/database-installed.lock` to prevent accidental repeated setup.

Notes:
- Replace `CHANGE_THIS_SETUP_TOKEN` with a strong random secret before upload.
- Remove `install-database.php` from the production server immediately after successful setup.

## 2026-05-11 - CSP and Template Upload Diagnostics

Completed:
- Updated CSP `font-src` to allow Tabler icon fonts from `cdn.jsdelivr.net`.
- Updated CSP `connect-src` to allow CDN source-map requests from `cdn.jsdelivr.net` and `cdnjs.cloudflare.com`.
- Added detailed server-side logging for certificate template image upload failures.

Notes:
- If image upload still fails, check `logs/app.log` for the exact upload failure reason.

## 2026-05-11 - Certificate Template Schema Compatibility Fix

Completed:
- Added missing `cert_templates` columns to `database/schema.sql`: `show_name`, `show_course`, and `show_certno`.
- Added a guarded schema compatibility check in `models/CertTemplate.php` to add those columns on existing databases before template reads/writes.

Notes:
- The reported image upload failure was caused by the template save failing after upload because the existing database table did not have `show_name`.

## 2026-05-11 - Certificate Designer Preview Toggles

Completed:
- Connected certificate designer display switches to the live preview canvas.
- Hidden disabled preview elements and their property panels immediately when a display switch is off.
- Excluded disabled elements from `layout_json` during sync.

## 2026-05-11 - Certificate Designer Logo and Preview Scale

Completed:
- Added persistent display switches for logo, signature 1, and signature 2 in certificate templates.
- Added schema compatibility columns: `show_logo`, `show_sign1`, and `show_sign2`.
- Updated the certificate designer preview to scale responsively based on the available canvas area.
- Updated drag math to respect the current responsive preview scale.

Verification:
- Ran PHP lint on `models/CertTemplate.php`: passed.
- Ran PHP lint on `views/certificates/templates.php`: passed.
- Ran local schema compatibility check and verified `show_logo`, `show_sign1`, and `show_sign2` exist.

## 2026-05-11 - Question Route Fix

Completed:
- Added missing front-controller routes for question import, create, edit, and delete actions.
- Fixed `/admin/questions/create.php?project_id=...` returning a raw 404.

Verification:
- Ran PHP lint on `index.php`: passed.
- Requested `/admin/questions/create.php?project_id=1` locally and received an auth redirect instead of route 404.

## 2026-05-11 - Admin Route Coverage Fix

Completed:
- Added missing front-controller routes for participant import, create, edit, and delete actions.
- Added missing project routes for force status and exam extension actions.
- Disabled Apache MultiViews in `.htaccess` to prevent path resolution conflicts with front-controller routing.

Verification:
- Ran PHP lint on `index.php`: passed.
- Checked local requests for question import/create, participant import/create, project force-status, and project extend. All now return auth redirects instead of route 404.

## 2026-05-11 - Six Digit Exam Access Codes

Completed:
- Changed new participant exam access codes to 6-digit numeric codes.
- Changed participant import to generate 6-digit numeric access codes.
- Updated `setup/reset-tokens.php` to convert existing non-6-digit tokens to 6-digit numeric codes.
- Updated public exam entry input to expect a 6-digit numeric code.

Verification:
- Ran PHP lint on `models/Participant.php`: passed.
- Ran PHP lint on `setup/reset-tokens.php`: passed.
- Ran PHP lint on `views/exam/entry.php`: passed.
- Ran `setup/reset-tokens.php`; updated 1 existing participant token.

## 2026-05-11 - Six Digit Exam Access Code Documentation

Completed:
- Updated `master-prompt-examcert.md` to define participant exam access codes as unique 6-digit numeric strings.
- Updated `.agents/skills/examcert-php/SKILL.md` with the Exam Access Code Policy.
- Updated `docs/database.md` and `PROJECT.md` to confirm the same rule.
- Removed old prompt guidance that required participant access tokens to be 64-character hex strings.

## 2026-05-11 - Participant Excel Export Fix

Completed:
- Added backend participant export route: `/admin/participants/export.php`.
- Changed the participant export button from client-side SheetJS export to a server-generated CSV file that Excel opens correctly.
- Added UTF-8 BOM and text-formatted exam access codes so Thai text and leading zeroes are preserved.

Verification:
- Ran PHP lint on `controllers/ParticipantController.php`: passed.
- Ran PHP lint on `index.php`: passed.
- Ran PHP lint on `views/participants/index.php`: passed.
- Requested `/admin/participants/export.php?project_id=1` locally and received an auth redirect instead of route 404.

## 2026-05-11 - Subjective Question Support

Completed:
- Added `subjective` as a supported question type.
- Updated question validation so subjective questions do not require choices or a correct answer.
- Updated the question form with a subjective option and UI logic that hides choices for subjective questions.
- Updated the exam screen to render subjective questions as a textarea.
- Updated exam submission so subjective answers are stored, are not auto-scored, and are marked `pending_manual` in `answer_logs.grading_status`.
- Added guarded schema compatibility for the `questions.type` enum and `answer_logs.grading_status`.
- Updated `database/schema.sql` for fresh installs.

Verification:
- Ran PHP lint on `models/Question.php`: passed.
- Ran PHP lint on `models/AnswerLog.php`: passed.
- Ran PHP lint on `models/ExamSession.php`: passed.
- Ran PHP lint on `views/questions/form.php`: passed.
- Ran PHP lint on `views/exam/start.php`: passed.
- Ran local schema compatibility check; `questions.type` includes `subjective` and `answer_logs.grading_status` exists.

## 2026-05-11 - Context Budget Skill Rules

Completed:
- Added context-budget and monorepo rules to `.agents/skills/examcert-php/SKILL.md`.
- Added `docs/ai-handoff/CURRENT_STATE.md` as the required phase handoff note.
- Documented that future phases must update the handoff file before changing scope.

## 2026-05-11 - Bangkok Timezone Fix

Completed:
- Set the application default timezone to `Asia/Bangkok` in `config/config.php`.
- Fixed schedule display mismatch where public entry could show `21:00` while admin showed `14:00`.

Verification:
- Ran PHP lint on `config/config.php`: passed.
- Verified `2026-05-11 14:00:00` formats as `11/05/2026 14:00` after loading config.
