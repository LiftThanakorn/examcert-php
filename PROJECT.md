# 🎯 ExamCert - Source of Truth (Project Status)

## 📋 Project Overview
ExamCert is a standalone PHP/MySQL system for online exams and certificate issuance, designed for RERU (มหาวิทยาลัยราชภัฏร้อยเอ็ด).

## 🚀 Current Status (2026-05-09)
- **Architecture**: Front Controller (`index.php`) is stable and handles all routing.
- **UI System**: "Orange-White Premium" with NO GRADIENTS.
- **Core Modules**: Admin Dashboard, Project Management, Participant Whitelist (Search-based), Question Bank, Exam Engine, Certificate Designer, and Public Verification.
- **Recent Fixes**: Resolved 403 Forbidden errors and absolute path leaks.

## 🛠️ Tech Stack
- **Backend**: PHP 8.1+, PDO, MVC-ish pattern.
- **Frontend**: Tailwind CSS 3.4 (CDN), jQuery, SweetAlert2.
- **PDF/Excel**: `html2pdf.js` for certificates, `SheetJS` for Excel import/export (Client-side).

## 🚩 Pending Tasks / Known Issues
1.  **E2E Testing**: Full verification of the "Project -> Participant -> Exam -> Cert" flow.
2.  **PDF Fidelity**: Ensure client-side PDF matches the screen preview perfectly.
3.  **Localization**: Final sweep for any remaining mojibake in Thai UI.

## 📁 Key Directories
- `controllers/`, `models/`, `views/`: Core application logic.
- `api/`: AJAX endpoints.
- `assets/`: CSS, JS, and Images.
- `.agents/`: AI skills and modular rules.

---
*Last Updated: 2026-05-09 23:10*
