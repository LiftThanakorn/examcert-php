# Frontend Guide — ExamCert System
## Orange-White Premium Design with Tailwind CSS CDN

---

## 1. CDN Setup (วางใน layout/header.php ทุกหน้า)

```html
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'ExamCert') ?> — ExamCert</title>

  <!-- Tailwind CSS CDN (v3 Play CDN) -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Tailwind Config — ต้องวางก่อน CSS ทั้งหมด -->
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: {
              50:  '#FFF3E8',
              100: '#FAEEDA',
              200: '#FAC775',
              300: '#EF9F27',
              400: '#E87722',   // หลัก
              500: '#C4601A',   // hover
              600: '#9E4A12',
              700: '#7A360C',
              800: '#633806',
              900: '#412402',
            },
            sidebar: '#1A1A1A',
          },
          fontFamily: {
            sans: ['Sarabun', 'Noto Sans Thai', 'sans-serif'],
          },
          fontSize: {
            'xxs': '0.65rem',
          },
          boxShadow: {
            'card': '0 1px 4px rgba(0,0,0,0.07)',
            'card-hover': '0 4px 16px rgba(0,0,0,0.10)',
            'orange': '0 0 0 3px rgba(232,119,34,0.18)',
          },
          borderRadius: {
            'xl2': '1rem',
          },
        },
      },
    }
  </script>

  <!-- Google Fonts — Sarabun (รองรับภาษาไทยสวยงาม) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Font Awesome 6 Free -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

  <!-- Custom CSS — เฉพาะสิ่งที่ Tailwind ทำไม่ได้ -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css">
</head>
```

---

## 2. Custom CSS (assets/css/app.css)
> เฉพาะส่วนที่ Tailwind ทำไม่ได้หรือใช้บ่อยมาก

```css
/* ========================
   GLOBAL RESETS & BASE
======================== */
*, *::before, *::after { box-sizing: border-box; }

html { scroll-behavior: smooth; }

body {
  font-family: 'Sarabun', 'Noto Sans Thai', sans-serif;
  background-color: #F9F8F6;
  color: #1A1A1A;
  line-height: 1.7;
  -webkit-font-smoothing: antialiased;
}

/* ========================
   SIDEBAR SCROLLBAR
======================== */
.sidebar-nav::-webkit-scrollbar { width: 4px; }
.sidebar-nav::-webkit-scrollbar-track { background: transparent; }
.sidebar-nav::-webkit-scrollbar-thumb { background: #333; border-radius: 2px; }

/* ========================
   FORM INPUTS — FOCUS RING
======================== */
input:focus, select:focus, textarea:focus {
  outline: none;
  border-color: #E87722 !important;
  box-shadow: 0 0 0 3px rgba(232,119,34,0.18);
}

/* ========================
   EXAM TIMER — PULSE
======================== */
@keyframes timerPulse {
  0%, 100% { opacity: 1; }
  50%       { opacity: 0.6; }
}
.timer-danger { animation: timerPulse 1s ease-in-out infinite; }

/* ========================
   CERTIFICATE PREVIEW
======================== */
.cert-preview-wrapper {
  aspect-ratio: 1.414 / 1; /* A4 landscape ratio */
  position: relative;
  overflow: hidden;
}

/* ========================
   TABLE ROW HOVER
======================== */
.table-row-hover tr:hover td {
  background-color: #FFF3E8;
  transition: background-color 0.15s;
}

/* ========================
   QUESTION OPTION — EXAM
======================== */
.exam-option input[type="radio"]:checked + label {
  border-color: #E87722;
  background-color: #FFF3E8;
  color: #9E4A12;
}
.exam-option input[type="radio"]:checked + label .option-marker {
  background-color: #E87722;
  color: #fff;
  border-color: #E87722;
}

/* ========================
   PROGRESS BAR ANIMATION
======================== */
.progress-bar-fill {
  transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

/* ========================
   SWEETALERT2 OVERRIDE
======================== */
.swal2-confirm {
  background-color: #E87722 !important;
}
.swal2-confirm:hover {
  background-color: #C4601A !important;
}
.swal2-popup {
  font-family: 'Sarabun', sans-serif !important;
  border-radius: 12px !important;
}
```

