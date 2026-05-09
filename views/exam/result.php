<?php
$isPass = $session['result'] === 'pass';
$pct = (float) $session['percent'];
$correctCount = 0;
$wrongCount = 0;
$skipCount = 0;

foreach ($answerLogs as $log) {
    if ((int)$log['is_correct'] === 1) $correctCount++;
    elseif ($log['given_answer'] === null || trim((string)$log['given_answer']) === '') $skipCount++;
    else $wrongCount++;
}

// Calculate time used
$start = new DateTime($session['started_at']);
$end = new DateTime($session['submitted_at'] ?? 'now');
$diff = $start->diff($end);
$timeUsedStr = "";
if ($diff->h > 0) $timeUsedStr .= $diff->h . " ชั่วโมง ";
if ($diff->i > 0) $timeUsedStr .= $diff->i . " นาที ";
$timeUsedStr .= $diff->s . " วินาที";
?>

<style>
/* ── Animated background ── */
.bg-mesh {
  background-color: #FFF3E8;
  background-image:
    radial-gradient(ellipse 80% 60% at 20% -10%, rgba(232,119,34,0.18) 0%, transparent 60%),
    radial-gradient(ellipse 60% 50% at 90% 110%, rgba(232,119,34,0.12) 0%, transparent 55%);
}

/* ── Confetti particles ── */
.confetti-wrap { position:fixed; inset:0; pointer-events:none; overflow:hidden; z-index:0; }
.confetti-piece {
  position:absolute; top:-20px;
  width:8px; height:8px;
  border-radius:2px;
  animation: confettiFall linear infinite;
  opacity:0;
}
@keyframes confettiFall {
  0%   { transform:translateY(-20px) rotate(0deg);   opacity:1; }
  100% { transform:translateY(110vh) rotate(720deg); opacity:0; }
}

