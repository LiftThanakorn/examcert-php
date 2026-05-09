<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ห้องสอบออนไลน์ | <?= e($project['name']) ?></title>
<meta name="base-url" content="<?= e(BASE_URL) ?>">

<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        primary: {
          50:'#FFF3E8', 100:'#FAEEDA', 200:'#FAC775',
          300:'#EF9F27', 400:'#E87722', 500:'#C4601A',
          600:'#9E4A12', 700:'#7A360C', 800:'#633806', 900:'#412402',
        },
      },
      fontFamily: { sans: ['Sarabun','Noto Sans Thai','sans-serif'] },
      fontSize:   { xxs: '0.65rem' },
      boxShadow: {
        card:     '0 1px 4px rgba(0,0,0,0.07)',
        'card-md':'0 4px 16px rgba(0,0,0,0.10)',
        orange:   '0 0 0 3px rgba(232,119,34,0.20)',
      },
    },
  },
}
</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
  body { font-family:'Sarabun','Noto Sans Thai',sans-serif; -webkit-font-smoothing:antialiased; }

  /* Option selected state */
  .option-item input[type="radio"]:checked ~ .option-label {
    border-color: #E87722;
    background-color: #FFF3E8;
  }
  .option-item input[type="radio"]:checked ~ .option-label .option-key {
    background-color: #E87722;
    border-color: #E87722;
    color: #fff;
  }
  .option-item input[type="radio"]:checked ~ .option-label .option-text {
    color: #7A360C;
    font-weight: 500;
  }

  /* Timer danger pulse */
  @keyframes pulse-red {
    0%,100% { color:#dc2626; opacity:1; }
    50%      { color:#dc2626; opacity:0.55; }
  }
  .timer-danger { animation: pulse-red 1s ease-in-out infinite; }

  /* Progress fill animation */
  .progress-fill { transition: width 0.5s cubic-bezier(.4,0,.2,1); }

  /* Question dot */
  .q-dot { transition: all 0.2s; }

  /* Slide transition */
  @keyframes slideInRight {
    from { opacity:0; transform:translateX(18px); }
    to   { opacity:1; transform:translateX(0); }
  }
  @keyframes slideInLeft {
    from { opacity:0; transform:translateX(-18px); }
    to   { opacity:1; transform:translateX(0); }
  }
  .slide-right { animation: slideInRight 0.25s ease both; }
  .slide-left  { animation: slideInLeft  0.25s ease both; }

  /* Warning banner */
  @keyframes slideDown {
    from { transform:translateY(-100%); opacity:0; }
    to   { transform:translateY(0);     opacity:1; }
  }
  .warning-banner { animation: slideDown 0.3s ease both; }
</style>
</head>

<body class="bg-[#F9F8F6] min-h-screen">

<!-- ===================== WARNING BANNER (hidden by default) ===================== -->
<div id="warning-banner" class="hidden fixed top-0 inset-x-0 z-50 warning-banner">
  <div class="bg-amber-500 text-white text-sm font-medium px-6 py-2.5 flex items-center justify-center gap-3">
    <i class="fas fa-triangle-exclamation"></i>
    <span>เหลือเวลาอีก <strong id="warn-time">10 นาที</strong> — กรุณาตรวจสอบคำตอบและส่งข้อสอบ</span>
    <button onclick="document.getElementById('warning-banner').classList.add('hidden')" class="ml-4 text-white/70 hover:text-white">
      <i class="fas fa-xmark"></i>
    </button>
  </div>
</div>

<!-- ===================== TOP BAR ===================== -->
<header class="fixed top-0 inset-x-0 bg-white border-b border-gray-100 z-30 shadow-card">
  <div class="max-w-6xl mx-auto px-4 h-14 flex items-center justify-between gap-4">

    <!-- Project info -->
    <div class="flex items-center gap-3 min-w-0">
      <div class="w-8 h-8 bg-primary-50 rounded-lg flex items-center justify-center flex-shrink-0">
        <i class="fas fa-award text-primary-400 text-sm"></i>
      </div>
      <div class="min-w-0">
        <p class="text-xs font-semibold text-gray-800 truncate leading-tight"><?= e($project['name']) ?></p>
        <p class="text-xxs text-gray-400 truncate"><?= e($participant['title'] . $participant['first_name'] . ' ' . $participant['last_name']) ?></p>
      </div>
    </div>

    <!-- Timer -->
    <div id="timer-box"
      class="flex items-center gap-2 px-4 py-1.5 bg-gray-50 border border-gray-200 rounded-xl font-mono text-sm font-semibold text-gray-700 flex-shrink-0 transition-colors duration-300">
      <i class="fas fa-clock text-primary-400 text-xs" id="timer-icon"></i>
      <span id="timer-display">--:--</span>
    </div>

    <!-- Progress info -->
    <div class="text-right flex-shrink-0">
      <p class="text-xxs text-gray-400 leading-tight">ตอบแล้ว</p>
      <p class="text-sm font-semibold text-gray-700">
        <span id="answered-count" class="text-primary-400">0</span>
        <span class="text-gray-300">/</span>
        <span id="total-count"><?= count($questions) ?></span>
      </p>
    </div>

  </div>

  <!-- Progress bar -->
  <div class="h-1 bg-gray-100">
    <div id="progress-bar" class="h-full bg-primary-400 rounded-r-full progress-fill" style="width:0%"></div>
  </div>
</header>

<!-- ===================== MAIN ===================== -->
<div class="max-w-6xl mx-auto px-4 pt-20 pb-32">

  <!-- Question card -->
  <div id="question-card" class="bg-white rounded-2xl border border-gray-100 shadow-card p-8 mb-6 slide-right">

    <!-- Header row -->
    <div class="flex items-center justify-between mb-5">
      <div class="flex items-center gap-2">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary-50 text-primary-600 text-xxs font-semibold">
          <i class="fas fa-circle-question text-xxs"></i>
          ข้อที่ <span id="q-num">1</span> จาก <span id="q-total"><?= count($questions) ?></span>
        </span>
        <span id="q-category" class="px-2.5 py-1 rounded-full bg-gray-100 text-gray-500 text-xxs font-medium">--</span>
      </div>
      <!-- Difficulty -->
      <div class="flex items-center gap-1">
        <i class="fas fa-signal text-green-500 text-xs" id="q-diff-icon"></i>
        <span class="text-xxs text-gray-400" id="q-difficulty">--</span>
      </div>
    </div>

    <!-- Question text -->
    <div id="q-text" class="text-gray-800 font-medium leading-relaxed text-base mb-6">
      --
    </div>

    <!-- Options -->
    <div id="options-container" class="space-y-2.5">
      <!-- rendered by JS -->
    </div>

  </div>

  <!-- Question palette (mobile-friendly collapsible) -->
  <div class="bg-white rounded-2xl border border-gray-100 shadow-card p-4">
    <button onclick="togglePalette()" class="w-full flex items-center justify-between text-sm font-medium text-gray-700 mb-3">
      <span class="flex items-center gap-2">
        <i class="fas fa-grip-dots text-primary-400"></i>
        แผงคำถามทั้งหมด
      </span>
      <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform" id="palette-chevron"></i>
    </button>
    <div id="palette-grid" class="grid grid-cols-5 sm:grid-cols-10 gap-1.5">
      <!-- rendered by JS -->
    </div>
    <!-- Legend -->
    <div class="flex items-center gap-4 mt-3 pt-3 border-t border-gray-50">
      <div class="flex items-center gap-1.5">
        <div class="w-5 h-5 rounded-md bg-primary-400"></div>
        <span class="text-xxs text-gray-400">กำลังดู</span>
      </div>
      <div class="flex items-center gap-1.5">
        <div class="w-5 h-5 rounded-md bg-green-100 border border-green-200"></div>
        <span class="text-xxs text-gray-400">ตอบแล้ว</span>
      </div>
      <div class="flex items-center gap-1.5">
        <div class="w-5 h-5 rounded-md bg-gray-100 border border-gray-200"></div>
        <span class="text-xxs text-gray-400">ยังไม่ตอบ</span>
      </div>
    </div>
  </div>

</div>

<!-- ===================== BOTTOM NAV ===================== -->
<div class="fixed bottom-0 inset-x-0 bg-white border-t border-gray-100 z-30">
  <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between gap-3">

    <!-- Prev -->
    <button id="btn-prev" onclick="navigate(-1)"
      class="inline-flex items-center gap-2 px-4 py-2.5 text-sm text-gray-500 hover:text-gray-700
             border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
      <i class="fas fa-chevron-left text-xs"></i> ก่อนหน้า
    </button>

    <!-- Dots (max 10 visible) -->
    <div id="dot-nav" class="flex items-center gap-1 overflow-x-auto max-w-xs pb-0.5"></div>

    <!-- Next / Submit -->
    <button id="btn-next" onclick="navigate(1)"
      class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white
             bg-primary-400 hover:bg-primary-500 rounded-xl transition-colors shadow-sm">
      ถัดไป <i class="fas fa-chevron-right text-xs"></i>
    </button>

  </div>
</div>

<form id="exam-form" method="post" action="<?= e(BASE_URL) ?>/public/take-exam.php?session_id=<?= (int) $session['id'] ?>" class="hidden">
    <?= csrfField() ?>
    <input type="hidden" name="action" value="submit">
</form>

<!-- ===================== JS ===================== -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
// ── DATA FROM PHP ──────────────────────────────────────────────────
const QUESTIONS = <?= json_encode(array_map(function($q) {
    return [
        'id' => (int)$q['id'],
        'text' => $q['question_text'],
        'type' => $q['type'],
        'category' => 'ทั่วไป', 
        'difficulty' => 'ปานกลาง',
        'diffColor' => 'text-amber-500',
        'choices' => json_decode($q['choices'], true) ?: []
    ];
}, $questions)) ?>;

// ── STATE ─────────────────────────────────────────────────────────
let current  = 0;                          
let answers  = new Array(QUESTIONS.length).fill(null); 
let totalSec = <?= (int)$secondsLeft ?>;                    
let timerInterval;
let direction = 'right';                   
let sessionId = <?= (int)$session['id'] ?>;

// ── INIT ──────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    renderQuestion();
    renderPalette();
    renderDots();
    startTimer();
    updateProgress();
});