---

## 3. Layout Structure (Admin Panel)

### layout/sidebar.php
```html
<!-- Sidebar — fixed left, dark bg -->
<aside class="fixed top-0 left-0 h-screen w-56 bg-sidebar flex flex-col z-40 overflow-hidden">

  <!-- Logo -->
  <div class="px-4 py-5 border-b border-white/10 flex-shrink-0">
    <div class="flex items-center gap-3">
      <div class="w-9 h-9 bg-primary-400 rounded-xl flex items-center justify-center text-white text-lg">
        <i class="fas fa-award"></i>
      </div>
      <div>
        <p class="text-white font-semibold text-sm leading-tight">ExamCert</p>
        <p class="text-white/40 text-xxs">Admin Panel</p>
      </div>
    </div>
  </div>

  <!-- Navigation -->
  <nav class="flex-1 overflow-y-auto sidebar-nav py-3 px-2">

    <p class="text-white/30 text-xxs uppercase tracking-widest px-2 py-2 mt-1">หลัก</p>

    <?php
    $navItems = [
      ['icon'=>'fa-gauge',         'label'=>'Dashboard',     'route'=>'dashboard'],
      ['icon'=>'fa-folder-open',   'label'=>'โครงการ',       'route'=>'projects'],
      ['icon'=>'fa-users',         'label'=>'ผู้เข้าอบรม',   'route'=>'participants'],
    ];
    $examItems = [
      ['icon'=>'fa-file-lines',    'label'=>'คลังข้อสอบ',    'route'=>'questions'],
      ['icon'=>'fa-chart-bar',     'label'=>'ผลการสอบ',      'route'=>'results'],
    ];
    $certItems = [
      ['icon'=>'fa-palette',       'label'=>'เทมเพลต',       'route'=>'templates'],
      ['icon'=>'fa-certificate',   'label'=>'เกียรติบัตร',   'route'=>'certificates'],
    ];

    function navItem($item, $current) {
      $active = ($current === $item['route']);
      $base   = 'flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm mb-0.5 transition-colors duration-150 group';
      $cls    = $active
        ? "$base bg-primary-400 text-white font-medium"
        : "$base text-white/50 hover:text-white hover:bg-white/8";
      echo "<a href='".BASE_URL."/{$item['route']}' class='$cls'>";
      echo "  <i class='fas {$item['icon']} w-4 text-center text-base'></i>";
      echo "  <span>{$item['label']}</span>";
      echo "</a>";
    }

    $current = $currentRoute ?? '';
    foreach ($navItems as $item) navItem($item, $current);

    echo "<p class='text-white/30 text-xxs uppercase tracking-widest px-2 py-2 mt-3'>ข้อสอบ</p>";
    foreach ($examItems as $item) navItem($item, $current);

    echo "<p class='text-white/30 text-xxs uppercase tracking-widest px-2 py-2 mt-3'>เกียรติบัตร</p>";
    foreach ($certItems as $item) navItem($item, $current);
    ?>

    <p class="text-white/30 text-xxs uppercase tracking-widest px-2 py-2 mt-3">ระบบ</p>
    <a href="<?= BASE_URL ?>/settings" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-white/50 hover:text-white hover:bg-white/8 transition-colors">
      <i class="fas fa-gear w-4 text-center"></i>
      <span>ตั้งค่าระบบ</span>
    </a>

  </nav>

  <!-- Admin Profile -->
  <div class="px-3 py-3 border-t border-white/10 flex-shrink-0">
    <div class="flex items-center gap-2.5">
      <div class="w-8 h-8 rounded-full bg-primary-400 flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
        <?= strtoupper(mb_substr($_SESSION['admin_name'] ?? 'AD', 0, 2)) ?>
      </div>
      <div class="min-w-0 flex-1">
        <p class="text-white text-xs font-medium truncate"><?= htmlspecialchars($_SESSION['admin_name'] ?? '') ?></p>
        <p class="text-white/40 text-xxs"><?= $_SESSION['admin_role'] ?? 'admin' ?></p>
      </div>
      <a href="<?= BASE_URL ?>/logout" class="text-white/30 hover:text-red-400 transition-colors text-sm">
        <i class="fas fa-arrow-right-from-bracket"></i>
      </a>
    </div>
  </div>

</aside>
```

