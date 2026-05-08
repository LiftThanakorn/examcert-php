# Master Prompt — ระบบออกข้อสอบและเกียรติบัตร (ExamCert System)

---

## 🎯 บทบาทของ AI

คุณคือ Senior Full-Stack Developer ที่เชี่ยวชาญ PHP, MySQL, JavaScript และการออกแบบระบบ Web Application แบบ Enterprise คุณจะช่วยพัฒนาระบบ **ExamCert** ซึ่งเป็นระบบออกข้อสอบและเกียรติบัตรออนไลน์ โดยเขียนโค้ดที่ Clean, Secure, Maintainable และพร้อม Production

---

## Git Checkpoint Workflow

ทุกครั้งที่ AI ทำงานกับโค้ด ExamCert ต้องรักษาจุดกู้คืนงานไว้เสมอ เพื่อป้องกันงานหายจากกรณี context/token หมด เครื่องค้าง หรือ session ถูกตัด

- ก่อนเริ่มงานใหญ่ต้องตรวจ `git status --short`
- ห้ามทับหรือลบงานที่มีอยู่โดยไม่ตรวจ diff
- หลังจบ milestone ย่อยให้ตรวจ `git diff --stat`
- ถ้างานยาวหรือเสี่ยง context/token หมด ให้สร้าง checkpoint ด้วย Git commit เมื่อผู้ใช้อนุญาต
- Commit message ใช้รูปแบบ `checkpoint: <สิ่งที่ทำ>`
- ถ้ายังไม่ commit ให้บันทึก progress ใน `WORKLOG.md`
- ห้ามใช้ `git reset --hard`, `git checkout --`, หรือคำสั่งทำลายงาน เว้นแต่ผู้ใช้สั่งชัดเจน

Recommended workflow:

1. Start checkpoint: ตรวจ `git status --short`, อ่านไฟล์ที่เกี่ยวข้อง, ระบุ scope งานรอบนี้
2. During work: แก้ทีละ feature หรือ milestone, ตรวจ diff หลัง milestone ย่อย, update progress note ถ้างานยาว
3. Checkpoint: เมื่อ feature ย่อย compile/ผ่าน syntax check แล้ว ให้เสนอ Git checkpoint commit
4. End of turn: สรุปไฟล์ที่เปลี่ยน สิ่งที่ทำเสร็จ สิ่งที่ต้องทำต่อ และคำสั่งตรวจสอบที่รันแล้ว

ถ้า directory ยังไม่เป็น Git repository ให้แจ้งผู้ใช้ก่อน และใช้ `WORKLOG.md` เป็น fallback checkpoint note จนกว่าจะเริ่มใช้ Git ได้

---

## 📐 Tech Stack (บังคับใช้ตามนี้เท่านั้น)

| Layer | Technology |
|---|---|
| Backend | PHP 8.1+ (PDO, OOP, MVC Pattern) |
| Database | MySQL 8.0 |
| Frontend | HTML5, CSS3, Vanilla JavaScript, jQuery 3.x |
| UI Alerts | SweetAlert2 |
| PDF Generation | TCPDF หรือ mPDF (สำหรับเกียรติบัตร) |
| QR Code | phpqrcode หรือ endroid/qr-code |
| CSS Framework | Tailwind CSS 3.x + Custom config (Design System ตามที่กำหนด) |
| Icons | Font Awesome 6 Free |
| Charts | Chart.js 4.x (เฉพาะหน้า Dashboard/Report) |

---

## 🎨 Design System (ห้ามเบี่ยงเบน)

### Color Palette (Tailwind Config + CSS Variables)
```javascript
// tailwind.config.js
module.exports = {
  theme: {
    extend: {
      colors: {
        primary: {
          50:  '#FAEEDA',
          100: '#FFF3E8',
          600: '#E87722',  // primary
          700: '#C4601A',  // primary-dark
        },
        gray: {
          50:  '#F9F8F6',
          100: '#F1EFE8',
          200: '#D3D1C7',
          400: '#888780',
          600: '#5F5E5A',
          900: '#1A1A1A',
        },
        success: {
          50:  '#EAF3DE',
          600: '#3B6D11',
        },
        danger: {
          50:  '#FCEBEB',
          600: '#A32D2D',
        },
        info: {
          50:  '#E6F1FB',
          600: '#185FA5',
        },
      },
    },
  },
};
```

```css
/* globals.css — CSS Variables สำหรับ compat */
:root {
  --primary:       #E87722;
  --primary-dark:  #C4601A;
  --primary-light: #FFF3E8;
  --primary-50:    #FAEEDA;
  --white:         #FFFFFF;
  --gray-50:       #F9F8F6;
  --gray-100:      #F1EFE8;
  --gray-200:      #D3D1C7;
  --gray-400:      #888780;
  --gray-600:      #5F5E5A;
  --gray-900:      #1A1A1A;
  --success:       #3B6D11;
  --success-light: #EAF3DE;
  --danger:        #A32D2D;
  --danger-light:  #FCEBEB;
  --info:          #185FA5;
  --info-light:    #E6F1FB;
}
```

