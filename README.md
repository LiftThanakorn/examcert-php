# 🎯 ExamCert

**ExamCert** is a standalone PHP/MySQL system for online exams and certificate issuance, designed for RERU (Roi Et Rajabhat University).

## 📋 Project Overview
ExamCert simplifies the process of creating exams, managing participants, and automatically issuing verifiable certificates.

## 🚀 Features
- **Admin Dashboard**: Comprehensive project and participant management.
- **Project Management**: Group exams and certificates by specific events or projects.
- **Participant Whitelist**: Search-based participant management.
- **Question Bank**: Flexible question creation and management.
- **Exam Engine**: Responsive and secure online exam interface.
- **Certificate Designer**: Web-based designer with real-time preview.
- **Public Verification**: Secure certificate verification system via QR code or unique ID.

## 🛠️ Tech Stack
- **Backend**: PHP 8.1+, PDO, MVC-ish pattern.
- **Frontend**: Tailwind CSS 3.4 (CDN), jQuery, SweetAlert2.
- **PDF/Excel**: `html2pdf.js` for certificates, `SheetJS` for Excel import/export.

## 📁 Project Structure
- `controllers/`, `models/`, `views/`: Core application logic (MVC).
- `api/`: AJAX endpoints for dynamic interactions.
- `assets/`: CSS, JS, and image assets.
- `docs/`: Database schema and documentation.
- `setup/`: Database initialization scripts.

## ⚙️ Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/LiftThanakorn/examcert-php.git
   ```
2. Import the database schema from `docs/database.sql` (or check `setup/` for scripts).
3. Configure database connection in `config/database.php`.
4. Ensure `uploads/` directory is writable.

## 🎨 Design System
- **Theme**: Orange-White Premium.
- **Style**: Clean, professional, solid colors (no gradients).
- **Responsive**: Fully optimized for mobile and desktop.

---
*Developed for RERU (มหาวิทยาลัยราชภัฏร้อยเอ็ด)*