function getBaseUrl() {
    return document.querySelector('meta[name="base-url"]')?.content || '';
}

function saveAnswer(questionId, answer) {
    $.ajax({
        url: getBaseUrl() + '/api/exam.php?action=save_answer',
        type: 'POST',
        data: {
            session_id: sessionId,
            question_id: questionId,
            answer: answer,
            csrf_token: document.querySelector('input[name="csrf_token"]')?.value
        },
        dataType: 'json',
        success: function(res) {
            if (!res.success) console.error('Auto-save failed:', res.message);
        }
    });
}

// ── TIMER ─────────────────────────────────────────────────────────
function startTimer() {
  timerInterval = setInterval(() => {
    totalSec--;
    updateTimerDisplay();

    if (totalSec === 600) showWarningBanner(10);

    if (totalSec === 300) {
      document.getElementById('timer-display').classList.add('timer-danger','text-red-600');
      document.getElementById('timer-box').classList.add('border-red-200','bg-red-50');
      document.getElementById('timer-icon').classList.replace('text-primary-400','text-red-500');
      showWarningBanner(5);
    }

    if (totalSec <= 0) {
      clearInterval(timerInterval);
      autoSubmit();
    }
  }, 1000);
}

function updateTimerDisplay() {
  const m = String(Math.floor(totalSec / 60)).padStart(2,'0');
  const s = String(totalSec % 60).padStart(2,'0');
  document.getElementById('timer-display').textContent = `${m}:${s}`;
}