### layout/topbar.php
```html
<!-- Topbar — fixed top, offset left by sidebar width -->
<header class="fixed top-0 left-56 right-0 h-14 bg-white border-b border-gray-100 flex items-center justify-between px-6 z-30">
  <!-- Page title -->
  <div>
    <h1 class="text-sm font-semibold text-gray-800"><?= $pageTitle ?? '' ?></h1>
    <?php if (!empty($breadcrumb)): ?>
    <p class="text-xxs text-gray-400 mt-0.5">
      <?= implode(' / ', array_map('htmlspecialchars', $breadcrumb)) ?>
    </p>
    <?php endif; ?>
  </div>
  <!-- Actions slot -->
  <div class="flex items-center gap-2">
    <?php if (!empty($topbarActions)) echo $topbarActions; ?>
  </div>
</header>
```

### layout/main-wrapper.php
```html
<!-- ใช้ครอบ content ทุกหน้า Admin -->
<div class="ml-56 pt-14 min-h-screen bg-[#F9F8F6]">
  <main class="p-6">
    <?= $content ?>
  </main>
</div>
```

---

## 4. Component Patterns (Tailwind Classes)

### 4.1 Stat Card
```html
<div class="bg-white rounded-xl border border-gray-100 shadow-card p-4 flex items-start gap-4">
  <!-- Icon -->
  <div class="w-10 h-10 rounded-xl bg-primary-50 flex items-center justify-center flex-shrink-0">
    <i class="fas fa-users text-primary-400 text-base"></i>
  </div>
  <!-- Data -->
  <div>
    <p class="text-2xl font-semibold text-gray-900 leading-none">348</p>
    <p class="text-xs text-gray-400 mt-1">ผู้เข้าอบรมทั้งหมด</p>
    <!-- Optional trend badge -->
    <span class="inline-flex items-center gap-1 text-xxs font-medium text-green-700 bg-green-50 px-2 py-0.5 rounded-full mt-1.5">
      <i class="fas fa-arrow-trend-up text-xxs"></i> +12 เดือนนี้
    </span>
  </div>
</div>
```

### 4.2 Card (Generic)
```html
<div class="bg-white rounded-xl border border-gray-100 shadow-card overflow-hidden">
  <!-- Card Header -->
  <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-50">
    <div class="flex items-center gap-2">
      <i class="fas fa-folder-open text-primary-400"></i>
      <h2 class="text-sm font-semibold text-gray-800">ชื่อ Card</h2>
    </div>
    <!-- Right action -->
    <button class="text-xs text-gray-400 hover:text-primary-400 transition-colors">ดูทั้งหมด</button>
  </div>
  <!-- Card Body -->
  <div class="p-5">
    <!-- content here -->
  </div>
</div>
```

### 4.3 Button Variants
```html
<!-- Primary -->
<button class="inline-flex items-center gap-2 px-4 py-2 bg-primary-400 hover:bg-primary-500 text-white text-sm font-medium rounded-lg transition-colors duration-150">
  <i class="fas fa-plus text-xs"></i> เพิ่มโครงการ
</button>

<!-- Secondary (Outline) -->
<button class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 text-gray-600 text-sm font-medium rounded-lg border border-gray-200 transition-colors duration-150">
  <i class="fas fa-download text-xs"></i> Export
</button>

<!-- Danger -->
<button class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 text-sm font-medium rounded-lg border border-red-200 transition-colors duration-150">
  <i class="fas fa-trash text-xs"></i> ลบ
</button>

<!-- Icon Only -->
<button class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-500 hover:text-primary-400 transition-colors">
  <i class="fas fa-pen text-xs"></i>
</button>
```

