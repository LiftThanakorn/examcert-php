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
  #q-text, .option-text { overflow-wrap: anywhere; }
  @media (max-width: 640px) {
    .exam-topbar-inner {
      height: auto;
      min-height: 64px;
      padding: 8px 12px;
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      grid-template-areas:
        "info timer"
        "progress progress";
      row-gap: 6px;
      column-gap: 10px;
    }
    .exam-participant-info { grid-area: info; min-width: 0; }
    .exam-progress-summary {
      grid-area: progress;
      display: flex;
      align-items: center;
      gap: 6px;
      text-align: left;
    }
    #timer-box {
      grid-area: timer;
      padding: 6px 10px;
      border-radius: 10px;
      font-size: 12px;
    }
    .exam-main {
      padding: 90px 12px 120px;
    }
    #question-card {
      padding: 18px;
      border-radius: 16px;
      margin-bottom: 12px;
    }
    .exam-question-meta {
      align-items: flex-start;
      flex-direction: column;
      gap: 8px;
    }
    .option-label {
      align-items: flex-start;
      gap: 10px;
      padding: 12px;
    }
    .option-key {
      width: 32px;
      height: 32px;
      font-size: 12px;
    }
    .exam-palette-card {
      padding: 12px;
      border-radius: 16px;
    }
    .exam-palette-legend {
      flex-wrap: wrap;
      gap: 8px;
    }
    .exam-bottom-nav-inner {
      display: grid;
      grid-template-columns: auto minmax(0, 1fr) auto;
      gap: 8px;
      padding: 10px 12px calc(10px + env(safe-area-inset-bottom));
    }
    #btn-prev,
    #btn-next {
      min-height: 44px;
      padding: 10px 12px;
      white-space: nowrap;
    }
    #dot-nav {
      max-width: none;
      min-width: 0;
    }
  }
  @media (max-width: 380px) {
    .exam-project-icon { display: none; }
    #btn-prev,
    #btn-next {
      padding-left: 10px;
      padding-right: 10px;
      font-size: 12px;
    }
  }
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
<header class="exam-topbar fixed top-0 inset-x-0 bg-white border-b border-gray-100 z-30 shadow-card">
  <div class="exam-topbar-inner max-w-6xl mx-auto px-4 h-14 flex items-center justify-between gap-4">

    <!-- Project info -->
    <div class="exam-participant-info flex items-center gap-3 min-w-0">
      <div class="exam-project-icon w-8 h-8 bg-primary-50 rounded-lg flex items-center justify-center flex-shrink-0">
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
    <div class="exam-progress-summary text-right flex-shrink-0">
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
<div class="exam-main max-w-6xl mx-auto px-4 pt-20 pb-32">

  <!-- Question card -->
  <div id="question-card" class="bg-white rounded-2xl border border-gray-100 shadow-card p-8 mb-6 slide-right">

    <!-- Header row -->
    <div class="exam-question-meta flex items-center justify-between mb-5">
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
  <div class="exam-palette-card bg-white rounded-2xl border border-gray-100 shadow-card p-4">
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
    <div class="exam-palette-legend flex items-center gap-4 mt-3 pt-3 border-t border-gray-50">
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
<div class="exam-bottom-nav fixed bottom-0 inset-x-0 bg-white border-t border-gray-100 z-30">
  <div class="exam-bottom-nav-inner max-w-6xl mx-auto px-4 py-3 flex items-center justify-between gap-3">

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
// ── PREVENT BACK NAVIGATION ───────────────────────────────────────
(function() {
    history.pushState(null, null, location.href);
    window.addEventListener('popstate', function() {
        history.pushState(null, null, location.href);
        Swal.fire({
            title: 'คำเตือน!',
            text: 'ไม่สามารถกดย้อนกลับได้ในขณะทำข้อสอบ หากต้องการออกกรุณาส่งข้อสอบ',
            icon: 'warning',
            confirmButtonText: 'รับทราบ',
            customClass: { popup: 'rounded-xl font-sans text-sm' }
        });
    });

    window.addEventListener('beforeunload', function (e) {
        if (window.isSubmitting) return;
        e.preventDefault();
        e.returnValue = '';
    });
})();