function showWarningBanner(minutes) {
  const banner = document.getElementById('warning-banner');
  const warnTime = document.getElementById('warn-time');
  if(warnTime) warnTime.textContent = `${minutes} นาที`;
  banner.classList.remove('hidden');
  setTimeout(() => banner.classList.add('hidden'), 8000);
}

// ── RENDER QUESTION ───────────────────────────────────────────────
function renderQuestion(dir = 'right') {
  if (!QUESTIONS || QUESTIONS.length === 0) {
      document.getElementById('q-text').innerHTML = '<div class="text-center py-10 text-gray-400"><i class="fas fa-exclamation-circle mb-2 text-2xl block"></i>ไม่พบข้อมูลข้อสอบในระบบ</div>';
      return;
  }
  
  const q    = QUESTIONS[current];
  const card = document.getElementById('question-card');

  // Animate
  card.classList.remove('slide-right','slide-left');
  void card.offsetWidth; // reflow
  card.classList.add(dir === 'right' ? 'slide-right' : 'slide-left');

  document.getElementById('q-num').textContent        = current + 1;
  document.getElementById('q-text').textContent       = q.text;
  document.getElementById('q-category').textContent   = q.category;
  document.getElementById('q-difficulty').textContent = q.difficulty;
  document.getElementById('q-difficulty').className   = `text-xxs ${q.diffColor}`;

  // Options
  const container = document.getElementById('options-container');
  container.innerHTML = '';
  
  if (q.type === 'fill_blank') {
      container.innerHTML = `
        <div class="mt-4">
            <input type="text" 
                class="w-full px-5 py-4 border-2 border-gray-100 rounded-2xl bg-gray-50/50 
                       focus:border-primary-400 focus:bg-white focus:ring-4 focus:ring-primary-400/10 
                       transition-all outline-none font-medium text-gray-700"
                placeholder="พิมพ์คำตอบของคุณที่นี่..."
                value="${answers[current] || ''}"
                oninput="selectAnswer(this.value)">
        </div>
      `;
  } else {
      q.choices.forEach(choice => {
        const checked = answers[current] === choice.key;
        container.insertAdjacentHTML('beforeend', `
          <label class="option-item block cursor-pointer select-none">
            <input type="radio" name="q${q.id}" value="${choice.key}"
              class="sr-only" ${checked ? 'checked' : ''}
              onchange="selectAnswer('${choice.key}')">
            <div class="option-label flex items-center gap-3 p-4 border-2 border-gray-100 rounded-xl
                        hover:border-primary-200 hover:bg-primary-50/50 transition-all duration-150
                        ${checked ? 'border-primary-400 bg-primary-50' : ''}">
              <span class="option-key w-9 h-9 rounded-full border-2 border-gray-200 flex items-center justify-center
                           text-sm font-semibold text-gray-400 flex-shrink-0
                           ${checked ? 'bg-primary-400 border-primary-400 text-white' : ''}">
                ${choice.key}
              </span>
              <span class="option-text text-sm text-gray-700 leading-snug
                           ${checked ? 'text-primary-800 font-medium' : ''}">
                ${choice.text}
              </span>
            </div>
          </label>
        `);
      });
  }

  // Nav buttons state
  document.getElementById('btn-prev').disabled = current === 0;
  document.getElementById('btn-prev').style.opacity = current === 0 ? '0.35' : '1';

  const isLast = current === QUESTIONS.length - 1;
  const btnNext = document.getElementById('btn-next');
  if (isLast) {
    btnNext.innerHTML = '<i class="fas fa-paper-plane text-xs"></i> ส่งข้อสอบ';
    btnNext.onclick   = confirmSubmit;
    btnNext.className = btnNext.className.replace('bg-primary-400 hover:bg-primary-500','bg-green-600 hover:bg-green-700');
  } else {
    btnNext.innerHTML = 'ถัดไป <i class="fas fa-chevron-right text-xs"></i>';
    btnNext.onclick   = () => navigate(1);
    btnNext.className = btnNext.className.replace('bg-green-600 hover:bg-green-700','bg-primary-400 hover:bg-primary-500');
  }

  updateProgress();
  renderDots();
  highlightPalette();
}

