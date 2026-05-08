# ExamCert Local Setup

## 1. Import database

Import `database/schema.sql` into MySQL/MariaDB using phpMyAdmin or the MySQL CLI.

Database name: `examcert`

Or run the CLI installer:

```powershell
C:\laragon\bin\php\php-8.3.16-Win32-vs16-x64\php.exe setup/install.php
```

## 2. Create first admin

```powershell
C:\laragon\bin\php\php-8.3.16-Win32-vs16-x64\php.exe setup/create-admin.php admin YourPassword123
```

Login URL:

```text
http://localhost/examcert/admin/login.php
```

## 3. Optional sample data

```powershell
C:\laragon\bin\php\php-8.3.16-Win32-vs16-x64\php.exe setup/seed-sample.php
```

Sample exam URL:

```text
http://localhost/examcert/public/exam.php?project=TEST-001
```

Use the token printed by the seed script.

## 4. Manual test checklist

- Login admin
- Open `admin/projects/`
- Create or inspect project
- Add participants
- Add questions
- Open public exam URL
- Submit exam
- Open `admin/exam-sessions/`
- Issue certificate for a passed session
- Open public verify URL