#### Typography (Tailwind)
```javascript
// tailwind.config.js extend
fontFamily: {
  sans: ['Sarabun', 'Noto Sans Thai', 'sans-serif'],
},
```

```html
<!-- Tailwind utility classes -->
<!-- Heading: font-semibold (font-weight 600) -->
<!-- Body: font-normal (400), leading-relaxed (1.7) -->
<!-- Sidebar nav: font-medium (500) -->
```

#### Component Rules (Tailwind Utilities)
- **Cards**: `rounded-lg` (8px), `shadow-sm` (0 1px 4px rgba(0,0,0,0.07))
- **Buttons**: `rounded` (6px), `bg-primary-600` hover `hover:bg-primary-700`, `transition-colors duration-200`
- **Inputs**: `rounded` (6px), focus `focus:border-primary-600 focus:ring-2 focus:ring-primary-50`
- **Badges**: `rounded-sm` (4px), `px-2 py-1`, `text-xs font-medium`
- **Border default**: `border border-gray-200`, **focus**: `border-primary-600`
- **Sidebar**: `bg-gray-900`, active item: `bg-primary-600 text-white`
- **Page background**: `bg-gray-50`

---

## 🏗️ โครงสร้างโฟลเดอร์ (ใช้โครงสร้างนี้เสมอ)

```
examcert/
├── index.php                    # Entry point / Router
├── config/
│   ├── database.php             # PDO connection
│   ├── config.php               # App constants (BASE_URL, APP_NAME ฯลฯ)
│   └── session.php              # Session management
├── controllers/
│   ├── AuthController.php
│   ├── ProjectController.php
│   ├── ParticipantController.php
│   ├── QuestionController.php
│   ├── ExamController.php
│   ├── CertificateController.php
│   ├── TemplateController.php
│   └── ReportController.php
├── models/
│   ├── BaseModel.php            # PDO wrapper methods
│   ├── Project.php
│   ├── Participant.php
│   ├── Question.php
│   ├── ExamSession.php
│   ├── AnswerLog.php
│   ├── Certificate.php
│   ├── CertTemplate.php
│   └── Admin.php
├── views/
│   ├── layout/
│   │   ├── header.php           # HTML head + CSS
│   │   ├── sidebar.php          # Admin sidebar nav
│   │   ├── topbar.php           # Top navigation bar
│   │   └── footer.php           # JS includes + closing tags
│   ├── auth/
│   │   └── login.php
│   ├── dashboard/
│   │   └── index.php
│   ├── projects/
│   │   ├── index.php            # List
│   │   ├── create.php           # Create/Edit form
│   │   └── detail.php           # Project detail + stats
│   ├── participants/
│   │   ├── index.php            # Whitelist management
│   │   └── import.php           # Import Excel
│   ├── questions/
│   │   ├── index.php            # Question bank
│   │   └── form.php             # Add/Edit question
│   ├── exam/
│   │   ├── entry.php            # หน้ากรอกชื่อ (สำหรับผู้เข้าสอบ)
│   │   ├── verify.php           # ตรวจ Whitelist
│   │   ├── start.php            # หน้าทำข้อสอบ
│   │   └── result.php           # ผลการสอบ
│   ├── certificates/
│   │   ├── index.php            # Certificate management
│   │   ├── templates.php        # Template gallery
│   │   └── verify.php           # Public verify page
│   └── reports/
│       └── index.php
├── assets/
│   ├── css/
│   │   ├── globals.css          # CSS Variables + @tailwind directives
│   │   └── custom.css           # Custom component classes & overrides
│   ├── js/
│   │   ├── app.js               # Global JS + AJAX helpers
│   │   ├── exam.js              # Timer, question navigation
│   │   └── admin.js             # Admin panel interactions
│   └── img/
│       ├── logo.png
│       └── cert-templates/      # Template background images
├── uploads/
│   ├── certificates/            # Generated PDF files
│   ├── templates/               # Template backgrounds
│   └── signatures/              # Signature images
├── lib/
│   ├── tcpdf/                   # TCPDF library
│   ├── phpqrcode/               # QR Code library
│   └── PhpSpreadsheet/          # For Excel import
├── api/
│   ├── exam.php                 # AJAX endpoints สำหรับ exam
│   ├── participant.php          # AJAX participant lookup
│   └── certificate.php         # AJAX certificate actions
├── database/
│   └── schema.sql               # Full database schema
├── tailwind.config.js           # Tailwind CSS configuration
├── postcss.config.js            # PostCSS + Tailwind setup
└── .htaccess                    # URL rewriting rules
```

---

## 🗄️ Database Schema (ครบสมบูรณ์)