// ── DATA FROM PHP ──────────────────────────────────────────────────
const QUESTIONS = <?= jsonForScript(array_map(function($q) use ($project) {
    $type = (string)($q['type'] ?? 'multiple_choice');
    $choiceLabels = thaiChoiceLabels();
    $choices = $type === 'rating_scale'
        ? ratingScaleChoices()
        : (json_decode((string)($q['choices'] ?? ''), true) ?: []);
    if ($type !== 'rating_scale' && (int)($project['randomize_choices'] ?? 0) === 1) {
        shuffle($choices);
    }
    $choices = array_values(array_map(static function ($choice) use ($type, $choiceLabels) {
        $key = (string)($choice['key'] ?? '');
        if ($type === 'multiple_choice') {
            $key = normalizeChoiceKey($key);
        }
        return [
            'key' => $key,
            'label' => $type === 'multiple_choice' ? ($choiceLabels[$key] ?? $key) : $key,
            'text' => (string)($choice['text'] ?? ''),
        ];
    }, $choices));

    return [
        'id' => (int)$q['id'],
        'text' => $q['question_text'],
        'type' => $type,
        'category' => trim((string)($q['category'] ?? '')) ?: 'ทั่วไป', 
        'difficulty' => trim((string)($q['difficulty'] ?? '')) ?: 'ปานกลาง',
        'diffColor' => ((string)($q['difficulty'] ?? '') === 'hard' ? 'text-red-500' : ((string)($q['difficulty'] ?? '') === 'easy' ? 'text-green-500' : 'text-amber-500')),
        'choices' => $choices
    ];
}, $questions)) ?> || [];

<?php $savedAnswersForView = $savedAnswers ?? []; ?>
const SAVED_ANSWERS = <?= jsonForScript(array_reduce($questions, static function ($carry, $q) use ($savedAnswersForView) {
    $qid = (int)$q['id'];
    $answer = trim((string)($savedAnswersForView[$qid] ?? ''));
    if ((string)($q['type'] ?? 'multiple_choice') === 'multiple_choice' && $answer !== '') {
        $answer = normalizeChoiceKey($answer);
    }
    $carry[$qid] = $answer;
    return $carry;
}, [])) ?> || {};

// ── STATE ─────────────────────────────────────────────────────────
window.isSubmitting = false;
let current  = 0;                          
let answers  = QUESTIONS.map(q => SAVED_ANSWERS[q.id] || null); 
let totalSec = <?= (int)$secondsLeft ?>;                    
let timerInterval;
let direction = 'right';                   
let sessionId = <?= (int)$session['id'] ?>;
let warningThreshold = <?= max(0, (int) ($project['warning_before'] ?? 30)) * 60 ?>;
let warningShown = false;

