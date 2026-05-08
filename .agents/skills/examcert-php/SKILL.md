---
name: examcert-php
description: Use when developing the ExamCert PHP/MySQL standalone exam and certificate system. Covers project exams, participant whitelists, question banks, timed exam sessions, scoring, certificate generation, public verification, admin workflows, security rules, Git checkpoint workflow, and future events-system integration notes.
---

# ExamCert PHP

Use this skill for work inside the ExamCert project.

## Core Workflow

1. Read `master-prompt-examcert.md` before major architecture or feature changes.
2. Inspect the relevant files before editing.
3. Keep changes scoped to the requested ExamCert behavior.
4. Follow the existing PHP/MySQL patterns in the repo once implementation files exist.
5. Report changed files, verification commands, and remaining work at the end of each task.

## Work Checkpoints

Before large changes, inspect git status. Preserve user work. After each meaningful milestone, summarize changed files and propose a Git checkpoint commit. If a task is long, maintain `WORKLOG.md` with completed work, pending work, and resume notes. Never run destructive Git commands unless explicitly requested.

Use these checkpoint rules:

- Run `git status --short` before large work when the directory is a Git repository.
- Run `git diff --stat` after meaningful milestones when Git is available.
- Do not auto-commit unless the user explicitly asks or approves.
- Use commit messages in the form `checkpoint: <short description>`.
- If the directory is not a Git repository, say so and use `WORKLOG.md` as the fallback resume note when needed.
- Never use `git reset --hard`, `git checkout -- <file>`, or destructive cleanup commands unless the user explicitly requests them.
- If existing changes are present, treat them as user work and avoid reverting them.

## Coding Direction

- Build ExamCert as a standalone v1 system for online exams and certificate issuance.
- Use PHP 8.1+, MySQL, PDO, prepared statements, sessions, CSRF protection, and output escaping.
- Use `password_hash()` / `password_verify()` for admin passwords.
- Call `session_regenerate_id(true)` after successful login.
- Validate uploads using MIME checks and safe renamed filenames.
- Keep SQL injection, XSS, CSRF, file upload, and session fixation risks in scope for every feature.
- Prefer clear, maintainable PHP modules over broad abstractions.

## V1 Scope

Focus on:

- Admin authentication
- Exam project management
- Participant whitelist management
- Question bank management
- Timed exam sessions
- Answer logging and scoring
- Certificate generation
- Public certificate verification
- Dashboard and basic reports

## Future Integration Note

Do not integrate with the separate `events` project in v1 unless the user explicitly asks. If requested, plan it first as a future integration where ExamCert reads event registrations and syncs them into participants without directly coupling runtime code.