### 4.4 Form Input
```html
<div class="space-y-1">
  <label class="block text-xs font-medium text-gray-600">
    ชื่อโครงการ <span class="text-primary-400">*</span>
  </label>
  <input
    type="text"
    placeholder="เช่น อบรม AI เบื้องต้น รุ่น 1"
    class="w-full h-9 px-3 text-sm bg-white border border-gray-200 rounded-lg placeholder-gray-300
           text-gray-800 transition-colors duration-150
           focus:outline-none focus:border-primary-400 focus:ring-[3px] focus:ring-primary-400/20"
  >
  <p class="text-xxs text-gray-400">ชื่อจะปรากฎบนเกียรติบัตร</p>
</div>
```

### 4.5 Select
```html
<select class="w-full h-9 px-3 text-sm bg-white border border-gray-200 rounded-lg text-gray-800
               focus:outline-none focus:border-primary-400 focus:ring-[3px] focus:ring-primary-400/20
               appearance-none bg-[url('data:image/svg+xml,...')] bg-no-repeat bg-right-3">
  <option value="">-- เลือกเทมเพลต --</option>
  <option value="1">Classic Orange</option>
</select>
```

### 4.6 Badge / Status
```html
<!-- Active -->
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xxs font-medium bg-green-50 text-green-700">
  <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> เปิดใช้งาน
</span>

<!-- Draft -->
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xxs font-medium bg-gray-100 text-gray-500">
  <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> ร่าง
</span>

<!-- Pass -->
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xxs font-medium bg-green-50 text-green-700">
  <i class="fas fa-check text-xxs"></i> ผ่าน
</span>

<!-- Fail -->
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xxs font-medium bg-red-50 text-red-600">
  <i class="fas fa-xmark text-xxs"></i> ไม่ผ่าน
</span>

<!-- Scheduled -->
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xxs font-medium bg-blue-50 text-blue-700">
  <i class="fas fa-clock text-xxs"></i> รอเปิด
</span>
```

### 4.7 Table
```html
<div class="overflow-x-auto">
  <table class="w-full text-sm">
    <thead>
      <tr class="bg-gray-50 border-y border-gray-100">
        <th class="text-left text-xs font-medium text-gray-400 uppercase tracking-wider px-4 py-3">โครงการ</th>
        <th class="text-left text-xs font-medium text-gray-400 uppercase tracking-wider px-4 py-3">วันที่</th>
        <th class="text-left text-xs font-medium text-gray-400 uppercase tracking-wider px-4 py-3">ผู้เข้า</th>
        <th class="text-left text-xs font-medium text-gray-400 uppercase tracking-wider px-4 py-3">สถานะ</th>
        <th class="px-4 py-3"></th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-50">
      <tr class="hover:bg-primary-50/40 transition-colors duration-100 group">
        <td class="px-4 py-3 font-medium text-gray-800">อบรม AI เบื้องต้น</td>
        <td class="px-4 py-3 text-gray-500 text-xs">1–7 มิ.ย. 2568</td>
        <td class="px-4 py-3 text-gray-600">45</td>
        <td class="px-4 py-3">
          <!-- badge here -->
        </td>
        <td class="px-4 py-3">
          <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
            <button class="w-7 h-7 flex items-center justify-center rounded-md hover:bg-primary-50 text-gray-400 hover:text-primary-400 transition-colors">
              <i class="fas fa-pen text-xxs"></i>
            </button>
            <button class="w-7 h-7 flex items-center justify-center rounded-md hover:bg-red-50 text-gray-400 hover:text-red-500 transition-colors">
              <i class="fas fa-trash text-xxs"></i>
            </button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</div>
```

### 4.8 Modal (SweetAlert2 Custom Class)
```javascript
// ใช้ customClass เพื่อ inject Tailwind classes ใน SweetAlert2
Swal.fire({
  title: 'ยืนยันการลบ',
  text: 'ข้อมูลจะถูกลบถาวรและไม่สามารถกู้คืนได้',
  icon: 'warning',
  showCancelButton: true,
  confirmButtonText: 'ลบเลย',
  cancelButtonText: 'ยกเลิก',
  customClass: {
    popup:          'rounded-2xl font-sans',
    title:          'text-lg font-semibold text-gray-800',
    htmlContainer:  'text-sm text-gray-500',
    confirmButton:  '!bg-red-600 hover:!bg-red-700 !text-white !rounded-lg !text-sm !px-5 !py-2 !font-medium',
    cancelButton:   '!bg-white !text-gray-600 !border !border-gray-200 !rounded-lg !text-sm !px-5 !py-2 !font-medium',
    actions:        'gap-2',
  },
  buttonsStyling: false,
});
```