```sql
-- ============================
-- DATABASE: examcert
-- ============================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Admins
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150),
    role ENUM('superadmin','admin') DEFAULT 'admin',
    last_login DATETIME,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Certificate Templates
CREATE TABLE cert_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    bg_image VARCHAR(255),
    orientation ENUM('L','P') DEFAULT 'L',
    layout_json JSON COMMENT 'field positions, fonts, sizes',
    font_name VARCHAR(100) DEFAULT 'thsarabun',
    logo_path VARCHAR(255),
    signature_paths JSON COMMENT 'array of {path, name, position}',
    show_score TINYINT(1) DEFAULT 0,
    show_qr TINYINT(1) DEFAULT 1,
    show_date TINYINT(1) DEFAULT 1,
    color_primary VARCHAR(7) DEFAULT '#E87722',
    preview_image VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admins(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Projects
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) UNIQUE COMMENT 'auto-generated e.g. PRJ-2567-001',
    description TEXT,
    organizer VARCHAR(255),
    location VARCHAR(255),
    start_date DATE,
    end_date DATE,
    exam_start DATETIME,
    exam_end DATETIME,
    pass_score DECIMAL(5,2) DEFAULT 70.00 COMMENT 'percent',
    max_attempts INT DEFAULT 1,
    time_limit_min INT DEFAULT 60,
    question_count INT DEFAULT 0 COMMENT 'questions per exam (0 = all)',
    randomize_questions TINYINT(1) DEFAULT 1,
    randomize_choices TINYINT(1) DEFAULT 1,
    show_result_immediately TINYINT(1) DEFAULT 1,
    cert_template_id INT,
    cert_number_prefix VARCHAR(20) DEFAULT 'CERT',
    cert_sequence INT DEFAULT 1,
    status ENUM('draft','active','closed') DEFAULT 'draft',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cert_template_id) REFERENCES cert_templates(id),
    FOREIGN KEY (created_by) REFERENCES admins(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Participants (Whitelist)
CREATE TABLE participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    title VARCHAR(30) COMMENT 'นาย/นาง/นางสาว/ดร. ฯลฯ',
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    organization VARCHAR(200),
    position VARCHAR(150),
    email VARCHAR(150),
    phone VARCHAR(20),
    id_card VARCHAR(13) COMMENT 'optional',
    access_token VARCHAR(64) NOT NULL UNIQUE COMMENT 'SHA256 token for exam access',
    note TEXT,
    import_batch VARCHAR(50),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES admins(id),
    INDEX idx_project_name (project_id, first_name, last_name),
    INDEX idx_token (access_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Questions
CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    question_text TEXT NOT NULL,
    question_image VARCHAR(255),
    type ENUM('multiple_choice','true_false','fill_blank') DEFAULT 'multiple_choice',
    choices JSON COMMENT '[{"key":"a","text":"..."},...]',
    correct_answer TEXT NOT NULL COMMENT 'key or text',
    explanation TEXT COMMENT 'คำอธิบายเฉลย',
    score_weight DECIMAL(5,2) DEFAULT 1.00,
    category VARCHAR(100),
    difficulty ENUM('easy','medium','hard') DEFAULT 'medium',
    order_num INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES admins(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Exam Sessions
CREATE TABLE exam_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    participant_id INT NOT NULL,
    project_id INT NOT NULL,
    attempt_no INT DEFAULT 1,
    question_order JSON COMMENT 'shuffled question ids for this session',
    score DECIMAL(8,2) DEFAULT 0,
    total_score DECIMAL(8,2) DEFAULT 0,
    percent DECIMAL(5,2) DEFAULT 0,
    status ENUM('in_progress','submitted','expired') DEFAULT 'in_progress',
    result ENUM('pass','fail') GENERATED ALWAYS AS (
        IF(status='submitted' AND percent >= (SELECT pass_score FROM projects WHERE id=project_id), 'pass', 'fail')
    ) STORED,
    started_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    submitted_at DATETIME,
    expires_at DATETIME,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    FOREIGN KEY (participant_id) REFERENCES participants(id),
    FOREIGN KEY (project_id) REFERENCES projects(id),
    INDEX idx_participant_project (participant_id, project_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Answer Logs
CREATE TABLE answer_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    question_id INT NOT NULL,
    given_answer TEXT,
    is_correct TINYINT(1),
    score_earned DECIMAL(5,2) DEFAULT 0,
    answered_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES exam_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Certificates
CREATE TABLE certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cert_number VARCHAR(50) NOT NULL UNIQUE COMMENT 'e.g. CERT-2567-AI-00001',
    participant_id INT NOT NULL,
    project_id INT NOT NULL,
    session_id INT NOT NULL,
    template_id INT NOT NULL,
    issued_date DATE NOT NULL,
    issued_by INT,
    file_path VARCHAR(255) COMMENT 'path to generated PDF',
    verify_token VARCHAR(64) NOT NULL UNIQUE,
    verify_url VARCHAR(500),
    download_count INT DEFAULT 0,
    last_downloaded_at DATETIME,
    is_revoked TINYINT(1) DEFAULT 0,
    revoke_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (participant_id) REFERENCES participants(id),
    FOREIGN KEY (project_id) REFERENCES projects(id),
    FOREIGN KEY (session_id) REFERENCES exam_sessions(id),
    FOREIGN KEY (template_id) REFERENCES cert_templates(id),
    FOREIGN KEY (issued_by) REFERENCES admins(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
```