// ── INIT ──────────────────────────────────────────────────────────
$(document).ready(() => {
    // Show server-side error if any
    const serverError = <?= jsonForScript($error ?? '') ?>;
    if (serverError) {
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: serverError,
            customClass: { popup: 'rounded-2xl font-sans' }
        });
    }

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
    checkStatus();
    timerInterval = setInterval(() => {
        totalSec--;
        if (totalSec <= 0) {
            clearInterval(timerInterval);
            totalSec = 0;
            updateTimerDisplay();
            autoSubmit();
            return;
        }
        updateTimerDisplay();
        if (!warningShown && warningThreshold > 0 && totalSec <= warningThreshold) {
            showWarningBanner(Math.ceil(totalSec / 60));
        }
        if (totalSec <= 300) {
            $('#timer-box').addClass('text-red-500 animate-pulse');
        }
    }, 1000);
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
                if (data.seconds_left !== null && data.seconds_left < totalSec) {
                    totalSec = data.seconds_left;
                    updateTimerDisplay();
                }
                if (data.should_submit && !window.isSubmitting) {
                    clearInterval(timerInterval);
                    autoSubmit(data.message || 'โครงการปิดการเข้าสอบแล้ว ระบบกำลังส่งคำตอบ...');
                }
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
    if (warningShown) return;
    warningShown = true;
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

    $card.removeClass('slide-right slide-left');
    void $card[0].offsetWidth; 
    $card.addClass(dir === 'right' ? 'slide-right' : 'slide-left');

    $('#q-num').text(current + 1);
    $('#q-text').text(q.text);
    $('#q-category').text(q.category);
    $('#q-difficulty').text(q.difficulty).attr('class', `text-xxs ${q.diffColor}`);

    const $container = $('#options-container').empty();
    
    if (q.type === 'subjective') {
        $container.append(`
            <div class="mt-4">
                <textarea rows="7"
                    class="w-full px-5 py-4 border-2 border-gray-100 rounded-2xl bg-gray-50/50
                           focus:border-primary-400 focus:bg-white focus:ring-4 focus:ring-primary-400/10
                           transition-all outline-none font-medium text-gray-700"
                    placeholder="พิมพ์คำตอบแบบอัตนัยของคุณที่นี่..."
                    oninput="selectAnswer(this.value)">${answers[current] || ''}</textarea>
                <p class="mt-2 text-xs text-amber-600">คำตอบข้อนี้จะถูกส่งให้ตรวจแบบ manual review และไม่คิดคะแนนอัตโนมัติ</p>
            </div>
        `);
    } else if (q.type === 'fill_blank') {
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
    } else if (q.type === 'rating_scale') {
        const $scale = $('<div class="grid grid-cols-5 gap-2 sm:gap-3"></div>');
        q.choices.forEach(choice => {
            const checked = answers[current] === choice.key;
            $scale.append(`
                <label class="option-item block cursor-pointer select-none">
                    <input type="radio" name="q${q.id}" value="${choice.key}"
                        class="sr-only" ${checked ? 'checked' : ''}
                        onchange="selectAnswer('${choice.key}')">
                    <div class="option-label min-h-[76px] flex flex-col items-center justify-center gap-2 p-3 border-2 border-gray-100 rounded-xl
                                hover:border-primary-200 hover:bg-primary-50/50 transition-all duration-150
                                ${checked ? 'border-primary-400 bg-primary-50' : ''}">
                        <span class="option-key w-9 h-9 rounded-full border-2 border-gray-200 flex items-center justify-center
                                     text-sm font-semibold text-gray-400 flex-shrink-0
                                     ${checked ? 'bg-primary-400 border-primary-400 text-white' : ''}">
                            ${choice.label || choice.key}
                        </span>
                    </div>
                </label>
            `);
        });
        $container.append($scale);
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
                            ${choice.label || choice.key}
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

function selectAnswer(key) {
    answers[current] = key;
    updateProgress();
    renderPalette();
    renderDots();
    saveAnswer(QUESTIONS[current].id, key);

    if (QUESTIONS[current].type !== 'fill_blank' && QUESTIONS[current].type !== 'subjective') {
        $('.option-label').each(function() {
            const input = $(this).closest('label').find('input')[0];
            const chosen = input.value === key;
            $(this).toggleClass('border-primary-400 bg-primary-50', chosen).toggleClass('border-gray-100', !chosen);
            $(this).find('.option-key').toggleClass('bg-primary-400 border-primary-400 text-white', chosen).toggleClass('text-gray-400', !chosen);
            $(this).find('.option-text').toggleClass('text-primary-800 font-medium', chosen);
        });
    }
}

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

function updateProgress() {
    const done = answers.filter(a => a !== null && String(a).trim() !== '').length;
    const pct  = (done / QUESTIONS.length) * 100;
    $('#answered-count').text(done);
    $('#progress-bar').css('width', pct + '%');
}

function renderPalette() {
    const $grid = $('#palette-grid').empty();
    QUESTIONS.forEach((q, i) => {
        const answered = answers[i] !== null && String(answers[i]).trim() !== '';
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

function renderDots() {
    const $nav = $('#dot-nav').empty();
    const total  = QUESTIONS.length;
    const winSize = 5;
    let start = Math.max(0, current - Math.floor(winSize / 2));
    let end   = Math.min(total - 1, start + winSize - 1);
    if (end - start < winSize - 1) start = Math.max(0, end - winSize + 1);

    if (start > 0) $nav.append('<span class="text-gray-300 text-xxs px-1">...</span>');
    for (let i = start; i <= end; i++) {
        const answered = answers[i] !== null && String(answers[i]).trim() !== '';
        const isCur    = i === current;
        let cls = 'w-7 h-7 rounded-lg text-xxs font-semibold flex items-center justify-center cursor-pointer transition-all flex-shrink-0 ';
        if (isCur)      cls += 'bg-primary-400 text-white';
        else if(answered) cls += 'bg-green-100 text-green-700 border border-green-200';
        else              cls += 'bg-gray-100 text-gray-400 hover:bg-primary-50 border border-gray-200';
        $nav.append(`<button class="${cls}" onclick="goToQuestion(${i})">${i+1}</button>`);
    }
    if (end < total - 1) $nav.append('<span class="text-gray-300 text-xxs px-1">...</span>');
}

function confirmSubmit() {
    const missing = [];
    QUESTIONS.forEach((q, i) => {
        let val = answers[i];
        if (val === null || val === undefined || String(val).trim() === '') {
            missing.push(i + 1);
        }
    });

    if (missing.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาทำข้อสอบให้ครบทุกข้อ',
            html: `
                <div class="text-left bg-red-50 p-4 rounded-xl border border-red-100 mt-2">
                    <p class="text-sm text-red-700 font-medium mb-2">ข้อที่ยังไม่ได้ทำ:</p>
                    <div class="flex flex-wrap gap-1.5">
                        ${missing.map(n => `<span class="inline-flex items-center justify-center w-7 h-7 bg-red-200 text-red-800 text-xxs font-bold rounded-lg">${n}</span>`).join('')}
                    </div>
                </div>
                <p class="text-gray-500 text-xs mt-4">กรุณาทำข้อสอบให้ครบทั้ง ${QUESTIONS.length} ข้อก่อนส่งคำตอบ</p>
            `,
            confirmButtonText: 'กลับไปทำต่อ',
            customClass: {
                popup: 'rounded-2xl font-sans',
                title: 'text-lg font-bold text-gray-800',
                confirmButton: '!bg-primary-400 hover:!bg-primary-500 !text-white !rounded-xl !text-sm !px-6 !py-2.5 !font-semibold',
            },
            buttonsStyling: false,
        });
        return;
    }

    Swal.fire({
        icon: 'question',
        title: 'ยืนยันการส่งข้อสอบ?',
        html: `<p class="text-gray-600 text-sm">คุณตอบครบทั้ง <strong class="text-green-600">${QUESTIONS.length} ข้อ</strong> แล้ว<br>ต้องการส่งคำตอบและสิ้นสุดการสอบหรือไม่?</p>`,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-paper-plane mr-1.5"></i> ส่งข้อสอบ',
        cancelButtonText: 'ยกเลิก',
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
    const $form = $('#exam-form');
    $form.find('[data-answer-field="1"]').remove();
    QUESTIONS.forEach((q, i) => {
        const value = answers[i];
        if (value === null || value === undefined || String(value).trim() === '') return;
        $('<input>', {
            type: 'hidden',
            name: `answers[${q.id}]`,
            value: value,
            'data-answer-field': '1',
        }).appendTo($form);
    });
    Swal.fire({
        title: 'กำลังส่งข้อสอบ...',
        allowOutsideClick: false,
        allowEscapeKey: false,
        customClass: { popup: 'rounded-2xl font-sans' },
        didOpen: () => Swal.showLoading(),
    });
    $form.submit();
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