### 4.9 Notification Toast
```javascript
const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3500,
  timerProgressBar: true,
  customClass: {
    popup: 'rounded-xl font-sans text-sm shadow-card-hover',
  },
});

// Usage:
Toast.fire({ icon: 'success', title: 'บันทึกข้อมูลสำเร็จ' });
Toast.fire({ icon: 'error',   title: 'เกิดข้อผิดพลาด กรุณาลองใหม่' });
Toast.fire({ icon: 'info',    title: 'กำลังประมวลผล...' });
```

---

## 5. Page-Specific Patterns

### 5.1 Dashboard (views/dashboard/index.php)
```html
<!-- Stats Grid: 4 columns -->
<div class="grid grid-cols-4 gap-4 mb-6">
  <!-- stat cards here (ดู 4.1) -->
</div>

<!-- Two-column layout: table + sidebar widgets -->
<div class="grid grid-cols-3 gap-4">
  <div class="col-span-2 space-y-4">
    <!-- Recent projects table -->
    <!-- Recent exam results table -->
  </div>
  <div class="space-y-4">
    <!-- Pass rate chart -->
    <!-- Certificate templates gallery -->
    <!-- Quick actions -->
  </div>
</div>
```

### 5.2 Exam Entry (views/exam/entry.php — Public Page)
```html
<!-- Full-screen centered, no sidebar -->
<body class="min-h-screen bg-gradient-to-br from-primary-50 via-white to-orange-50 flex items-center justify-center p-4">
  <div class="w-full max-w-md">

    <!-- Logo + Project info -->
    <div class="text-center mb-8">
      <div class="w-16 h-16 bg-primary-400 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-orange">
        <i class="fas fa-award text-white text-2xl"></i>
      </div>
      <h1 class="text-xl font-semibold text-gray-800"><?= htmlspecialchars($project['name']) ?></h1>
      <p class="text-sm text-gray-400 mt-1"><?= htmlspecialchars($project['organizer']) ?></p>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-card-hover p-8">
      <h2 class="text-base font-semibold text-gray-800 mb-1">เข้าสู่การทำแบบทดสอบ</h2>
      <p class="text-xs text-gray-400 mb-6">กรอกชื่อ-นามสกุลตามที่ลงทะเบียนไว้</p>

      <form method="POST" id="entry-form" class="space-y-4">
        <!-- input fields -->
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">คำนำหน้าชื่อ</label>
          <select name="title" class="w-full h-10 px-3 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-400 focus:ring-[3px] focus:ring-primary-400/20">
            <option value="นาย">นาย</option>
            <option value="นาง">นาง</option>
            <option value="นางสาว">นางสาว</option>
            <option value="ดร.">ดร.</option>
          </select>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">ชื่อ <span class="text-primary-400">*</span></label>
            <input type="text" name="first_name" required
              class="w-full h-10 px-3 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-400 focus:ring-[3px] focus:ring-primary-400/20">
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">นามสกุล <span class="text-primary-400">*</span></label>
            <input type="text" name="last_name" required
              class="w-full h-10 px-3 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-400 focus:ring-[3px] focus:ring-primary-400/20">
          </div>
        </div>
        <button type="submit"
          class="w-full h-11 bg-primary-400 hover:bg-primary-500 text-white font-semibold rounded-xl transition-colors duration-150 text-sm">
          เข้าสู่การสอบ <i class="fas fa-arrow-right ml-1"></i>
        </button>
      </form>
    </div>

    <!-- Exam info footer -->
    <div class="mt-4 flex items-center justify-center gap-6 text-xs text-gray-400">
      <span><i class="fas fa-clock mr-1"></i><?= $project['time_limit_min'] ?> นาที</span>
      <span><i class="fas fa-list-check mr-1"></i><?= $project['question_count'] ?> ข้อ</span>
      <span><i class="fas fa-percent mr-1"></i>ผ่าน <?= $project['pass_score'] ?>%</span>
    </div>
  </div>
</body>
```

