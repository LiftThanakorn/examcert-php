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

## 2026-05-08 - Admin Authentication

Completed:

- Added `src/Auth.php` for admin login state, login verification, required-login guard, and logout.
- Added `admin/login.php` with CSRF validation and password login.
- Added `admin/logout.php`.
- Added `admin/dashboard.php` as a protected page that serves the existing dashboard design without editing it.
- Added CLI-only `setup/create-admin.php` to create or update the first superadmin.

Notes:

- Create the first admin after importing `database/schema.sql`:
  `C:\laragon\bin\php\php-8.3.16-Win32-vs16-x64\php.exe setup/create-admin.php admin <password>`
- `setup/create-admin.php` is CLI-only to avoid exposing admin creation through the browser.

Next:

- Build Project Management milestone: project CRUD, schedule fields, and admin project list/detail pages.