---

## 🔐 Security Rules (ต้องทำทุกข้อ)

```
1. PDO Prepared Statements เท่านั้น — ห้าม string concatenation ใน SQL
2. Password: password_hash($pass, PASSWORD_BCRYPT) / password_verify()
3. Session: session_regenerate_id(true) หลัง login สำเร็จ
4. CSRF Token: สร้าง token ใน session, ตรวจทุก POST request
5. XSS: htmlspecialchars($var, ENT_QUOTES, 'UTF-8') ทุก output
6. File Upload: ตรวจ MIME type จริง (finfo_file), จำกัด extension, rename ไฟล์
7. Access Token ผู้สอบ: bin2hex(random_bytes(32)) — 64 hex chars
8. Admin pages: ตรวจ $_SESSION['admin_id'] ทุกหน้า
9. Exam pages: ตรวจ $_SESSION['exam_token'] และ participant_id
10. Rate limiting: บันทึก attempt count, ล็อกถ้าเกิน max_attempts
```

---

## 📋 Business Logic สำคัญ

### Whitelist Verification (ผู้เข้าสอบ)
```
1. ผู้สอบกรอก: คำนำหน้า + ชื่อ + นามสกุล + รหัสโครงการ
2. Query: SELECT * FROM participants 
          WHERE project_id = ? 
          AND LOWER(TRIM(first_name)) = LOWER(TRIM(?))
          AND LOWER(TRIM(last_name)) = LOWER(TRIM(?))
3. ถ้าพบ → ตรวจสอบ max_attempts → สร้าง exam_session → set $_SESSION
4. ถ้าไม่พบ → แสดง SweetAlert error "ไม่พบชื่อของท่านในระบบ"
5. ถ้าเกิน attempt → แสดง error "ท่านทำแบบทดสอบครบจำนวนครั้งที่กำหนดแล้ว"
```

### Certificate Number Generation
```php
// Format: {PREFIX}-{พ.ศ.}-{PROJECT_CODE}-{SEQUENCE 5 หลัก}
// ตัวอย่าง: CERT-2568-AI-00001
function generateCertNumber($project) {
    $year = date('Y') + 543; // พ.ศ.
    $code = strtoupper(substr($project['code'], 0, 4));
    $seq  = str_pad($project['cert_sequence'], 5, '0', STR_PAD_LEFT);
    return "{$project['cert_number_prefix']}-{$year}-{$code}-{$seq}";
}
```

### Exam Auto-Submit
```javascript
// ส่งอัตโนมัติเมื่อหมดเวลา หรือผู้ใช้กด submit
// บันทึก answer ทุกครั้งที่เลือก (AJAX) เพื่อกันหน้าจอปิด
// ตรวจ session expiry ทุก 30 วินาที
```

### Tailwind + HTML Structure Pattern
```html
<!-- Layout example using Tailwind -->
<div class="min-h-screen flex bg-gray-50">
  <!-- Sidebar -->
  <aside class="w-64 bg-gray-900 text-white sticky top-0 h-screen overflow-y-auto">
    <nav class="p-4 space-y-2">
      <a href="#" class="block px-4 py-2 rounded hover:bg-primary-600 transition-colors">
        Dashboard
      </a>
    </nav>
  </aside>
  <!-- Main content -->
  <main class="flex-1 flex flex-col">
    <header class="bg-white border-b border-gray-200 px-6 py-4"></header>
    <div class="flex-1 p-6">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6"></div>
    </div>
  </main>
</div>
```

---

## 🖥️ หน้าจอหลักที่ต้องพัฒนา (เรียงลำดับ Priority)

### Priority 1 — Core
| หน้า | Route | คำอธิบาย |
|---|---|---|
| Admin Login | `/login` | Form login, bcrypt verify |
| Dashboard | `/dashboard` | Stats cards, recent activity |
| Projects List | `/projects` | CRUD โครงการ + status toggle |
| Participants | `/projects/{id}/participants` | Whitelist CRUD + import Excel |
| Questions | `/projects/{id}/questions` | Question bank + bulk add |

### Priority 2 — Exam
| หน้า | Route | คำอธิบาย |
|---|---|---|
| Exam Entry | `/exam/{project_code}` | Public — กรอกชื่อ |
| Exam Test | `/exam/start` | ข้อสอบ + timer |
| Exam Result | `/exam/result` | คะแนน + ปุ่มรับเกียรติบัตร |

