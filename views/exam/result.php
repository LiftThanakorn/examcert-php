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
if ($diff->h > 0) $timeUsedStr .= $diff->h . " ชม. ";
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
</style>

<!-- Confetti (pass only) -->
<div class="confetti-wrap" id="confetti-wrap"></div>

<div class="w-full max-w-2xl relative z-10 pt-4 pb-20">

    <!-- Icon + headline -->
    <div class="text-center mb-8 anim-scale-in">
      <div id="result-icon"
        class="w-24 h-24 rounded-full mx-auto mb-5 flex items-center justify-center shadow-lg transition-all duration-700 <?= $isPass ? 'bg-primary-500' : 'bg-red-500' ?>">
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
    <div class="bg-white rounded-2xl shadow-card-lg p-6 mb-6 anim-fade-up d-2">
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
            <div class="text-center p-2.5 rounded-xl bg-gray-50 border border-gray-100">
              <p class="text-lg font-bold text-gray-800"><?= $correctCount ?></p>
              <p class="text-[10px] text-gray-400 mt-0.5">ตอบถูก</p>
            </div>
            <div class="text-center p-2.5 rounded-xl bg-gray-50 border border-gray-100">
              <p class="text-lg font-bold text-gray-800"><?= $wrongCount ?></p>
              <p class="text-[10px] text-gray-400 mt-0.5">ตอบผิด</p>
            </div>
            <div class="text-center p-2.5 rounded-xl bg-gray-50 border border-gray-100">
              <p class="text-lg font-bold text-gray-800"><?= $skipCount ?></p>
              <p class="text-[10px] text-gray-400 mt-0.5">ไม่ตอบ</p>
            </div>
          </div>

          <!-- Pass threshold bar -->
          <div class="anim-fade-up d-4">
            <div class="flex items-center justify-between mb-1">
              <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">เกณฑ์ผ่าน <?= (float)$project['pass_score'] ?>%</span>
              <span class="text-[10px] font-semibold px-2.5 py-0.5 rounded-full <?= $isPass ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' ?>">
                  <?= $isPass ? '✓ ผ่านเกณฑ์' : '✕ ไม่ผ่านเกณฑ์' ?>
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

    <!-- ── ACTION CARD ── -->
    <div class="bg-white rounded-2xl shadow-card-lg overflow-hidden anim-fade-up d-5">
      <div class="p-8">
        <?php if ($isPass): ?>
          <!-- Success State -->
          <div class="flex flex-col items-center text-center">
            <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mb-4">
              <i class="fas fa-award text-green-500 text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">ยินดีด้วย! คุณผ่านการทดสอบ</h3>
            <p class="text-sm text-gray-400 mb-8 max-w-[320px]">ระบบได้ออกเกียรติบัตรให้คุณเรียบร้อยแล้ว สามารถดาวน์โหลดไฟล์ PDF ได้จากปุ่มด้านล่างนี้</p>
            
            <div class="flex flex-col gap-3 w-full">
              <button id="btn-download" onclick="triggerDownload()" 
                 class="flex items-center justify-center gap-3 py-4 bg-primary-400 hover:bg-primary-500 text-white font-bold rounded-2xl transition-all text-sm shadow-xl shadow-primary-500/25 border-none cursor-pointer group">
                <i class="fas fa-file-pdf text-lg group-hover:scale-110 transition-transform"></i>
                <span>ดาวน์โหลดเกียรติบัตร (PDF)</span>
              </button>

              <div id="fallback-container" class="hidden animate-fade-in">
                <p class="text-[10px] text-gray-400 mb-1">หากการดาวน์โหลดไม่เริ่มอัตโนมัติ:</p>
                <a id="fallback-link" href="#" target="_blank" class="text-xs font-bold text-primary-500 hover:underline">คลิกที่นี่เพื่อดาวน์โหลดโดยตรง</a>
              </div>

              <button onclick="shareCert()" class="flex items-center justify-center gap-2 py-4 bg-white border-2 border-gray-100 hover:border-primary-100 text-gray-600 font-bold rounded-2xl transition-all text-sm mt-2">
                <i class="fas fa-share-nodes text-primary-400"></i> แชร์ลิงก์ตรวจสอบ
              </button>
            </div>
          </div>

          <!-- Hidden Iframe for download -->
          <iframe id="download-iframe" name="cert_iframe" src="about:blank" style="width:1px; height:1px; border:none; opacity:0; position:absolute;"></iframe>

        <?php else: ?>
          <!-- Fail State -->
          <div class="py-4 flex flex-col items-center text-center">
            <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mb-4">
              <i class="fas fa-circle-exclamation text-red-400 text-2xl"></i>
            </div>
            <p class="text-gray-800 font-bold text-lg mb-1">ขออภัย คุณยังไม่ผ่านเกณฑ์</p>
            <p class="text-sm text-gray-400 max-w-[280px]">คุณสามารถเริ่มทำแบบทดสอบใหม่อีกครั้งเพื่อปรับปรุงคะแนนและรับเกียรติบัตร</p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- ── NAVIGATION ── -->
    <div class="mt-8 flex flex-col gap-3 anim-fade-up d-6">
        <?php if (!$isPass): ?>
        <a href="<?= e(BASE_URL) ?>/public/exam.php?project=<?= e($project['code']) ?>" 
           class="flex items-center justify-center gap-2 w-full py-4 bg-gray-900 hover:bg-black text-white font-bold rounded-2xl transition-all shadow-xl active:scale-95 no-underline">
           <i class="fas fa-rotate-right"></i> เริ่มทำแบบทดสอบใหม่
        </a>
        <?php endif; ?>
        <a href="<?= e(BASE_URL) ?>/" class="flex items-center justify-center gap-2 w-full py-4 bg-white border-2 border-gray-100 hover:border-gray-200 text-gray-500 font-bold rounded-2xl transition-all no-underline">
            <i class="fas fa-house"></i> กลับสู่หน้าหลัก
        </a>
    </div>

    <div class="mt-8 text-center opacity-60">
        <p class="text-[10px] font-bold text-slate-500 leading-relaxed">
            <i class="fa-solid fa-code mr-1"></i>
            ระบบออกข้อสอบพร้อมรับเกียรติบัตร พัฒนาโดยนายธนากร อินทพันธ์ บุคลากร งานบริหารทรัพยากรบุคคลและนิติการ มหาวิทยาลัยราชภัฏร้อยเอ็ด
        </p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Animate score ring and progress bars
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
        
        // Count up text effect
        let count = 0;
        const target = Math.round(pct);
        if (target > 0) {
            const counter = setInterval(() => {
                count++;
                if (document.getElementById('score-pct-text')) {
                    document.getElementById('score-pct-text').textContent = count + '%';
                }
                if (count >= target) clearInterval(counter);
            }, 15);
        } else {
            if (document.getElementById('score-pct-text')) {
                document.getElementById('score-pct-text').textContent = '0%';
            }
        }
    }, 400);

    // Confetti explosion for success
    <?php if ($isPass): ?>
    const wrap = document.getElementById('confetti-wrap');
    const colors = ['#E87722', '#FAC775', '#EF9F27', '#ffffff', '#C4601A'];
    for (let i = 0; i < 60; i++) {
        const el = document.createElement('div');
        el.className = 'confetti-piece';
        const size = Math.random() * 8 + 4;
        el.style.cssText = `
            left:${Math.random() * 100}%;
            width:${size}px; height:${size}px;
            background:${colors[Math.floor(Math.random() * colors.length)]};
            animation-duration:${Math.random() * 3 + 2}s;
            animation-delay:${Math.random() * 2}s;
        `;
        wrap.appendChild(el);
    }
    <?php endif; ?>
});