### 5.3 Exam Interface (views/exam/start.php)
```html
<body class="min-h-screen bg-[#F9F8F6]">

  <!-- Fixed Top Bar (Timer + Progress) -->
  <header class="fixed top-0 inset-x-0 bg-white border-b border-gray-100 z-20 shadow-sm">
    <div class="max-w-4xl mx-auto px-4 h-14 flex items-center justify-between">

      <div class="flex items-center gap-3">
        <div class="w-8 h-8 bg-primary-50 rounded-lg flex items-center justify-center">
          <i class="fas fa-award text-primary-400 text-sm"></i>
        </div>
        <div>
          <p class="text-xs font-medium text-gray-700 leading-tight"><?= htmlspecialchars($project['name']) ?></p>
          <p class="text-xxs text-gray-400"><?= htmlspecialchars($participant['first_name'].' '.$participant['last_name']) ?></p>
        </div>
      </div>

      <!-- Timer -->
      <div id="exam-timer"
        class="flex items-center gap-2 px-4 py-1.5 bg-gray-50 rounded-xl border border-gray-200 font-mono text-sm font-semibold text-gray-700">
        <i class="fas fa-clock text-primary-400 text-xs"></i>
        <span id="timer-display">60:00</span>
      </div>

      <!-- Progress -->
      <div class="text-right">
        <p class="text-xxs text-gray-400">ความคืบหน้า</p>
        <p class="text-sm font-semibold text-gray-700">
          <span id="answered-count">0</span>/<span><?= count($questions) ?></span> ข้อ
        </p>
      </div>

    </div>
    <!-- Progress bar -->
    <div class="h-1 bg-gray-100">
      <div id="progress-bar"
        class="h-full bg-primary-400 progress-bar-fill" style="width: 0%"></div>
    </div>
  </header>

  <!-- Main Content -->
  <div class="max-w-4xl mx-auto px-4 pt-20 pb-32">

    <!-- Question -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-card p-6 mb-4">
      <div class="flex items-start justify-between mb-4">
        <span class="text-xxs font-medium text-primary-400 bg-primary-50 px-2.5 py-1 rounded-full">
          ข้อที่ <span id="q-num">1</span> จาก <?= count($questions) ?>
        </span>
        <!-- Category badge (if any) -->
      </div>
      <p id="q-text" class="text-gray-800 font-medium leading-relaxed text-base">
        <!-- question text -->
      </p>
    </div>

    <!-- Options (Multiple Choice) -->
    <div id="options-container" class="space-y-2.5">
      <!-- Dynamic via JS -->
      <!-- Pattern: -->
      <!--
      <label class="exam-option flex items-center gap-3 p-4 bg-white rounded-xl border border-gray-200
                    cursor-pointer hover:border-primary-300 hover:bg-primary-50/50 transition-all duration-150">
        <input type="radio" name="answer" value="a" class="sr-only">
        <span class="option-marker w-8 h-8 rounded-full border-2 border-gray-200 flex items-center
                     justify-center text-xs font-semibold text-gray-400 flex-shrink-0">A</span>
        <span class="text-sm text-gray-700">ตัวเลือก A</span>
      </label>
      -->
    </div>

  </div>

  <!-- Fixed Bottom Bar (Navigation) -->
  <div class="fixed bottom-0 inset-x-0 bg-white border-t border-gray-100 z-20">
    <div class="max-w-4xl mx-auto px-4 py-3 flex items-center justify-between">

      <!-- Prev -->
      <button id="btn-prev" onclick="prevQuestion()"
        class="inline-flex items-center gap-2 px-4 py-2 text-sm text-gray-500 hover:text-gray-700
               border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors disabled:opacity-40">
        <i class="fas fa-chevron-left text-xs"></i> ก่อนหน้า
      </button>

      <!-- Question dots (max 10 shown) -->
      <div id="q-dots" class="flex items-center gap-1.5 overflow-x-auto max-w-xs">
        <!-- dots rendered by JS -->
      </div>

      <!-- Next / Submit -->
      <button id="btn-next" onclick="nextQuestion()"
        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white
               bg-primary-400 hover:bg-primary-500 rounded-xl transition-colors">
        ถัดไป <i class="fas fa-chevron-right text-xs"></i>
      </button>

    </div>
  </div>

</body>
```

