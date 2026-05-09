---
name: examcert-php
description: Use when developing the ExamCert PHP/MySQL standalone exam and certificate system.
---

# 🛡️ ExamCert PHP - Mandatory Enforcement

Use this skill for ALL work inside the ExamCert project.

## 🚀 Mandatory Pre-Flight Check (AI MUST DO THIS)
Before performing ANY code modification, the AI assistant MUST:
1.  **Read the Source of Truth**: Read `PROJECT.md` to understand current status.
2.  **Read Modular Rules**: Read relevant files in `.agents/rules/` (`ui.md`, `routing.md`, `security.md`).
3.  **Chain of Thought (Plan)**: Summarize the relevant rules for the current task before proposing code.
4.  **Confirm Git Status**: Ensure the working tree is ready for changes.

---

## 🏗️ Core Architecture Reference
- **Frontend Controller**: `index.php`
- **Rules Directory**: `.agents/rules/`
- **Design System**: Solid Orange Premium (See `.agents/rules/ui.md`)

---

## 🔄 Workflow & Git
- **Checkpoint**: Commit with `checkpoint: <description>`.
- **Worklog**: Log daily progress.
- **Rules Enforcement**: If a user request contradicts the rules in `.agents/rules/`, point it out before proceeding.

---
## ⚠️ Enforcement Policy
Failure to read `PROJECT.md` and the modular rules before acting is considered a **CRITICAL FAILURE**. Always maintain "Context as Code" by updating these rules as the project evolves.