function triggerDownload() {
    const btn = document.getElementById('btn-download');
    const iframe = document.getElementById('download-iframe');
    const fallback = document.getElementById('fallback-container');
    const fallbackLink = document.getElementById('fallback-link');
    
    if (!btn || !iframe) return;

    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> กำลังเตรียมไฟล์ PDF...';
    btn.disabled = true;

    // Load the dedicated render page into the hidden iframe (same as verify.php)
    const renderUrl = "<?= e(BASE_URL . '/public/render-cert.php?token=' . ($certificate['verify_token'] ?? '')) ?>&download=1";
    iframe.src = renderUrl;
    
    // Setup fallback link
    fallbackLink.href = renderUrl;
    setTimeout(() => {
        if (btn.disabled) {
            if (fallback) fallback.classList.remove('hidden');
        }
    }, 5000);

    // Listen for completion (from render-cert.php)
    window.onmessage = function(e) {
        if (e.data === 'download_complete') {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
            if (fallback) fallback.classList.add('hidden');
        }
    };
}

function shareCert() {
    const url = '<?= e(BASE_URL) ?>/public/verify.php?token=<?= e($certificate['verify_token'] ?? '') ?>';
    navigator.clipboard.writeText(url).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'คัดลอกลิงก์ตรวจสอบแล้ว',
            text: 'คุณสามารถส่งลิงก์นี้ให้ผู้อื่นเพื่อตรวจสอบเกียรติบัตรได้',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            customClass: { popup: 'rounded-xl font-sans text-sm' }
        });
    });
}
</script>