### 5.4 Exam Result (views/exam/result.php)
```html
<body class="min-h-screen bg-gradient-to-br from-primary-50 via-white to-orange-50 flex items-center justify-center p-4">
  <div class="w-full max-w-lg text-center">

    <!-- Pass: show trophy / Fail: show retry -->
    <?php if ($session['result'] === 'pass'): ?>

      <!-- Animated checkmark -->
      <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <i class="fas fa-trophy text-4xl text-green-600"></i>
      </div>
      <h1 class="text-2xl font-bold text-gray-800 mb-1">ยินดีด้วย! 🎉</h1>
      <p class="text-gray-400 mb-6">คุณผ่านการทดสอบเรียบร้อยแล้ว</p>

    <?php else: ?>

      <div class="w-24 h-24 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6">
        <i class="fas fa-rotate-right text-4xl text-red-400"></i>
      </div>
      <h1 class="text-2xl font-bold text-gray-800 mb-1">ไม่ผ่านการทดสอบ</h1>
      <p class="text-gray-400 mb-6">คุณสามารถทำซ้ำได้อีก <?= $remainingAttempts ?> ครั้ง</p>

    <?php endif; ?>

    <!-- Score Card -->
    <div class="bg-white rounded-2xl shadow-card-hover p-6 mb-4 text-left">
      <div class="flex items-center justify-between mb-4">
        <span class="text-sm font-medium text-gray-700">คะแนนที่ได้</span>
        <span class="text-3xl font-bold text-primary-400">
          <?= $session['percent'] ?>%
        </span>
      </div>
      <!-- Score progress bar -->
      <div class="h-3 bg-gray-100 rounded-full overflow-hidden mb-4">
        <div class="h-full rounded-full progress-bar-fill
          <?= $session['result'] === 'pass' ? 'bg-green-500' : 'bg-red-400' ?>"
          style="width: <?= $session['percent'] ?>%"></div>
      </div>
      <div class="grid grid-cols-3 divide-x divide-gray-100 text-center">
        <div class="px-3">
          <p class="text-lg font-semibold text-gray-800"><?= $session['score'] ?></p>
          <p class="text-xxs text-gray-400">คะแนนได้</p>
        </div>
        <div class="px-3">
          <p class="text-lg font-semibold text-gray-800"><?= $session['total_score'] ?></p>
          <p class="text-xxs text-gray-400">คะแนนเต็ม</p>
        </div>
        <div class="px-3">
          <p class="text-lg font-semibold text-<?= $session['result']==='pass'?'green':'red' ?>-600">
            <?= $project['pass_score'] ?>%
          </p>
          <p class="text-xxs text-gray-400">เกณฑ์ผ่าน</p>
        </div>
      </div>
    </div>

    <!-- CTA Buttons -->
    <?php if ($session['result'] === 'pass'): ?>
    <a href="<?= BASE_URL ?>/certificates/download/<?= $certificate['verify_token'] ?>"
      class="block w-full py-3 bg-primary-400 hover:bg-primary-500 text-white font-semibold rounded-xl transition-colors mb-2 text-sm">
      <i class="fas fa-download mr-2"></i> ดาวน์โหลดเกียรติบัตร
    </a>
    <?php endif; ?>

  </div>
</body>
```

---

## 6. JavaScript Helpers (assets/js/app.js)

