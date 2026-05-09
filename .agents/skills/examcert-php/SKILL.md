---
name: examcert-php
description: Use when developing the ExamCert PHP/MySQL standalone exam and certificate system. Covers project exams, participant whitelists, question banks, timed exam sessions, scoring, certificate generation, public verification, admin workflows, security rules, Git checkpoint workflow, and future events-system integration notes.
---

# 🛡️ ExamCert PHP - Mandatory Rules & Enforcement

Use this skill for ALL work inside the ExamCert project. These rules are BINDING.

## 🚀 Mandatory Pre-Flight Check (AI MUST DO THIS)
Before performing ANY code modification, the AI assistant MUST:
1.  **Read this SKILL.md** to refresh current project rules.
2.  **Summarize the relevant rules** for the current task in the response (e.g., "Design Rule: No gradients allowed", "Routing Rule: Do not leak absolute paths").
3.  **Confirm Git Status**: If the directory is a Git repo, check for uncommitted changes.

---

## 🎨 UI & Design Rules (Orange-White Premium)
*   **Colors**: Use Primary Orange `#E87722` (primary-400/500). Backgrounds should be White or Gray-50.
*   **NO GRADIENTS**: Do NOT use CSS gradients (`bg-gradient-to-...`). Use **Solid Colors** for all UI elements, icons, and buttons.
*   **Premium Aesthetics**:
    *   **Shadows**: Use `shadow-card` (0 1px 4px) or `shadow-card-lg` (0 8px 32px).
    *   **Radius**: Cards `rounded-xl` (12px), Buttons/Inputs `rounded-lg` (8px).
    *   **Typography**: Font 'Sarabun' or 'Noto Sans Thai'. Use `leading-relaxed`.
*   **Components**: Use SweetAlert2 for all success/error notifications. Modals for errors, Toasts for success.

---

## 🌐 Routing & Stability Rules
*   **Front Controller**: All requests must go through `index.php`.
*   **Forbidden (403) Prevention**: 
    *   Do NOT allow Apache to resolve directories directly (Always disable `Options +Indexes` and `MultiViews`).
    *   Handle trailing slashes carefully in the router to avoid 404/403 errors.
*   **URL Safety**: Use `BASE_URL` for all links and assets. Never leak absolute server paths.
*   **Environment**: Compatibility with Windows/Laragon subdirectories must be maintained in URI detection.

---

## 🔐 Security & Data Integrity
*   **Database**: Use PDO Prepared Statements ONLY. No string concatenation.
*   **Validation**: Sanitize all inputs. Use `htmlspecialchars()` on all outputs.
*   **CSRF**: Validate `csrf_token` on every POST/AJAX request.
*   **Sessions**: Use `session_regenerate_id(true)` on login. Implement session fingerprinting (User-Agent).
*   **Uploads**: Validate MIME types using `finfo`. Rename uploaded files to random strings.

---

## 🔄 Workflow & Git Checkpoints
1.  **Git Restore**: Before starting a task that requires returning to a "clean state", use `git restore .`.
2.  **Checkpoint Messages**: Use `checkpoint: <short description>`.
3.  **WORKLOG.md**: Maintain a daily log. Each day should have its own header. Remove previous days' logs only when explicitly requested.
4.  **No Automated Pushes**: Never push to GitHub unless the user explicitly orders "Update git/github" or "Push to origin".

---

## 📦 Project Scope (V1 Only)
*   **Admin Auth**: Login/Logout/Dashboard.
*   **Management**: Project CRUD, Participant Whitelist (Import/Export), Question Bank.
*   **Exam Flow**: Entry (Search-based), Start (Timer/Auto-save), Result (Solid UI).
*   **Certificates**: Designer Studio (Pixel-to-MM), Verification (Public), Download (Client-side PDF).
*   **Reports**: Overall stats, CSV/Excel export (Client-side SheetJS).

---

## ⚠️ Critical Reminder
If the code looks simple, generic, or uses default browser styles, you have **FAILED**. Aim for a premium, custom, and alive interface.
