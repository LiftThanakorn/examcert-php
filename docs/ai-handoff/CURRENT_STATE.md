# Current State Handoff

Last updated: 2026-05-12

## Checkpoint
- Checkpoint commit created after adding certificate numbering settings and fixing preview mock data.
- Recent changes include manual numbering controls (prefix/sequence) and UI polish for the certificate template builder.

## Current Scope
- Certificate numbering settings (Prefix, Next Sequence) are now configurable in Project settings.
- Certificate model (`models/Project.php`) reconstructed and hardened after previous tool failure.
- Certificate preview mock data in `CertificateController.php` now uses dynamic Thai year and standard placeholder.

## Recent Completed Work
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