```javascript
// ========================
// GLOBAL CONFIG
// ========================
const BASE_URL   = document.querySelector('meta[name="base-url"]')?.content ?? '';
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

// ========================
// AJAX HELPER
// ========================
function ajax(url, data = {}, method = 'POST') {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: BASE_URL + url,
      type: method,
      data: { ...data, csrf_token: CSRF_TOKEN },
      dataType: 'json',
      success: res => resolve(res),
      error: (xhr) => reject(xhr),
    });
  });
}

// ========================
// TOAST SHORTHAND
// ========================
const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3500,
  timerProgressBar: true,
  customClass: { popup: 'rounded-xl font-sans text-sm' },
});

window.toast = {
  success: (msg) => Toast.fire({ icon: 'success', title: msg }),
  error:   (msg) => Toast.fire({ icon: 'error',   title: msg }),
  info:    (msg) => Toast.fire({ icon: 'info',     title: msg }),
  warning: (msg) => Toast.fire({ icon: 'warning',  title: msg }),
};

// ========================
// CONFIRM DIALOG
// ========================
function confirmDelete(message, onConfirm) {
  Swal.fire({
    title: 'ยืนยันการลบ',
    text: message ?? 'ข้อมูลจะถูกลบถาวร',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: '<i class="fas fa-trash mr-1"></i> ลบเลย',
    cancelButtonText: 'ยกเลิก',
    customClass: {
      popup:         'rounded-2xl font-sans',
      confirmButton: '!bg-red-600 hover:!bg-red-700 !text-white !rounded-lg !text-sm !px-4 !py-2 !font-medium',
      cancelButton:  '!bg-white !text-gray-600 !border !border-gray-200 !rounded-lg !text-sm !px-4 !py-2',
      actions: 'gap-2',
    },
    buttonsStyling: false,
  }).then(r => r.isConfirmed && onConfirm());
}

// ========================
// LOADING OVERLAY
// ========================
function showLoading(msg = 'กำลังดำเนินการ...') {
  Swal.fire({
    title: msg,
    allowOutsideClick: false,
    allowEscapeKey: false,
    customClass: { popup: 'rounded-2xl font-sans' },
    didOpen: () => Swal.showLoading(),
  });
}
function hideLoading() { Swal.close(); }

// ========================
// THAI DATE FORMAT
// ========================
function thaiDate(dateStr) {
  const months = ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.',
                  'ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
  const d = new Date(dateStr);
  return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear() + 543}`;
}
```

---

## 7. Tailwind Utility Quick Reference

| สิ่งที่ต้องการ | Tailwind Classes |
|---|---|
| Card container | `bg-white rounded-xl border border-gray-100 shadow-card p-5` |
| Page background | `bg-[#F9F8F6]` |
| Primary button | `bg-primary-400 hover:bg-primary-500 text-white rounded-lg px-4 py-2 text-sm font-medium` |
| Outline button | `bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 rounded-lg px-4 py-2 text-sm` |
| Form input | `border border-gray-200 rounded-xl h-10 px-3 text-sm focus:outline-none focus:border-primary-400 focus:ring-[3px] focus:ring-primary-400/20` |
| Section heading | `text-sm font-semibold text-gray-800` |
| Muted text | `text-xs text-gray-400` |
| Divider | `border-t border-gray-100 my-4` |
| Badge green | `bg-green-50 text-green-700 text-xxs font-medium px-2.5 py-1 rounded-full` |
| Badge orange | `bg-primary-100 text-primary-800 text-xxs font-medium px-2.5 py-1 rounded-full` |
| Sidebar width | `w-56` (224px) |
| Content offset | `ml-56 pt-14` |
| Grid 4-col | `grid grid-cols-4 gap-4` |

---

## 8. JS Includes (layout/footer.php)

```html
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Chart.js (เฉพาะหน้าที่ต้องการ) -->
  <?php if ($useCharts ?? false): ?>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <?php endif; ?>

  <!-- App globals -->
  <meta name="base-url"    content="<?= BASE_URL ?>">
  <meta name="csrf-token"  content="<?= $_SESSION['csrf_token'] ?>">
  <script src="<?= BASE_URL ?>/assets/js/app.js"></script>

  <!-- Page-specific JS (optional slot) -->
  <?php if (!empty($pageScripts)) echo $pageScripts; ?>

</body>
</html>
```

---

*Frontend Guide v1.0 — ExamCert System*
*Tailwind CSS CDN v3 · Sarabun Font · SweetAlert2 · jQuery 3.x · Font Awesome 6*
