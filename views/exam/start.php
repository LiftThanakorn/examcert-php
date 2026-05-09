<style>
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

<script>
// ── PREVENT BACK NAVIGATION (Immediate) ───────────────────────────
(function() {
    // Push twice to create a buffer
    history.pushState(null, null, location.href);
    history.pushState(null, null, location.href);
    
    window.addEventListener('popstate', function() {
        history.pushState(null, null, location.href);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'คำเตือน!',
                text: 'ไม่สามารถกดย้อนกลับได้ในขณะทำข้อสอบ หากต้องการออกกรุณาส่งข้อสอบ',
                icon: 'warning',
                confirmButtonText: 'รับทราบ',
                customClass: { popup: 'rounded-xl font-sans text-sm' }
            });
        } else {
            alert('ไม่สามารถกดย้อนกลับได้ในขณะทำข้อสอบ หากต้องการออกกรุณาส่งข้อสอบ');
        }
    });

    window.addEventListener('beforeunload', function (e) {
        if (window.isSubmitting) return;
        e.preventDefault();
        e.returnValue = '';
    });
})();

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
window.isSubmitting = false;
let current  = 0;                          
let answers  = new Array(QUESTIONS.length).fill(null); 
let totalSec = <?= (int)$secondsLeft ?>;                    
let timerInterval;
let direction = 'right';                   
let sessionId = <?= (int)$session['id'] ?>;

// ── INIT ──────────────────────────────────────────────────────────
$(document).ready(() => {
    renderQuestion();
    renderPalette();
    renderDots();
    startTimer();
    updateProgress();
});

function getBaseUrl() {
    return $('meta[name="base-url"]').attr('content') || '';
}