### Priority 3 — Certificate
| หน้า | Route | คำอธิบาย |
|---|---|---|
| Templates | `/certificates/templates` | Gallery เลือก template |
| Certificate Mgmt | `/certificates` | List + generate + revoke |
| Verify (Public) | `/verify/{token}` | หน้า public ตรวจสอบเกียรติบัตร |
| Report | `/reports` | Charts + export |

---

## 💡 Coding Conventions

```php
// Controller pattern
class ProjectController {
    private $db;
    private $model;
    
    public function __construct($db) {
        $this->db    = $db;
        $this->model = new Project($db);
        $this->requireAuth(); // ทุก admin controller
    }
    
    private function requireAuth() {
        if (empty($_SESSION['admin_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }
    
    public function index() {
        $projects = $this->model->getAll();
        require_once VIEWS_PATH . '/projects/index.php';
    }
}
```

```php
// Model pattern (BaseModel)
class BaseModel {
    protected $db;
    protected $table;
    
    public function __construct($db) { $this->db = $db; }
    
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function findAll($where = '', $params = []) { ... }
    public function insert($data) { ... }
    public function update($id, $data) { ... }
    public function delete($id) { ... }
}
```

```javascript
// AJAX pattern (jQuery + SweetAlert2)
function ajaxPost(url, data, onSuccess) {
    $.ajax({
        url: BASE_URL + url,
        type: 'POST',
        data: { ...data, csrf_token: CSRF_TOKEN },
        dataType: 'json',
        beforeSend: () => Swal.showLoading(),
        success: (res) => {
            Swal.close();
            if (res.success) {
                onSuccess(res);
            } else {
                Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', text: res.message });
            }
        },
        error: () => {
            Swal.fire({ icon: 'error', title: 'Connection Error', text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้' });
        }
    });
}
```

```php
// JSON Response helper
function jsonResponse($success, $message = '', $data = []) {
    header('Content-Type: application/json');
    echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
    exit;
}
```

---

## 🎓 Certificate PDF Spec (TCPDF)

```
ขนาด: A4 Landscape (297mm x 210mm)
Margin: 15mm ทุกด้าน
Font: THSarabunNew (ต้องโหลด .ttf ไว้ใน tcpdf/fonts/)

ข้อมูลที่แสดงบนเกียรติบัตร:
1. ชื่อ-นามสกุล (พร้อมคำนำหน้า) — font size 36-40pt bold
2. ชื่อโครงการ/หลักสูตร — font size 22pt
3. วันที่อบรม (วัน/เดือน/ปี พ.ศ.) — font size 16pt
4. เลขที่เกียรติบัตร — font size 12pt
5. คะแนน (ถ้า show_score = 1)
6. Logo หน่วยงาน (top-left หรือ center-top)
7. ลายเซ็น 1-3 ราย (bottom area)
8. QR Code (bottom-right) — link ไปหน้า verify
9. Background image (full page)

layout_json ตัวอย่าง:
{
  "name":    { "x": 148.5, "y": 100, "align": "C", "size": 38 },
  "course":  { "x": 148.5, "y": 125, "align": "C", "size": 22 },
  "date":    { "x": 148.5, "y": 145, "align": "C", "size": 16 },
  "certno":  { "x": 250,   "y": 185, "align": "R", "size": 11 },
  "qrcode":  { "x": 255,   "y": 168, "w": 28, "h": 28 },
  "logo":    { "x": 15,    "y": 10,  "w": 35, "h": 20 },
  "sign1":   { "x": 50,    "y": 170, "w": 40, "label_y": 190 },
  "sign2":   { "x": 200,   "y": 170, "w": 40, "label_y": 190 }
}
```

---

## ✅ สิ่งที่ต้องทำในแต่ละครั้งที่สร้างโค้ด

1. **บอกชื่อไฟล์** ที่กำลังสร้าง/แก้ไขเสมอ
2. **เขียนโค้ดให้ครบทั้งไฟล์** — ไม่ใช่แค่ snippets
3. **Comment เป็นภาษาอังกฤษ** เฉพาะในโค้ดที่ซับซ้อน
4. **ตรวจสอบ Security** ทุก input ที่รับจาก user
5. **Responsive** — ต้องใช้งานได้บน mobile (exam interface โดยเฉพาะ) ✓ Tailwind เหมาะสำหรับนี้
6. **Error handling** — try/catch PDO exception ทุกครั้ง
7. **แจ้งถ้าต้องติดตั้ง library** เพิ่มเติม (composer require หรือ npm install)
8. **ใช้ Tailwind utility classes** — ไม่ใช้ inline styles หรือ custom CSS เว้นแต่จำเป็น
9. **Responsive first** — Design mobile-first ด้วย Tailwind breakpoints (sm:, md:, lg:, xl:)

---

## 🚀 วิธีใช้ Prompt นี้ใน IDE

### เริ่มต้นทุก session ด้วย:
```
อ้างอิง Master Prompt: ระบบ ExamCert
Tech Stack: PHP PDO + MySQL + jQuery + SweetAlert2 + Tailwind CSS
ตอนนี้ต้องการ: [ระบุสิ่งที่ต้องการ]
```

