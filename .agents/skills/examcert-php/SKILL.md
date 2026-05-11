---
name: examcert-php
description: Use when developing the ExamCert PHP/MySQL standalone exam and certificate system.
---

# ExamCert PHP - Mandatory Enforcement v2

Use this skill for ALL work. These rules are BINDING and non-negotiable.

## Mandatory Pre-Flight Check
Before performing ANY code modification, the AI assistant MUST:
1. Read Source of Truth: `PROJECT.md`
2. Read Architecture & DB: `docs/architecture.md`, `docs/database.md`
3. Read Modular Rules: all files in `.agents/rules/` (`ui.md`, `routing.md`, `security.md`, `workflow.md`)
4. Summarize the task and relevant rules before writing code
5. Confirm Git status and preserve existing user work

---

## Knowledge Repository
- Current Status: `PROJECT.md`
- Architectural Guidelines: `docs/architecture.md`
- Database Schema Rules: `docs/database.md`
- Modular Rule Files: `.agents/rules/*.md`

---

## Workflow Policy
- Follow the Git Checkpoint Workflow strictly. See `.agents/rules/workflow.md`.
- Update `WORKLOG.md` after meaningful work.
- Maintain UI negative constraints, especially no gradients unless explicitly allowed by current project rules.
- After each phase, update `docs/ai-handoff/CURRENT_STATE.md`.
- Prefer small commits and small diffs.
- Do not continue into a new scope without writing handoff notes.

---

## Mandatory UTF-8 Encoding Policy
- All PHP, HTML, JS, CSS, Markdown, SQL, and config files MUST be read and written as UTF-8 without BOM.
- NEVER use PowerShell `Set-Content` / `Out-File` without an explicit UTF-8-without-BOM writer for files containing Thai text.
- NEVER run blind encoding conversion such as CP874/Windows-874/ANSI to UTF-8 unless the user explicitly approves a file-by-file recovery plan.
- Before editing Thai text, inspect the target lines first and preserve existing readable UTF-8 exactly.
- After editing any Thai text file, verify there is no mojibake/replacement text with a search for `เธ`, `เน€`, `๏ฟฝ`, and `�`.
- If encoding is uncertain, stop and ask. Do not "fix" encoding by guessing.

---

## Context Budget and Monorepo Rules
- You are working in a large monorepo. Context budget is limited.
- Do not scan the whole repository.
- Only inspect files directly relevant to the task.
- Keep command output short; use `tail` or equivalent bounded reads for logs.
- Before context exceeds 65%, summarize state and suggest `/compact`.

---

## Exam Access Code Policy
- Participant exam access codes must be 6-digit numeric strings.
- Generate codes with `random_int()`, pad with leading zeroes when needed, and verify uniqueness against `participants.access_token`.
- Do not use long hex tokens for participant exam entry codes.
- Long random tokens remain appropriate for certificate verification tokens only.

---

## Critical Reminder
Always prioritize project stability and design consistency over speed. Do not refactor unrelated code.
