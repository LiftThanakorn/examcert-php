# Current State Handoff

Last updated: 2026-05-19

## Checkpoint
- Checkpoint note updated after improving overall reports logic with SQL subqueries and adding in-progress tracking.
- No Git checkpoint commit has been created in this turn.
- Rating-scale survey question support has been added in the current working tree; no Git checkpoint commit has been created for this change.
- `install-database.php` has been added for test-machine database install/update and should be removed from public servers after use.
- Rating-scale survey UI/report refinements and production migration SQL are pending commit in the current turn.

## Current Scope
- Overall reports logic refactored to use efficient subqueries.
- Reports now include tracking for "In Progress" (กำลังสอบ) sessions.
- Participant import now supports the "Title" (คำนำหน้า) column.
- The application uses a unique session name (`EXAMCERT_SESS`) to avoid domain-wide conflicts.
- Participants can download their certificate immediately from the result page after passing.
- Participants who have already passed are blocked from re-taking the exam and redirected to their result.
- Public participant search now returns attempt eligibility status for the selected project.
- Public exam entry shows allowed attempts, used attempts, remaining attempts, and active in-progress state after selecting a participant.
- `startExamSession()` uses the same attempt status helper to enforce resume/block/new-attempt behavior.
- Production no longer requires PHP multibyte string support for application code.
- The exam page uses `jsonForScript()` with invalid UTF-8 substitution so bad data cannot leave `QUESTIONS` empty and prevent question rendering.
- Admin exam sessions now show `กำลังสอบ` for `in_progress` rows instead of the database default `fail` result.
- Entering an active exam should not immediately fail unless the session has truly expired or auto-submit-on-close is enabled.
- Multiple-choice options display as `ก/ข/ค/ง` while internal answer keys remain `a/b/c/d`.
- Exam submission accepts both Thai and English choice keys and normalizes them before grading.
- Questions now support `rating_scale`; public exam rendering shows a fixed 5-4-3-2-1 Likert scale.
- Rating-scale answers are scored directly from the submitted 1-5 value and category score totals are shown on the result page when immediate result display is enabled.
- Test installs can use `install-database.php` to create/update the configured database, run schema/data import, and apply rating-scale migration support.
- Rating-scale-only result pages hide the normal exam score ring/pass-threshold card and show a survey report card with category averages and interpretation labels.

## Recent Completed Work
- Added `database/production-rating-scale-migration.sql` for production database updates without dropping existing data.
- Refined rating-scale exam UI to use survey-specific cards and result UI to show average/interpretation instead of normal exam scoring when applicable.
- Added `install-database.php` as an ASCII/UTF-8-safe web installer for test machines with clean install and update modes.
- Installer now handles configured database creation, schema execution, optional data import, and current migrations including `rating_scale`.
- Added `rating_scale` to question type support, schema, admin question form, CSV import type documentation, and runtime enum upgrade logic.
- Added rating-scale scoring in `submitExamSession()` and prepared category score aggregation with result-page rendering.

- Refactored `ReportController.php` and `views/reports/index.php` to improve accuracy and add "In Progress" session tracking.
- Implemented "Title" (คำนำหน้า) column support in `models/Participant.php` and `views/participants/import.php`.
- Added `session_name('EXAMCERT_SESS')` to `config/session.php` to isolate the session.
- Added a "Download Certificate" button to `views/exam/result.php`.
- Implemented `getParticipantPassingSession()` in `models/ExamSession.php` and added redirect logic in `PublicExamController.php` to prevent re-taking passed exams.
- Added `getParticipantAttemptStatus()` in `models/ExamSession.php`.
- Added attempt status display to `views/exam/entry.php`.
- Added attempt status payload to `api/exam.php?action=search_participants`.
- Verified the result retry button still goes through the entry route before a new session is created.
- Removed all direct PHP multibyte string calls from tracked application code.
- Replaced admin sidebar initials generation with PHP core string/regex logic.
- Added shared text fallback and safe script JSON helpers in `config/config.php`.
- Fixed production fatal errors by removing direct PHP multibyte string calls from application code.
- Hardened exam question payload rendering for malformed UTF-8.
- Fixed admin exam session status display for active in-progress sessions.
- Fixed active-session resume logic and timer checks around `auto_submit_on_close`.
- Added Thai multiple-choice labels across exam rendering, question form, CSV import, and grading.
- Fixed exam submit reliability and mobile responsive layout in `models/ExamSession.php` and `views/exam/start.php`.
- Added `cert_number_prefix` and `cert_sequence` fields to the Project creation/edit form.
- Updated `models/Project.php` to handle `cert_sequence` persistence.
- Reconstructed `models/Project.php` to restore missing CRUD functions.
- Fixed `CERT-2568-AI-00001` hardcoded mock number in `CertificateController::previewPDF` to use current Thai year and remove 'AI'.
- Implemented browser back-button locking in `views/exam/entry.php` and `controllers/PublicExamController.php` to prevent participants from accidentally leaving the exam.
- Standardized closed-exam and manual override messaging across the public entry flow.

## Recent Completed Work
- Fixed certificate template builder selection issues caused by duplicate/missing element IDs and overlapping default placement.
- Changed the manual override expired-exam public message to `หมดเวลาการสอบแล้ว` without exposing the internal `exam_end` field name.
- Fixed manual override entry enforcement after `exam_end` has passed.
- Entry close banners now show the runtime status message in user-facing wording.
- Added `views/certificates/template_builder.php` with fixed A4 canvas scaling, draggable/resizable elements, and properties panel.
- Added `api/save_template.php` and `api/upload_asset.php` with login and CSRF validation.
- Updated `models/CertTemplate.php`, `database/schema.sql`, `TemplateController`, and routes for the new `cert_templates` structure.
- Updated `models/Certificate.php` and `CertificateController` for TCPDF certificate issue/export/preview support.
- Added `/verify?t=TOKEN`, `/certificates/export?token=TOKEN`, and `/certificates/preview_pdf?template_id=ID` route support.
- Added `views/public/verify.php`, which loads real certificate data via `getCertificateData()` and reuses the certificate verify UI.
- Certificate template UI and related controllers were restored to readable UTF-8 Thai text.
- Certificate template preview handling was re-added after the encoding repair.
- Certificate render auto-download no longer stops on an undefined `allImages` JavaScript variable.
- Certificate QR images now include a fallback provider when the first QR image source fails.
- Verify preview certificate data now includes `verify_token` and `template_id`.
- Public entry schedule display no longer converts `14:00` to `21:00` when project times are stored as local Bangkok time.
- Participant exam access codes are now 6-digit numeric strings.
- Admin routes were expanded for questions, participants, and project actions.
- Participant export now uses backend CSV download.
- Certificate designer preview/toggles were improved.
- Subjective question support was added with `pending_manual` answer logs and no auto-scoring.

## Important Constraints
- Do not scan the whole repository.
- Inspect only task-relevant files.
- Keep command output short; use bounded reads for logs.
- Update this file after each phase.
- Prefer small diffs and small checkpoint commits.
- Do not enter a new scope without updating handoff notes.

## Dirty Worktree Note
- The worktree currently has many modified files from prior implementation phases.
- Do not revert deleted or modified files unless the user explicitly requests it.
- Treat existing dirty files as user/session work and preserve them.

## Suggested Next Verification
- Browser-test template builder save flow while logged in.
- Browser-test `/certificates/preview_pdf?template_id=ID`.
- Issue a certificate from a passed exam session and verify `/verify?t=TOKEN`.
- Download `/certificates/export?token=TOKEN` and confirm Thai font rendering in the PDF.