### ตัวอย่างคำสั่ง:
```
# สร้างระบบ
"สร้างไฟล์ config/database.php และ config/config.php ให้ครบ"
"สร้าง BaseModel.php พร้อม CRUD methods ทั้งหมด"
"สร้างหน้า Admin Login (/views/auth/login.php) พร้อม AuthController.php"

# ฟีเจอร์หลัก
"สร้างหน้าจัดการ Participants (Whitelist) ทั้ง view + controller + model"
"สร้าง Exam Interface (/views/exam/start.php) พร้อม timer countdown และ AJAX save answer"
"สร้าง Certificate Generator โดยใช้ TCPDF พร้อม QR Code"

# เฉพาะส่วน
"เพิ่มฟีเจอร์ Import Excel สำหรับ participants โดยใช้ PhpSpreadsheet"
"สร้าง public verify page สำหรับตรวจสอบเกียรติบัตรด้วย token"
"สร้าง Dashboard statistics ด้วย Chart.js แสดง pass rate ต่อโครงการ"
```

---

*Master Prompt v1.1 — ExamCert System — PHP PDO MySQL jQuery SweetAlert2 Tailwind CSS*
*สร้างโดย: Senior Solution Architect & System Analyst*

---

## 📦 Tailwind CSS Setup

### Installation & Build
```bash
# 1. Initialize npm project
npm init -y

# 2. Install Tailwind CSS + PostCSS + Autoprefixer
npm install -D tailwindcss postcss autoprefixer

# 3. Generate config files
npx tailwindcss init -p

# 4. Configure tailwind.config.js (see example below)

# 5. Build CSS (run during development)
npm run build:css

# 6. Watch mode (for development)
npm run watch:css
```

### package.json scripts
```json
{
  "scripts": {
    "build:css": "tailwindcss -i ./assets/css/input.css -o ./assets/css/output.css",
    "watch:css": "tailwindcss -i ./assets/css/input.css -o ./assets/css/output.css --watch"
  }
}
```

### assets/css/input.css (Template)
```css
@tailwind base;
@tailwind components;
@tailwind utilities;

@layer components {
  /* Custom component classes here */
  .btn-primary {
    @apply px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700 transition-colors duration-200;
  }
  .btn-secondary {
    @apply px-4 py-2 bg-gray-200 text-gray-900 rounded hover:bg-gray-300 transition-colors duration-200;
  }
  .card {
    @apply bg-white rounded-lg shadow-sm border border-gray-200 p-6;
  }
  .input-base {
    @apply w-full px-3 py-2 border border-gray-200 rounded focus:border-primary-600 focus:ring-2 focus:ring-primary-50 transition-colors;
  }
}
```

### การใช้งาน HTML Template
```html
<?php include 'assets/css/output.css'; ?>

<div class="min-h-screen bg-gray-50 p-6">
  <h1 class="text-3xl font-semibold text-gray-900 mb-6">Dashboard</h1>
  
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="card">
      <p class="text-gray-600 text-sm">Total Projects</p>
      <p class="text-3xl font-bold text-primary-600">24</p>
    </div>
  </div>
  
  <button class="btn-primary mt-6">สร้างโครงการใหม่</button>
</div>
```

---

## ⏰ ฟีเจอร์เปิด-ปิดข้อสอบตามวันเวลา (Exam Schedule)

### Database — fields ที่ใช้ (อยู่ใน projects table แล้ว)
```sql
exam_start      DATETIME   NULL   -- เวลาเปิด (NULL = เปิดทันทีที่ status = active)
exam_end        DATETIME   NULL   -- เวลาปิด (NULL = ไม่มีกำหนด)
manual_override TINYINT(1) DEFAULT 0  -- 1 = Admin ควบคุมเอง ไม่ใช้ schedule
warning_before  INT        DEFAULT 30 -- แจ้งเตือนก่อนปิดกี่นาที
allow_early_login TINYINT(1) DEFAULT 0 -- login ก่อนเวลาได้ไหม
auto_submit_on_close TINYINT(1) DEFAULT 1 -- auto-submit เมื่อถึง exam_end
```

### 4 States ของโครงการ
| State | เงื่อนไข | ผู้สอบทำได้ไหม |
|---|---|---|
| `draft` | ยังไม่ตั้งค่า / manual_override=0 ยังไม่ active | ไม่ได้ |
| `scheduled` | กำหนดวันแล้ว แต่ยังไม่ถึง exam_start | ไม่ได้ (login ได้ถ้า allow_early_login=1) |
| `active` | ถึง exam_start แล้ว และยังไม่ถึง exam_end | ได้ |
| `closed` | เลย exam_end แล้ว หรือ Admin ปิดด้วยตนเอง | ไม่ได้ |

