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

## 2026-05-08 - Project Management

Completed:

- Added `src/Project.php` for project defaults, validation, create, update, delete, detail, and list queries.
- Added admin project pages under `admin/projects/` for list, create, edit, detail, shared form, and delete.
- Added `currentAdminId()` helper to `src/Auth.php` for created-by attribution.
- Used CSRF validation on create, edit, and delete actions.
- Kept project pages admin-protected with `requireLogin()`.

Notes:

- Project delete uses the existing database cascade behavior for child data defined in `database/schema.sql`.
- UI is functional Tailwind CDN scaffolding; the preserved dashboard design remains unchanged.

Next:

- Build Participant Whitelist milestone: participant model, list/create/edit/import basics, token generation, and project-scoped participant management.

## 2026-05-08 - Participant Whitelist

Completed:

- Added `src/Participant.php` for participant defaults, validation, project-scoped list, create, update, and delete.
- Added admin participant pages under `admin/participants/` for list, create, edit, shared form, and delete.
- Added automatic `access_token` generation with `generateToken(32)` when creating a participant.
- Linked project detail pages to project-scoped participant management.
- Protected participant pages with `requireLogin()` and CSRF validation on write actions.

Notes:

- Import Excel is not implemented yet; this milestone covers manual whitelist management first.
- Participant delete may fail if related exam sessions/certificates exist, depending on database constraints.

Next:

- Build Question Bank milestone: question model, project-scoped list/create/edit/delete, JSON choices, and answer validation.

## 2026-05-08 - Question Bank

Completed:

- Added `src/Question.php` for project-scoped question list, create, update, delete, input validation, and JSON choices.
- Added admin question pages under `admin/questions/` for list, create, edit, shared form, and delete.
- Added multiple choice A-D, true/false, and fill blank support in the same model.
- Linked project detail pages to project-scoped question bank management.

Notes:

- Question import is not implemented yet; manual CRUD is available first.
- Multiple-choice choices are stored in `questions.choices` as JSON.

Next:

- Build Exam Engine milestone: public entry by project code/id, whitelist token verification, session creation, timed exam form, answer submit, scoring, and result page.

## 2026-05-08 - Exam Engine and Scoring

Completed:

- Added `src/Exam.php` for project lookup, token-based participant verification, session creation, question ordering, answer checking, scoring, and result updates.
- Added public exam entry at `public/exam.php`.
- Added public exam taking page at `public/take-exam.php`.
- Added public result page at `public/result.php`.

Notes:

- Timer is server-side through `expires_at`; client-side countdown can be improved later.
- Passed sessions can be used by the certificate milestone.

Next:

- Build Certificate Engine milestone: issue certificate from passed session, public verify token, admin certificate list.

## 2026-05-08 - Certificate Engine

Completed:

- Added `src/Certificate.php` for certificate listing, token lookup, duplicate-safe issue from passed session, verify URL, cert number, and local HTML certificate artifact.
- Added admin certificate list and issue endpoint under `admin/certificates/`.
- Added admin exam session list under `admin/exam-sessions/` with issue-certificate action for passed sessions.
- Added public certificate verification at `public/verify.php`.
- Added a default certificate template seed in `database/schema.sql` for local testing.

Notes:

- Certificate file generation currently writes an HTML artifact into `uploads/certificates/`; PDF generation can replace this later with TCPDF/mPDF.
- Certificate issue currently requires a passed, submitted session.

Next:

- Add local setup/test instructions and sample data script to make first local verification easier.
