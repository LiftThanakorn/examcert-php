# ExamCert Worklog

## 2026-05-08 - Version Safety Rules

Completed:

- Added `Git Checkpoint Workflow` to `master-prompt-examcert.md`.
- Created `.agents/skills/examcert-php/SKILL.md`.
- Added Work Checkpoints rules to the ExamCert skill.

Notes:

- `C:\laragon\www\examcert` is not currently a Git repository, so Git checkpoint commits are not available yet.
- Use this file as the fallback resume note until Git is initialized.

Next:

- Initialize Git when the user wants checkpoint commits.
- Continue developing ExamCert standalone v1: exams, participants, questions, certificates, and verification.

## 2026-05-08 - Standalone v1 Prompt Direction

Completed:

- Initialized Git repository in `C:\laragon\www\examcert`.
- Created first checkpoint commit: `checkpoint: add examcert planning and safety rules`.
- Updated `master-prompt-examcert.md` title and product scope to ExamCert Standalone v1.
- Added explicit v1 features and out-of-scope boundaries.
- Added development milestones for foundation through dashboard/report work.
- Marked `events` integration as a future improvement, not a v1 dependency.

Next:

- Create the ExamCert foundation files and database schema.
- Use a Git checkpoint after each completed milestone.

## 2026-05-08 - Foundation Scaffold

Completed:

- Added `.gitignore` for logs, uploads, dependencies, generated CSS, and environment files.
- Added root `index.php` that serves the existing dashboard design without modifying `views/dashboard/index.html`.
- Added `config/config.php`, `config/database.php`, and `config/session.php`.
- Added `src/helpers.php` with escaping, redirect, CSRF, flash, logging, token, and JSON helpers.
- Added `database/schema.sql` for ExamCert Standalone v1 tables.
- Added runtime directory placeholders for logs and upload folders.

Notes:

- Existing `views/dashboard/index.html` remains untouched as requested.
- Root `index.php` currently passes through that HTML design; it can be converted to PHP views later.

Next:

- Add Admin Authentication milestone: admin model/controller, login view, logout, and protected admin pages.