// ── SELECT ANSWER ─────────────────────────────────────────────────
function selectAnswer(key) {
  answers[current] = key;
  updateProgress();
  renderPalette();
  renderDots();
  
  // Call Auto-save from exam.js
  if (typeof saveAnswer === 'function') {
      saveAnswer(QUESTIONS[current].id, key);
  }

  // Update UI for radio items
  if (QUESTIONS[current].type !== 'fill_blank') {
      document.querySelectorAll('.option-label').forEach(el => {
        const input = el.closest('label').querySelector('input');
        const chosen = input.value === key;
        el.classList.toggle('border-primary-400', chosen);
        el.classList.toggle('bg-primary-50', chosen);
        el.classList.toggle('border-gray-100', !chosen);
        el.querySelector('.option-key').classList.toggle('bg-primary-400', chosen);
        el.querySelector('.option-key').classList.toggle('border-primary-400', chosen);
        el.querySelector('.option-key').classList.toggle('text-white', chosen);
        el.querySelector('.option-key').classList.toggle('text-gray-400', !chosen);
        el.querySelector('.option-text').classList.toggle('text-primary-800', chosen);
        el.querySelector('.option-text').classList.toggle('font-medium', chosen);
      });
  }
}

// ── NAVIGATE ─────────────────────────────────────────────────────
function navigate(delta) {
  const next = current + delta;
  if (next < 0 || next >= QUESTIONS.length) return;
  direction = delta > 0 ? 'right' : 'left';
  current   = next;
  renderQuestion(direction);
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function goToQuestion(idx) {
  direction = idx > current ? 'right' : 'left';
  current   = idx;
  renderQuestion(direction);
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ── PROGRESS ──────────────────────────────────────────────────────
function updateProgress() {
  const done = answers.filter(a => a !== null && a !== '').length;
  const pct  = (done / QUESTIONS.length) * 100;
  const answeredCountEl = document.getElementById('answered-count');
  const progressBarEl   = document.getElementById('progress-bar');
  
  if(answeredCountEl) answeredCountEl.textContent = done;
  if(progressBarEl) progressBarEl.style.width = pct + '%';
}

// ── PALETTE ───────────────────────────────────────────────────────
function renderPalette() {
  const grid = document.getElementById('palette-grid');
  if(!grid) return;
  grid.innerHTML = '';
  QUESTIONS.forEach((q, i) => {
    const answered = answers[i] !== null && answers[i] !== '';
    const isCurrent = i === current;
    let cls = 'w-full aspect-square rounded-lg text-xxs font-semibold flex items-center justify-center cursor-pointer q-dot transition-all ';
    if (isCurrent)   cls += 'bg-primary-400 text-white shadow-orange scale-105';
    else if (answered) cls += 'bg-green-100 text-green-700 border border-green-200';
    else               cls += 'bg-gray-100 text-gray-500 border border-gray-200 hover:border-primary-200 hover:bg-primary-50';
    grid.insertAdjacentHTML('beforeend',
      `<button class="${cls}" onclick="goToQuestion(${i})">${i + 1}</button>`
    );
  });
}

function highlightPalette() { renderPalette(); }

function togglePalette() {
  const grid    = document.getElementById('palette-grid');
  const chevron = document.getElementById('palette-chevron');
  if(grid) grid.classList.toggle('hidden');
  if(chevron) chevron.classList.toggle('rotate-180');
}

// ── DOT NAV (bottom bar) ──────────────────────────────────────────
function renderDots() {
  const nav = document.getElementById('dot-nav');
  if(!nav) return;
  nav.innerHTML = '';
  const total  = QUESTIONS.length;
  const winSize = 5;
  let start = Math.max(0, current - Math.floor(winSize / 2));
  let end   = Math.min(total - 1, start + winSize - 1);
  if (end - start < winSize - 1) start = Math.max(0, end - winSize + 1);

  if (start > 0) nav.insertAdjacentHTML('beforeend', `<span class="text-gray-300 text-xxs px-1">...</span>`);

  for (let i = start; i <= end; i++) {
    const answered = answers[i] !== null && answers[i] !== '';
    const isCur    = i === current;
    let cls = 'w-7 h-7 rounded-lg text-xxs font-semibold flex items-center justify-center cursor-pointer transition-all flex-shrink-0 ';
    if (isCur)      cls += 'bg-primary-400 text-white';
    else if(answered) cls += 'bg-green-100 text-green-700 border border-green-200';
    else              cls += 'bg-gray-100 text-gray-400 hover:bg-primary-50 border border-gray-200';
    nav.insertAdjacentHTML('beforeend', `<button class="${cls}" onclick="goToQuestion(${i})">${i+1}</button>`);
  }

  if (end < total - 1) nav.insertAdjacentHTML('beforeend', `<span class="text-gray-300 text-xxs px-1">...</span>`);
}

// ── SUBMIT ────────────────────────────────────────────────────────
function confirmSubmit() {
  const unanswered = answers.filter(a => a === null || a === '').length;

  Swal.fire({
    icon: unanswered > 0 ? 'warning' : 'question',
    title: 'ยืนยันการส่งข้อสอบ?',
    html: unanswered > 0
      ? `<p class="text-gray-600 text-sm">คุณยังมี <strong class="text-red-600">${unanswered} ข้อ</strong> ที่ยังไม่ได้ตอบ</p>
         <p class="text-gray-400 text-xs mt-1">คะแนนของข้อที่ไม่ตอบจะเป็น 0</p>`
      : `<p class="text-gray-600 text-sm">ตอบครบทั้ง <strong class="text-green-600">${QUESTIONS.length} ข้อ</strong> แล้ว<br>พร้อมส่งข้อสอบ</p>`,
    showCancelButton: true,
    confirmButtonText: '<i class="fas fa-paper-plane mr-1.5"></i> ส่งข้อสอบ',
    cancelButtonText: 'กลับไปตรวจสอบ',
    customClass: {
      popup:         'rounded-2xl font-sans',
      title:         'text-lg font-semibold text-gray-800',
      confirmButton: '!bg-primary-400 hover:!bg-primary-500 !text-white !rounded-xl !text-sm !px-5 !py-2.5 !font-semibold',
      cancelButton:  '!bg-white !text-gray-600 !border !border-gray-200 !rounded-xl !text-sm !px-5 !py-2.5',
      actions:       'gap-2',
    },
    buttonsStyling: false,
    reverseButtons: true,
  }).then(r => {
    if (r.isConfirmed) submitExam();
  });
}

function submitExam() {
  clearInterval(timerInterval);
  Swal.fire({
    title: 'กำลังส่งข้อสอบ...',
    allowOutsideClick: false,
    allowEscapeKey: false,
    customClass: { popup: 'rounded-2xl font-sans' },
    didOpen: () => Swal.showLoading(),
  });
  
  // Actually submit the form
  document.getElementById('exam-form').submit();
}

function autoSubmit() {
  Swal.fire({
    icon: 'info',
    title: 'หมดเวลา!',
    text: 'ระบบกำลังส่งคำตอบให้อัตโนมัติ...',
    allowOutsideClick: false,
    allowEscapeKey: false,
    timer: 2000,
    showConfirmButton: false,
    customClass: { popup: 'rounded-2xl font-sans' },
  }).then(() => submitExam());
}
</script>
</body>
</html>