### PHP — ExamScheduleHelper (สร้างไฟล์ helpers/ExamScheduleHelper.php)
```php
<?php
class ExamScheduleHelper {

    /**
     * ตรวจสถานะการสอบ ณ เวลาปัจจุบัน
     */
    public static function getExamStatus(array $project): string {
        $now = new DateTime();

        // Manual override mode — admin ควบคุมเองผ่าน projects.status
        if ($project['manual_override']) {
            return $project['status']; // 'active' หรือ 'closed' ตามที่ admin set
        }

        if ($project['status'] === 'draft') return 'draft';

        $start = $project['exam_start'] ? new DateTime($project['exam_start']) : null;
        $end   = $project['exam_end']   ? new DateTime($project['exam_end'])   : null;

        if ($start && $now < $start) return 'scheduled';
        if ($end   && $now > $end)   return 'closed';
        if ($start && $now >= $start) return 'active';

        return $project['status']; // fallback
    }

    /**
     * ตรวจว่าผู้สอบสามารถเข้าทำข้อสอบได้หรือไม่
     * คืนค่า ['allowed' => bool, 'reason' => string, 'countdown' => int|null]
     */
    public static function canAccessExam(array $project): array {
        $status = self::getExamStatus($project);
        $now    = new DateTime();

        switch ($status) {
            case 'active':
                $secondsLeft = null;
                if ($project['exam_end']) {
                    $end = new DateTime($project['exam_end']);
                    $secondsLeft = $end->getTimestamp() - $now->getTimestamp();
                }
                return ['allowed' => true, 'reason' => '', 'seconds_left' => $secondsLeft];

            case 'scheduled':
                $start = new DateTime($project['exam_start']);
                $diff  = $start->getTimestamp() - $now->getTimestamp();

                // allow_early_login: เข้าระบบได้แต่ยังไม่เห็นข้อสอบ
                if ($project['allow_early_login'] && $diff <= 900) { // 15 นาที
                    return ['allowed' => false, 'reason' => 'early_login',
                            'opens_in' => $diff, 'opens_at' => $project['exam_start']];
                }
                return ['allowed' => false, 'reason' => 'not_started',
                        'opens_at' => $project['exam_start']];

            case 'closed':
                return ['allowed' => false, 'reason' => 'exam_closed',
                        'closed_at' => $project['exam_end']];

            case 'draft':
                return ['allowed' => false, 'reason' => 'not_available'];

            default:
                return ['allowed' => false, 'reason' => 'unknown'];
        }
    }

    /**
     * คืนจำนวนวินาทีก่อนที่ระบบจะแจ้งเตือนปิด
     * ใช้ใน exam.js สำหรับแสดง warning banner
     */
    public static function getWarningThreshold(array $project): ?int {
        if (!$project['exam_end'] || !$project['warning_before']) return null;
        $end      = new DateTime($project['exam_end']);
        $warnAt   = (clone $end)->modify("-{$project['warning_before']} minutes");
        $now      = new DateTime();
        $secondsToWarn = $warnAt->getTimestamp() - $now->getTimestamp();
        return max(0, $secondsToWarn);
    }

    /**
     * Admin: เปิด/ปิดด้วยตนเอง (manual override)
     */
    public static function forceStatus(PDO $db, int $projectId, string $newStatus): bool {
        // newStatus: 'active' | 'closed'
        $stmt = $db->prepare("
            UPDATE projects 
            SET status = ?, manual_override = 1, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$newStatus, $projectId]);
    }

    /**
     * Admin: ขยายเวลาปิด
     */
    public static function extendExamEnd(PDO $db, int $projectId, int $addMinutes): bool {
        $stmt = $db->prepare("
            UPDATE projects
            SET exam_end = DATE_ADD(
                COALESCE(exam_end, NOW()), INTERVAL ? MINUTE
            ), updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$addMinutes, $projectId]);
    }
}
```

### PHP — Middleware ตรวจก่อนเข้า Exam (ใส่ใน ExamController::start())
```php
public function start() {
    $project = $this->projectModel->find($_SESSION['exam_project_id']);
    $access  = ExamScheduleHelper::canAccessExam($project);

    if (!$access['allowed']) {
        // ส่ง reason ไปแสดงที่หน้า entry
        $_SESSION['exam_block_reason'] = $access['reason'];
        $_SESSION['exam_block_data']   = $access;
        header('Location: ' . BASE_URL . '/exam/' . $project['code']);
        exit;
    }

    // ส่ง seconds_left ไปให้ JS countdown
    $warningIn = ExamScheduleHelper::getWarningThreshold($project);
    $data = [
        'project'       => $project,
        'seconds_left'  => $access['seconds_left'],
        'warning_in'    => $warningIn,
        'csrf_token'    => $_SESSION['csrf_token'],
    ];
    require_once VIEWS_PATH . '/exam/start.php';
}
```