/* ── Score ring animation ── */
.ring-svg circle.track  { stroke:#F1EFE8; }
.ring-svg circle.fill {
  stroke:#E87722;
  stroke-dasharray: 283;
  stroke-dashoffset: 283;
  stroke-linecap:round;
  transition: stroke-dashoffset 1.6s cubic-bezier(.4,0,.2,1);
  transform-origin: center;
  transform: rotate(-90deg);
}
.ring-svg.fail circle.fill { stroke:#f87171; }

/* ── Entry animations ── */
@keyframes fadeUp {
  from { opacity:0; transform:translateY(20px); }
  to   { opacity:1; transform:translateY(0); }
}
@keyframes scaleIn {
  from { opacity:0; transform:scale(0.7); }
  to   { opacity:1; transform:scale(1); }
}
.anim-fade-up   { animation: fadeUp   0.5s ease both; }
.anim-scale-in  { animation: scaleIn  0.5s cubic-bezier(.34,1.56,.64,1) both; }

.d-1 { animation-delay:.10s; }
.d-2 { animation-delay:.20s; }
.d-3 { animation-delay:.30s; }
.d-4 { animation-delay:.45s; }
.d-5 { animation-delay:.60s; }
.d-6 { animation-delay:.75s; }

.shadow-card-lg { box-shadow: 0 8px 32px rgba(0,0,0,0.12); }
.shadow-cert { box-shadow: 0 20px 60px rgba(0,0,0,0.18); }

/* ── Certificate styles ── */
.cert-wrapper {
  font-family: 'Sarabun', serif;
  background: #fff;
  position: relative;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0,0,0,0.18);
}
.cert-border-outer {
  position:absolute; inset:10px;
  border:2px solid #E87722;
  border-radius:4px;
  pointer-events:none;
  z-index:2;
}
.cert-corner {
  position:absolute; width:36px; height:36px;
  z-index:3;
}
.cert-corner.tl { top:6px; left:6px;   border-top:3px solid #E87722; border-left:3px solid #E87722;  }
.cert-corner.tr { top:6px; right:6px;  border-top:3px solid #E87722; border-right:3px solid #E87722; }
.cert-corner.bl { bottom:6px; left:6px;  border-bottom:3px solid #E87722; border-left:3px solid #E87722;  }
.cert-corner.br { bottom:6px; right:6px; border-bottom:3px solid #E87722; border-right:3px solid #E87722; }

.tab-btn { transition: all .15s; border-bottom:2px solid transparent; }
.tab-btn.active { border-color:#E87722; color:#E87722; }

.cert-shimmer {
  background: linear-gradient(90deg, transparent 30%, rgba(232,119,34,0.08) 50%, transparent 70%);
  background-size:200% 100%;
  animation: shimmer 3s ease-in-out infinite;
  position:absolute; inset:0; pointer-events:none; z-index:4; border-radius:inherit;
}
@keyframes shimmer {
  0%   { background-position:-200% 0; }
  100% { background-position: 200% 0; }
}

@media print {
  body > *:not(#cert-section) { display:none !important; }
  #cert-section { display:block !important; box-shadow:none; }
}
</style>

<!-- Confetti (pass only) -->
<div class="confetti-wrap" id="confetti-wrap"></div>

<div class="w-full max-w-2xl relative z-10 pt-4">

    <!-- Icon + headline -->
    <div class="text-center mb-8 anim-scale-in">
      <div id="result-icon"
        class="w-24 h-24 rounded-full mx-auto mb-5 flex items-center justify-center shadow-cert transition-all duration-700 <?= $isPass ? 'bg-gradient-to-br from-primary-400 to-primary-600' : 'bg-gradient-to-br from-red-400 to-red-600' ?>">
        <i class="text-4xl text-white fas <?= $isPass ? 'fa-trophy' : 'fa-rotate-right' ?>"></i>
      </div>
      <h1 class="text-3xl font-bold mb-2 <?= $isPass ? 'text-gray-800' : 'text-gray-700' ?>">
          <?= $isPass ? 'ยินดีด้วย! 🎉' : 'ไม่ผ่านการทดสอบ' ?>
      </h1>
      <p class="text-sm text-gray-500">
          <?= $isPass ? 'คุณผ่านการทดสอบเรียบร้อยแล้ว สามารถรับเกียรติบัตรได้ทันที' : 'คุณยังไม่ผ่านเกณฑ์ ' . (float)$project['pass_score'] . '% ทบทวนและลองใหม่ได้เลย' ?>
      </p>
    </div>

    <!-- Score ring card -->
    <div class="bg-white rounded-2xl shadow-card-lg p-6 mb-4 anim-fade-up d-2">
      <div class="flex flex-col sm:flex-row items-center gap-8">
        <!-- Ring -->
        <div class="relative flex-shrink-0 anim-scale-in d-3">
          <svg class="ring-svg <?= $isPass ? '' : 'fail' ?>" width="120" height="120" viewBox="0 0 120 120">
            <circle class="track" cx="60" cy="60" r="45" fill="none" stroke-width="9"/>
            <circle class="fill" id="ring-fill" cx="60" cy="60" r="45" fill="none" stroke-width="9" style="stroke-dashoffset: 283;"/>
          </svg>
          <div class="absolute inset-0 flex flex-col items-center justify-center">
            <span id="score-pct-text" class="text-2xl font-bold <?= $isPass ? 'text-primary-500' : 'text-red-500' ?> leading-none">0%</span>
            <span class="text-[10px] text-gray-400 mt-0.5 uppercase tracking-widest font-bold">คะแนน</span>
          </div>
        </div>

        <!-- Breakdown -->
        <div class="flex-1 w-full space-y-3">
          <div class="anim-fade-up d-3">
            <div class="flex items-center justify-between mb-1">
              <span class="text-xs text-gray-500">คะแนนที่ได้</span>
              <span class="text-sm font-semibold text-gray-800"><?= (float)$session['score'] ?> / <?= (float)$session['total_score'] ?></span>
            </div>
            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
              <div id="score-bar" class="h-full rounded-full transition-all duration-[1.4s] ease-out <?= $isPass ? 'bg-primary-400' : 'bg-red-400' ?>" style="width:0%"></div>
            </div>
          </div>

          <div class="grid grid-cols-3 gap-3 anim-fade-up d-4">
            <div class="text-center p-2.5 rounded-xl bg-gray-50">
              <p class="text-lg font-bold text-gray-800"><?= $correctCount ?></p>
              <p class="text-[10px] text-gray-400 mt-0.5">ตอบถูก</p>
            </div>
            <div class="text-center p-2.5 rounded-xl bg-gray-50">
              <p class="text-lg font-bold text-gray-800"><?= $wrongCount ?></p>
              <p class="text-[10px] text-gray-400 mt-0.5">ตอบผิด</p>
            </div>
            <div class="text-center p-2.5 rounded-xl bg-gray-50">
              <p class="text-lg font-bold text-gray-800"><?= $skipCount ?></p>
              <p class="text-[10px] text-gray-400 mt-0.5">ไม่ได้ตอบ</p>
            </div>
          </div>

          <!-- Pass threshold bar -->
          <div class="anim-fade-up d-4">
            <div class="flex items-center justify-between mb-1">
              <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">เกณฑ์ผ่าน <?= (float)$project['pass_score'] ?>%</span>
              <span class="text-[10px] font-semibold px-2.5 py-0.5 rounded-full <?= $isPass ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' ?>">
                  <?= $isPass ? '✓ ผ่าน' : '✕ ไม่ผ่าน' ?>
              </span>
            </div>
            <div class="relative h-2 bg-gray-100 rounded-full overflow-visible mt-1">
              <div class="absolute top-1/2 -translate-y-1/2 -translate-x-1/2 w-0.5 h-4 bg-red-400 rounded z-10" style="left:<?= (float)$project['pass_score'] ?>%"></div>
              <div id="pass-bar" class="h-full rounded-full transition-all duration-[1.4s] ease-out <?= $isPass ? 'bg-primary-400' : 'bg-red-400' ?>" style="width:0%"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Time info -->
      <div class="flex items-center justify-between pt-4 mt-4 border-t border-gray-50 anim-fade-up d-5">
        <div class="flex items-center gap-2 text-xs text-gray-400">
          <i class="fas fa-clock text-gray-300"></i>
          <span>ใช้เวลา: <strong class="text-gray-600"><?= $timeUsedStr ?></strong></span>
        </div>
        <div class="flex items-center gap-2 text-xs text-gray-400">
          <i class="fas fa-calendar text-gray-300"></i>
          <span class="text-gray-600"><?= date('j M Y', strtotime($session['submitted_at'] ?? 'now')) ?></span>
        </div>
      </div>
    </div>

    <!-- ── TABS ── -->
    <div class="bg-white rounded-2xl shadow-card-lg overflow-hidden anim-fade-up d-5">
      <!-- Tab bar -->
      <div class="flex border-b border-gray-100">
        <button class="tab-btn active flex-1 py-3 text-sm font-medium text-gray-600" onclick="switchTab('answers', this)">
          <i class="fas fa-list-check mr-1.5 text-xs"></i>เฉลยคำตอบ
        </button>
        <?php if ($isPass): ?>
        <button class="tab-btn flex-1 py-3 text-sm font-medium text-gray-400" onclick="switchTab('cert', this)">
          <i class="fas fa-award mr-1.5 text-xs"></i>เกียรติบัตรของคุณ
        </button>
        <?php endif; ?>
      </div>

      <!-- ── CONTENT ── -->
      <div class="p-0">
        <!-- ── ANSWERS TAB ── -->
        <div id="tab-answers" class="p-0 divide-y divide-gray-50">
          <?php foreach ($answerLogs as $index => $log): ?>
          <div class="answer-row flex items-start gap-3 px-5 py-4 transition-colors hover:bg-gray-50">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5 <?= (int)$log['is_correct'] === 1 ? 'bg-green-100' : (empty($log['given_answer']) ? 'bg-gray-100' : 'bg-red-100') ?>">
              <i class="fas <?= (int)$log['is_correct'] === 1 ? 'fa-check text-green-600' : (empty($log['given_answer']) ? 'fa-minus text-gray-400' : 'fa-xmark text-red-500') ?> text-xs"></i>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[10px] text-gray-400 mb-0.5">ข้อที่ <?= $index + 1 ?></p>
              <p class="text-sm text-gray-700 mb-1.5 leading-snug font-medium"><?= e($log['question_text']) ?></p>
              <div class="flex flex-wrap gap-2 items-center">
                <span class="text-xs px-2 py-0.5 rounded-md <?= (int)$log['is_correct'] === 1 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' ?> font-bold">
                  <?= empty($log['given_answer']) ? 'ไม่ได้ตอบ' : 'คำตอบของคุณ: ' . e($log['given_answer']) ?>
                </span>
                <?php if ((int)$log['is_correct'] === 0): ?>
                <span class="text-xs px-2 py-0.5 rounded-md bg-blue-50 text-blue-600 font-bold">
                  คำตอบที่ถูก: <?= e($log['correct_answer']) ?>
                </span>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- ── CERTIFICATE TAB ── -->
        <?php if ($isPass): ?>
        <div id="tab-cert" class="hidden p-6">
          <div id="cert-section" class="cert-wrapper rounded-xl mb-6 anim-scale-in" style="aspect-ratio:1.414/1; min-height:280px;">
            <div class="cert-border-outer"></div>
            <div class="cert-corner tl"></div><div class="cert-corner tr"></div>
            <div class="cert-corner bl"></div><div class="cert-corner br"></div>
            <div class="cert-shimmer"></div>
            <div class="relative z-5 flex flex-col items-center justify-center h-full px-10 text-center py-6" style="background: radial-gradient(circle at 50% 0%, rgba(232,119,34,0.05) 0%, transparent 70%), #fff;">
              <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-primary-400 flex items-center justify-center">
                  <i class="fas fa-award text-white text-sm"></i>
                </div>
                <span class="text-xs font-bold text-gray-500 tracking-wide"><?= e($project['organizer'] ?: 'มหาวิทยาลัยราชภัฏร้อยเอ็ด') ?></span>
              </div>
              <p class="text-[10px] uppercase tracking-[0.2em] text-primary-400 font-bold mb-1">เกียรติบัตรฉบับนี้ให้ไว้เพื่อแสดงว่า</p>
              <p class="font-bold text-gray-900 mb-2 leading-tight" style="font-size:clamp(16px,4vw,24px)">
                <?= e($participant['title'] . $participant['first_name'] . ' ' . $participant['last_name']) ?>
              </p>
              <p class="text-xs text-gray-400 mb-1">ได้ผ่านการทดสอบระบบออนไลน์ในหลักสูตร</p>
              <p class="font-bold text-primary-600 mb-4" style="font-size:clamp(12px,3vw,16px)">
                <?= e($project['name']) ?>
              </p>
              <div class="flex items-center justify-between w-full mt-auto">
                <div class="text-left">
                  <p class="text-[9px] text-gray-400">เลขที่เกียรติบัตร</p>
                  <p class="text-[10px] font-mono font-bold text-gray-700"><?= e($certificate['cert_number'] ?? 'PENDING') ?></p>
                </div>
                <div class="w-12 h-12 bg-gray-50 rounded-lg flex items-center justify-center border border-gray-100">
                  <i class="fas fa-qrcode text-gray-300 text-xl"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <a href="<?= e(BASE_URL) ?>/public/download-cert.php?token=<?= e($certificate['verify_token'] ?? '') ?>" 
               class="flex items-center justify-center gap-2 py-3.5 bg-primary-400 hover:bg-primary-500 text-white font-bold rounded-xl transition-all text-sm shadow-lg shadow-primary-500/20">
              <i class="fas fa-download"></i> ดาวน์โหลดเกียรติบัตร
            </a>
            <button onclick="shareCert()" class="flex items-center justify-center gap-2 py-3.5 bg-white border-2 border-gray-100 hover:border-primary-100 text-gray-600 font-bold rounded-xl transition-all text-sm">
              <i class="fas fa-share-nodes text-primary-400"></i> แชร์ลิงก์ตรวจสอบ
            </button>
          </div>
        </div>
        <?php else: ?>
        <div class="p-8 border-t border-gray-50 bg-gray-50/30">
          <div class="flex flex-col items-center text-center">
            <div class="w-14 h-14 bg-red-50 rounded-full flex items-center justify-center mb-4">
              <i class="fas fa-circle-exclamation text-red-400 text-xl"></i>
            </div>
            <p class="text-gray-800 font-bold mb-1">ยังไม่ผ่านเกณฑ์การทดสอบ</p>
            <p class="text-xs text-gray-400 max-w-[240px]">คุณต้องการคะแนนอย่างน้อย <?= (float)$project['pass_score'] ?>% เพื่อรับเกียรติบัตร สามารถทบทวนคำตอบด้านบนและลองใหม่อีกครั้ง</p>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- ── FOOTER ACTIONS ── -->
    <div class="mt-8 flex flex-col gap-3 anim-fade-up d-6">
        <?php if (!$isPass): ?>
        <a href="<?= e(BASE_URL) ?>/public/exam.php?project=<?= e($project['code']) ?>" 
           class="flex items-center justify-center gap-2 w-full py-4 bg-gray-900 hover:bg-black text-white font-bold rounded-2xl transition-all shadow-xl active:scale-95 no-underline">
           <i class="fas fa-rotate-right"></i> ทำแบบทดสอบอีกครั้ง
        </a>
        <?php endif; ?>
        <a href="<?= e(BASE_URL) ?>/" class="flex items-center justify-center gap-2 w-full py-4 bg-white border-2 border-gray-100 hover:border-gray-300 text-gray-500 font-bold rounded-2xl transition-all no-underline">
            <i class="fas fa-home"></i> กลับสู่หน้าหลัก
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Animate score ring
    setTimeout(() => {
        const circumference = 283;
        const pct = <?= $pct ?>;
        const offset = circumference - (pct / 100) * circumference;
        const fill = document.getElementById('ring-fill');
        if(fill) fill.style.strokeDashoffset = offset;
        
        const bar = document.getElementById('score-bar');
        if(bar) bar.style.width = pct + '%';

        const passBar = document.getElementById('pass-bar');
        if(passBar) passBar.style.width = pct + '%';
        
        // Count up text
        let count = 0;
        const target = Math.round(pct);
        if (target > 0) {
            const counter = setInterval(() => {
                count++;
                document.getElementById('score-pct-text').textContent = count + '%';
                if (count >= target) clearInterval(counter);
            }, 20);
        } else {
            document.getElementById('score-pct-text').textContent = '0%';
        }
    }, 300);

    // Confetti for pass
    <?php if ($isPass): ?>
    const wrap = document.getElementById('confetti-wrap');
    const colors = ['#E87722','#FAC775','#EF9F27','#fff','#C4601A'];
    for (let i = 0; i < 50; i++) {
        const el = document.createElement('div');
        el.className = 'confetti-piece';
        const size = Math.random() * 8 + 4;
        el.style.cssText = `
            left:${Math.random()*100}%;
            width:${size}px; height:${size}px;
            background:${colors[Math.floor(Math.random()*colors.length)]};
            animation-duration:${Math.random()*3+2}s;
            animation-delay:${Math.random()*2}s;
        `;
        wrap.appendChild(el);
    }
    <?php endif; ?>
});

function shareCert() {
    const url = '<?= e(BASE_URL) ?>/public/verify.php?token=<?= e($certificate['verify_token'] ?? '') ?>';
    navigator.clipboard.writeText(url).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'คัดลอกลิงก์ตรวจสอบแล้ว',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    });
}
</script>
