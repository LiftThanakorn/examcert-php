# Current State Handoff

Last updated: 2026-05-11

## Checkpoint
- Checkpoint note updated after completing TCPDF certificate builder/export implementation.
- No Git checkpoint commit has been created in this turn because the worktree already contains many unrelated modified/deleted files.
- Safe checkpoint scope for a future commit should include only the certificate builder/export files touched in this phase.

## Current Scope
- Added TCPDF-ready certificate template builder schema and UI.
- Certificate templates now store millimeter-based background and element JSON data.
- Builder save/upload APIs are available for the new template builder screen.
- TCPDF export and preview routes now render builder elements from `cert_templates.elements`.

## Recent Completed Work
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