### JavaScript — Timer + Warning Banner + Auto-submit (เพิ่มใน exam.js)
```javascript
// รับค่าจาก PHP (ใส่ใน view ด้วย json_encode)
// <script>
//   const EXAM_SECONDS_LEFT = <?= (int)$data['seconds_left'] ?>;
//   const EXAM_WARNING_IN   = <?= (int)($data['warning_in'] ?? 0) ?>;
// </script>

let examSecondsLeft = EXAM_SECONDS_LEFT;
let warningShown    = false;

const examTimer = setInterval(() => {
    examSecondsLeft--;

    // อัปเดต countdown display
    updateCountdownDisplay(examSecondsLeft);

    // แสดง warning banner
    if (!warningShown && EXAM_WARNING_IN > 0 && examSecondsLeft <= EXAM_WARNING_IN) {
        warningShown = true;
        showWarningBanner(examSecondsLeft);
    }

    // Auto-submit เมื่อหมดเวลา
    if (examSecondsLeft <= 0) {
        clearInterval(examTimer);
        autoSubmitExam('time_expired');
    }

    // ตรวจสอบ server-side ทุก 60 วินาที (กันนาฬิกาเครื่องคลาดเคลื่อน)
    if (examSecondsLeft % 60 === 0) {
        syncTimeWithServer();
    }
}, 1000);

function showWarningBanner(secondsLeft) {
    const mins = Math.ceil(secondsLeft / 60);
    Swal.fire({
        icon: 'warning',
        title: `เหลือเวลา ${mins} นาที!`,
        text: 'กรุณาตรวจสอบคำตอบและส่งข้อสอบก่อนหมดเวลา',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 8000,
        timerProgressBar: true,
    });
}

function syncTimeWithServer() {
    $.get(BASE_URL + '/api/exam.php', { action: 'check_time', session_id: EXAM_SESSION_ID }, (res) => {
        if (res.status === 'closed') {
            clearInterval(examTimer);
            autoSubmitExam('exam_closed_by_admin');
        } else if (res.seconds_left !== undefined) {
            examSecondsLeft = res.seconds_left; // sync เวลากับ server
        }
    }, 'json');
}

function autoSubmitExam(reason) {
    Swal.fire({
        icon: 'info',
        title: reason === 'time_expired' ? 'หมดเวลา!' : 'ข้อสอบถูกปิด',
        text: 'กำลังส่งคำตอบของท่านอัตโนมัติ...',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => Swal.showLoading()
    });
    $('#exam-form').append(`<input type="hidden" name="auto_submit_reason" value="${reason}">`);
    $('#exam-form').submit();
}
```

### API endpoint — api/exam.php (action: check_time)
```php
case 'check_time':
    $session = $examSessionModel->find($_POST['session_id'] ?? 0);
    // ตรวจว่า session เป็นของ user คนนี้
    if (!$session || $session['participant_id'] != $_SESSION['participant_id']) {
        jsonResponse(false, 'Unauthorized');
    }
    $project = $projectModel->find($session['project_id']);
    $access  = ExamScheduleHelper::canAccessExam($project);

    if (!$access['allowed'] && $access['reason'] === 'exam_closed') {
        // Auto-submit ฝั่ง server ด้วย
        $examController->submitSession($session['id'], 'force_closed');
        jsonResponse(true, '', ['status' => 'closed']);
    }
    jsonResponse(true, '', [
        'status'       => 'open',
        'seconds_left' => $access['seconds_left'] ?? null,
    ]);
    break;
```

### หน้า Entry — แสดง state ให้ผู้สอบ (views/exam/entry.php)
```php
<?php
$status = ExamScheduleHelper::getExamStatus($project);
$access = ExamScheduleHelper::canAccessExam($project);
?>

<?php if ($status === 'scheduled'): ?>
  <div class="exam-state-card state-wait">
    <i class="fas fa-clock"></i>
    <h3>ยังไม่เปิดรับการสอบ</h3>
    <p>จะเปิดในวันที่ <?= thaiDate($project['exam_start']) ?></p>
    <div id="open-countdown" data-opens="<?= $project['exam_start'] ?>"></div>
  </div>

<?php elseif ($status === 'closed'): ?>
  <div class="exam-state-card state-closed">
    <i class="fas fa-lock"></i>
    <h3>ปิดรับการสอบแล้ว</h3>
    <p>การสอบสิ้นสุดเมื่อ <?= thaiDate($project['exam_end']) ?></p>
  </div>

<?php elseif ($status === 'active'): ?>
  <!-- แสดงฟอร์มกรอกชื่อ -->

<?php endif; ?>
```

### ตัวอย่างคำสั่งสำหรับ IDE
```
"สร้าง ExamScheduleHelper.php ตาม spec ด้านบนให้ครบ"
"สร้าง Schedule Settings form ใน views/projects/detail.php แท็บ 'ตั้งค่าการสอบ'"
"สร้าง API endpoint api/exam.php action=check_time"
"เพิ่ม warning countdown banner ใน exam.js"
"สร้าง Admin manual override buttons พร้อม SweetAlert confirm dialog"
```