function saveAnswer(questionId, answer) {
    $.ajax({
        url: getBaseUrl() + '/api/exam.php?action=save_answer',
        type: 'POST',
        data: {
            session_id: sessionId,
            question_id: questionId,
            answer: answer,
            csrf_token: $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        success: function(res) {
            if (!res.success) console.error('Auto-save failed:', res.message);
        }
    });
}

// ── TIMER & HEARTBEAT ─────────────────────────────────────────────
function startTimer() {
  // Initial sync
  checkStatus();

  timerInterval = setInterval(() => {
    totalSec--;
    updateTimerDisplay();

    // Secondary local check (server check is every 30s)
    if (totalSec <= 0) {
      clearInterval(timerInterval);
      autoSubmit();
    }
  }, 1000);

  // Heartbeat every 30 seconds to sync status and enforce project schedule
  setInterval(checkStatus, 30000);
}

function checkStatus() {
  $.ajax({
    url: getBaseUrl() + '/api/exam.php?action=check_time',
    type: 'GET',
    data: { session_id: sessionId },
    dataType: 'json',
    success: function(res) {
      if (res.success && res.data) {
        const data = res.data;
        
        // Sync time if server reports lower seconds_left
        if (data.seconds_left !== null && data.seconds_left < totalSec) {
          totalSec = data.seconds_left;
          updateTimerDisplay();
        }

        // Handle auto-submit (Project closed, time out, etc.)
        if (data.should_submit && !window.isSubmitting) {
          clearInterval(timerInterval);
          autoSubmit(data.message || 'โครงการปิดการเข้าสอบแล้ว ระบบกำลังส่งคำตอบ...');
        }

        // Handle Warning banner
        if (data.warning) {
          showWarningBanner(Math.ceil(totalSec / 60));
        }
      }
    }
  });
}

function updateTimerDisplay() {
  const m = String(Math.floor(totalSec / 60)).padStart(2,'0');
  const s = String(totalSec % 60).padStart(2,'0');
  $('#timer-display').text(`${m}:${s}`);
}

function showWarningBanner(minutes) {
  const $banner = $('#warning-banner');
  $('#warn-time').text(`${minutes} นาที`);
  $banner.removeClass('hidden');
  setTimeout(() => $banner.addClass('hidden'), 8000);
}

// ── RENDER QUESTION ───────────────────────────────────────────────
function renderQuestion(dir = 'right') {
  if (!QUESTIONS || QUESTIONS.length === 0) {
      $('#q-text').html('<div class="text-center py-10 text-gray-400"><i class="fas fa-exclamation-circle mb-2 text-2xl block"></i>ไม่พบข้อมูลข้อสอบในระบบ</div>');
      return;
  }
  
  const q    = QUESTIONS[current];
  const $card = $('#question-card');

  // Animate
  $card.removeClass('slide-right slide-left');
  void $card[0].offsetWidth; // reflow
  $card.addClass(dir === 'right' ? 'slide-right' : 'slide-left');

  $('#q-num').text(current + 1);
  $('#q-text').text(q.text);
  $('#q-category').text(q.category);
  $('#q-difficulty').text(q.difficulty).attr('class', `text-xxs ${q.diffColor}`);

  // Options
  const $container = $('#options-container').empty();
  
  if (q.type === 'fill_blank') {
      $container.append(`
        <div class="mt-4">
            <input type="text" 
                class="w-full px-5 py-4 border-2 border-gray-100 rounded-2xl bg-gray-50/50 
                       focus:border-primary-400 focus:bg-white focus:ring-4 focus:ring-primary-400/10 
                       transition-all outline-none font-medium text-gray-700"
                placeholder="พิมพ์คำตอบของคุณที่นี่..."
                value="${answers[current] || ''}"
                oninput="selectAnswer(this.value)">
        </div>
      `);
  } else {
      q.choices.forEach(choice => {
        const checked = answers[current] === choice.key;
        $container.append(`
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
  $('#btn-prev').prop('disabled', current === 0).css('opacity', current === 0 ? '0.35' : '1');

  const isLast = current === QUESTIONS.length - 1;
  const $btnNext = $('#btn-next');
  if (isLast) {
    $btnNext.html('<i class="fas fa-paper-plane text-xs"></i> ส่งข้อสอบ')
            .attr('onclick', 'confirmSubmit()')
            .removeClass('bg-primary-400 hover:bg-primary-500').addClass('bg-green-600 hover:bg-green-700');
  } else {
    $btnNext.html('ถัดไป <i class="fas fa-chevron-right text-xs"></i>')
            .attr('onclick', 'navigate(1)')
            .removeClass('bg-green-600 hover:bg-green-700').addClass('bg-primary-400 hover:bg-primary-500');
  }

  updateProgress();
  renderDots();
  renderPalette();
}

// ── SELECT ANSWER ─────────────────────────────────────────────────
function selectAnswer(key) {
  answers[current] = key;
  updateProgress();
  renderPalette();
  renderDots();
  
  saveAnswer(QUESTIONS[current].id, key);

  // Update UI for radio items
  if (QUESTIONS[current].type !== 'fill_blank') {
      $('.option-label').each(function() {
        const input = $(this).closest('label').find('input')[0];
        const chosen = input.value === key;
        $(this).toggleClass('border-primary-400 bg-primary-50', chosen).toggleClass('border-gray-100', !chosen);
        $(this).find('.option-key').toggleClass('bg-primary-400 border-primary-400 text-white', chosen).toggleClass('text-gray-400', !chosen);
        $(this).find('.option-text').toggleClass('text-primary-800 font-medium', chosen);
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
  $('#answered-count').text(done);
  $('#progress-bar').css('width', pct + '%');
}

// ── PALETTE ───────────────────────────────────────────────────────
function renderPalette() {
  const $grid = $('#palette-grid').empty();
  QUESTIONS.forEach((q, i) => {
    const answered = answers[i] !== null && answers[i] !== '';
    const isCurrent = i === current;
    let cls = 'w-full aspect-square rounded-lg text-xxs font-semibold flex items-center justify-center cursor-pointer q-dot transition-all ';
    if (isCurrent)   cls += 'bg-primary-400 text-white shadow-orange scale-105';
    else if (answered) cls += 'bg-green-100 text-green-700 border border-green-200';
    else               cls += 'bg-gray-100 text-gray-500 border border-gray-200 hover:border-primary-200 hover:bg-primary-50';
    $grid.append(`<button class="${cls}" onclick="goToQuestion(${i})">${i + 1}</button>`);
  });
}

function togglePalette() {
  $('#palette-grid').toggleClass('hidden');
  $('#palette-chevron').toggleClass('rotate-180');
}

// ── DOT NAV (bottom bar) ──────────────────────────────────────────
function renderDots() {
  const $nav = $('#dot-nav').empty();
  const total  = QUESTIONS.length;
  const winSize = 5;
  let start = Math.max(0, current - Math.floor(winSize / 2));
  let end   = Math.min(total - 1, start + winSize - 1);
  if (end - start < winSize - 1) start = Math.max(0, end - winSize + 1);

  if (start > 0) $nav.append('<span class="text-gray-300 text-xxs px-1">...</span>');

  for (let i = start; i <= end; i++) {
    const answered = answers[i] !== null && answers[i] !== '';
    const isCur    = i === current;
    let cls = 'w-7 h-7 rounded-lg text-xxs font-semibold flex items-center justify-center cursor-pointer transition-all flex-shrink-0 ';
    if (isCur)      cls += 'bg-primary-400 text-white';
    else if(answered) cls += 'bg-green-100 text-green-700 border border-green-200';
    else              cls += 'bg-gray-100 text-gray-400 hover:bg-primary-50 border border-gray-200';
    $nav.append(`<button class="${cls}" onclick="goToQuestion(${i})">${i+1}</button>`);
  }

  if (end < total - 1) $nav.append('<span class="text-gray-300 text-xxs px-1">...</span>');
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
  window.isSubmitting = true;
  
  Swal.fire({
    title: 'กำลังส่งข้อสอบ...',
    allowOutsideClick: false,
    allowEscapeKey: false,
    customClass: { popup: 'rounded-2xl font-sans' },
    didOpen: () => Swal.showLoading(),
  });
  $('#exam-form').submit();
}

function autoSubmit(msg = 'ระบบกำลังส่งคำตอบให้อัตโนมัติ...') {
  window.isSubmitting = true;
  Swal.fire({
    icon: 'info',
    title: 'หมดเวลาหรือโครงการปิดแล้ว!',
    text: msg,
    allowOutsideClick: false,
    allowEscapeKey: false,
    timer: 3000,
    showConfirmButton: false,
    customClass: { popup: 'rounded-2xl font-sans' },
  }).then(() => submitExam());
}
</script>
