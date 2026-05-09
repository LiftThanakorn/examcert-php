<?php
/** @var string $token */
/** @var string $mode */
/** @var array|null $certificate */
?>

<style>
.bg-mesh-initial {
  background-color:#F9F8F6;
  background-image:
    radial-gradient(ellipse 70% 55% at 15% -5%,  rgba(232,119,34,0.08) 0%,transparent 60%),
    radial-gradient(ellipse 55% 45% at 88% 105%, rgba(232,119,34,0.05) 0%,transparent 55%);
}
.bg-mesh-valid {
  background-color:#F9F8F6;
  background-image:
    radial-gradient(ellipse 70% 55% at 15% -5%,  rgba(232,119,34,0.13) 0%,transparent 60%),
    radial-gradient(ellipse 55% 45% at 88% 105%, rgba(232,119,34,0.09) 0%,transparent 55%);
}
.bg-mesh-invalid {
  background-color:#F9F8F6;
  background-image:
    radial-gradient(ellipse 70% 55% at 15% -5%,  rgba(239,68,68,0.09) 0%,transparent 60%),
    radial-gradient(ellipse 55% 45% at 88% 105%, rgba(239,68,68,0.06) 0%,transparent 55%);
}
.bg-mesh-revoked {
  background-color:#F9F8F6;
  background-image:
    radial-gradient(ellipse 70% 55% at 15% -5%,  rgba(107,114,128,0.10) 0%,transparent 60%),
    radial-gradient(ellipse 55% 45% at 88% 105%, rgba(107,114,128,0.07) 0%,transparent 55%);
}

/* ── Animations ── */
@keyframes fadeUp  { from{opacity:0;transform:translateY(18px)} to{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { from{opacity:0;transform:scale(.82)}       to{opacity:1;transform:scale(1)}  }
@keyframes spin    { to{transform:rotate(360deg)} }
@keyframes checkDraw {
  from { stroke-dashoffset: 60; }
  to   { stroke-dashoffset: 0;  }
}
@keyframes ripple  {
  0%   { transform:scale(1);   opacity:0.55; }
  100% { transform:scale(2.6); opacity:0; }
}
@keyframes shimmer {
  0%   { background-position:-200% 0; }
  100% { background-position: 200% 0; }
}

.anim-fade-up  { animation:fadeUp  0.45s ease both; }
.anim-scale-in { animation:scaleIn 0.45s cubic-bezier(.34,1.46,.64,1) both; }
.d1{animation-delay:.08s} .d2{animation-delay:.16s} .d3{animation-delay:.24s}
.d4{animation-delay:.32s} .d5{animation-delay:.42s} .d6{animation-delay:.54s}

.spinner { animation:spin 0.9s linear infinite; }

/* check SVG */
.check-path {
  stroke-dasharray: 60;
  stroke-dashoffset: 60;
  animation: checkDraw 0.6s ease 0.3s both;
}

/* ripple rings (valid state) */
.ripple-ring {
  position:absolute; inset:0; border-radius:50%;
  border:2px solid rgba(232,119,34,0.35);
  animation:ripple 2.5s ease-out infinite;
}
.ripple-ring:nth-child(2) { animation-delay:0.8s; }
.ripple-ring:nth-child(3) { animation-delay:1.6s; }

/* cert shimmer */
.cert-shimmer {
  background:linear-gradient(90deg,transparent 30%,rgba(232,119,34,0.07) 50%,transparent 70%);
  background-size:200% 100%;
  animation:shimmer 3.5s ease-in-out infinite;
  position:absolute; inset:0; pointer-events:none; z-index:4; border-radius:inherit;
}

/* cert borders & corners */
.cert-frame { position:absolute; inset:10px; border:1.5px solid #E87722; border-radius:3px; pointer-events:none; z-index:2; }
.cert-frame-inner { position:absolute; inset:14px; border:0.5px solid rgba(232,119,34,0.28); border-radius:2px; pointer-events:none; z-index:2; }
.cert-corner { position:absolute; width:28px; height:28px; z-index:3; }
.cert-corner.tl { top:6px;    left:6px;   border-top:2.5px solid #E87722; border-left:2.5px solid #E87722; }
.cert-corner.tr { top:6px;    right:6px;  border-top:2.5px solid #E87722; border-right:2.5px solid #E87722; }
.cert-corner.bl { bottom:6px; left:6px;   border-bottom:2.5px solid #E87722; border-left:2.5px solid #E87722; }
.cert-corner.br { bottom:6px; right:6px;  border-bottom:2.5px solid #E87722; border-right:2.5px solid #E87722; }
.cert-watermark {
  position:absolute; inset:0; display:flex; align-items:center; justify-content:center;
  pointer-events:none; z-index:1; opacity:0.032;
  font-size:90px; color:#E87722; font-weight:700; transform:rotate(-28deg);
}

/* REVOKED stamp */
.stamp-revoked {
  position:absolute; top:50%; left:50%; transform:translate(-50%,-50%) rotate(-22deg);
  border:4px solid #dc2626; color:#dc2626; padding:6px 18px;
  font-size:clamp(22px,5vw,38px); font-weight:800;
  letter-spacing:.15em; text-transform:uppercase;
  opacity:0.22; pointer-events:none; z-index:10; white-space:nowrap;
  font-family:'Noto Serif Thai',serif;
}

/* token input */
.token-input:focus { outline:none; border-color:#E87722; box-shadow:0 0 0 3px rgba(232,119,34,0.18); }

/* info row */
.info-row { border-bottom:1px solid #F1EFE8; }
.info-row:last-child { border-bottom:none; }

/* scan line animation on QR */
@keyframes scan { 0%,100%{top:10%} 50%{top:85%} }
.scan-line { animation:scan 2.5s ease-in-out infinite; }

/* Override body mesh based on mode */
body { transition: background-color 0.5s ease; min-height: 100vh; display: flex; flex-direction: column; }
<?php if ($mode === 'valid'): ?>
body { background-color: #F9F8F6 !important; background-image: radial-gradient(ellipse 70% 55% at 15% -5%, rgba(232,119,34,0.13) 0%,transparent 60%), radial-gradient(ellipse 55% 45% at 88% 105%, rgba(232,119,34,0.09) 0%,transparent 55%) !important; }
<?php elseif ($mode === 'invalid'): ?>
body { background-color: #F9F8F6 !important; background-image: radial-gradient(ellipse 70% 55% at 15% -5%, rgba(239,68,68,0.09) 0%,transparent 60%), radial-gradient(ellipse 55% 45% at 88% 105%, rgba(239,68,68,0.06) 0%,transparent 55%) !important; }
<?php elseif ($mode === 'revoked'): ?>
body { background-color: #F9F8F6 !important; background-image: radial-gradient(ellipse 70% 55% at 15% -5%, rgba(107,114,128,0.10) 0%,transparent 60%), radial-gradient(ellipse 55% 45% at 88% 105%, rgba(107,114,128,0.07) 0%,transparent 55%) !important; }
<?php endif; ?>
</style>

<!-- ── TOP NAV ── -->
<nav class="bg-white/80 backdrop-blur border-b border-white/60 px-6 py-3.5 flex items-center justify-between sticky top-0 z-30">
  <div class="flex items-center gap-2.5">
    <div class="w-8 h-8 bg-primary-400 rounded-lg flex items-center justify-center shadow-orange">
      <i class="fas fa-award text-white text-sm"></i>
    </div>
    <div>
      <p class="text-sm font-semibold text-gray-800 leading-tight"><?= e(APP_NAME) ?></p>
      <p class="text-xxs text-gray-400">ระบบตรวจสอบเกียรติบัตร</p>
    </div>
  </div>
  <a href="<?= e(BASE_URL) ?>" class="text-xs text-primary-400 hover:text-primary-500 font-medium transition-colors">
    <i class="fas fa-external-link mr-1 text-xxs"></i>หน้าหลัก
  </a>
</nav>

<main class="flex-1 flex flex-col items-center justify-start pt-10 pb-20 px-4">
  <div class="w-full max-w-2xl">

    <!-- ══ INITIAL STATE ══ -->
    <?php if ($mode === 'initial'): ?>
    <div id="state-initial" class="space-y-8 py-10">
      <div class="text-center anim-fade-up">
        <div class="w-24 h-24 mx-auto mb-6 bg-white rounded-3xl shadow-card-lg flex items-center justify-center relative overflow-hidden group">
          <div class="absolute inset-0 bg-gradient-to-br from-primary-50 to-primary-100/50 opacity-0 group-hover:opacity-100 transition-opacity"></div>
          <i class="fas fa-search text-4xl text-primary-400 relative z-10 transition-transform group-hover:scale-110"></i>
        </div>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-3">ตรวจสอบเกียรติบัตร</h1>
        <p class="text-gray-500 max-w-sm mx-auto leading-relaxed">
          กรุณากรอก Verify Token หรือเลขที่เกียรติบัตร <br>เพื่อตรวจสอบความถูกต้องของเอกสาร
        </p>
      </div>

      <div class="bg-white/80 backdrop-blur-xl rounded-[2.5rem] border border-white p-8 shadow-card-lg anim-fade-up d2">
        <div class="space-y-4">
          <div class="relative">
            <label class="block text-xxs font-bold text-primary-400 uppercase tracking-[0.2em] mb-2 ml-1">กรอกข้อมูลเพื่อตรวจสอบ</label>
            <input id="initial-token-input" type="text" placeholder="วาง Verify Token หรือเลขที่เกียรติบัตรที่นี่..."
              class="token-input w-full h-14 px-5 text-base font-mono border border-gray-100 rounded-2xl bg-white/50 backdrop-blur transition-all focus:bg-white">
          </div>
          <button onclick="handleInitialSearch()"
            class="w-full h-14 bg-primary-400 hover:bg-primary-500 text-white font-bold rounded-2xl transition-all shadow-lg shadow-primary-200 active:scale-[0.98] flex items-center justify-center gap-3">
            <i class="fas fa-shield-check text-lg"></i>
            ตรวจสอบข้อมูลเกียรติบัตร
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 anim-fade-up d3">
        <div class="p-4 bg-white/40 rounded-2xl border border-white/60 flex items-center gap-4">
          <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center text-green-600">
            <i class="fas fa-qrcode"></i>
          </div>
          <div>
            <p class="text-xs font-bold text-gray-800">Scan QR Code</p>
            <p class="text-[10px] text-gray-500">สแกนเพื่อตรวจสอบทันที</p>
          </div>
        </div>
        <div class="p-4 bg-white/40 rounded-2xl border border-white/60 flex items-center gap-4">
          <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
            <i class="fas fa-link"></i>
          </div>
          <div>
            <p class="text-xs font-bold text-gray-800">Verify Link</p>
            <p class="text-[10px] text-gray-500">ตรวจสอบผ่านลิงก์อ้างอิง</p>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- ══ VALID STATE ══ -->
    <?php if ($mode === 'valid'): ?>
    <div id="state-valid" class="space-y-4">
      <div class="text-center anim-scale-in">
        <div class="relative inline-flex items-center justify-center w-24 h-24 mb-4">
          <div class="ripple-ring"></div>
          <div class="ripple-ring"></div>
          <div class="ripple-ring"></div>
          <div class="relative w-20 h-20 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center shadow-cert z-10">
            <svg width="38" height="38" viewBox="0 0 38 38" fill="none">
              <path class="check-path" d="M8 19 L16 27 L30 11" stroke="white" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
        </div>
        <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-green-50 border border-green-200 rounded-full mb-3 anim-fade-up d1">
          <span class="w-2 h-2 rounded-full bg-green-500"></span>
          <span class="text-xs font-semibold text-green-700">เกียรติบัตรถูกต้อง · Verified</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-800 mb-1 anim-fade-up d2">เกียรติบัตรนี้แท้จริง</h1>
        <p class="text-sm text-gray-400 anim-fade-up d3">ตรวจสอบ ณ <span id="verify-time-valid"><?= date('j') . ' ' . (['','ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'][date('n')]) . ' ' . (date('Y')+543) . ' · ' . date('H:i') ?> น.</span></p>
      </div>

      <div class="anim-fade-up d3">
        <p class="text-xxs text-gray-400 uppercase tracking-widest mb-2.5 text-center">ตัวอย่างเกียรติบัตร</p>
        <div class="cert-wrapper bg-white rounded-2xl shadow-cert overflow-hidden relative" style="aspect-ratio:1.414/1; min-height:240px;">
          <div class="cert-frame"></div>
          <div class="cert-frame-inner"></div>
          <div class="cert-corner tl"></div>
          <div class="cert-corner tr"></div>
          <div class="cert-corner bl"></div>
          <div class="cert-corner br"></div>
          <div class="cert-shimmer"></div>
          <div class="cert-watermark"><i class="fas fa-award"></i></div>
          <div class="absolute inset-0 z-0" style="background:radial-gradient(ellipse 70% 50% at 50% 0%,rgba(232,119,34,0.06) 0%,transparent 65%),#fff;"></div>
          <div class="relative z-5 flex flex-col items-center justify-center h-full px-10 text-center py-5">
            <div class="flex items-center gap-2 mb-2.5">
              <div class="w-7 h-7 rounded-lg bg-primary-400 flex items-center justify-center">
                <i class="fas fa-award text-white text-xs"></i>
              </div>
              <span class="text-xxs font-medium text-gray-500 tracking-wide">สถาบัน <?= e(APP_NAME) ?></span>
            </div>
            <p class="text-xxs uppercase tracking-[.18em] text-primary-400 font-semibold mb-0.5">เกียรติบัตร</p>
            <p class="text-xxs text-gray-400 mb-2.5">Certificate of Achievement</p>
            <p class="text-xxs text-gray-500 mb-1">มอบให้แก่</p>
            <p class="font-bold text-gray-900 leading-tight mb-2" style="font-family:'Noto Serif Thai',serif;font-size:clamp(13px,3vw,20px);">
              <?= e($certificate['title'] . $certificate['first_name'] . ' ' . $certificate['last_name']) ?>
            </p>
            <p class="text-xxs text-gray-400 mb-0.5">ผ่านการทดสอบหลักสูตร</p>
            <p class="font-semibold text-gray-700 mb-0.5" style="font-size:clamp(9px,2vw,13px);"><?= e($certificate['project_name']) ?></p>
            <p class="text-xxs text-gray-400 mb-3">คะแนน <span class="text-primary-500 font-semibold"><?= (int)$certificate['percent'] ?>%</span></p>
            <div class="flex items-center justify-between w-full mt-1">
              <div class="text-left">
                <p class="text-xxs font-mono font-semibold text-gray-600"><?= e($certificate['cert_number']) ?></p>
                <p class="text-xxs text-gray-400"><?= date('j', strtotime($certificate['issued_date'])) . ' ' . (['','ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'][date('n', strtotime($certificate['issued_date']))]) . ' ' . (date('Y', strtotime($certificate['issued_date']))+543) ?></p>
              </div>
              <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center border border-gray-200 relative overflow-hidden">
                <i class="fas fa-qrcode text-gray-400 text-lg"></i>
                <div class="scan-line absolute left-1 right-1 h-0.5 bg-primary-400/60 rounded"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Info card -->
      <div class="bg-white rounded-2xl shadow-card-lg overflow-hidden anim-fade-up d4">
        <div class="px-5 py-3.5 bg-primary-50 border-b border-primary-100 flex items-center gap-2">
          <i class="fas fa-circle-info text-primary-400 text-sm"></i>
          <p class="text-sm font-semibold text-primary-800">ข้อมูลเกียรติบัตร</p>
        </div>
        <div class="divide-y divide-gray-50">
          <div class="grid grid-cols-2 divide-x divide-gray-50">
            <div class="px-5 py-3.5 info-row">
              <p class="text-xxs text-gray-400 mb-0.5">ชื่อ-นามสกุล</p>
              <p class="text-sm font-semibold text-gray-800"><?= e($certificate['first_name'] . ' ' . $certificate['last_name']) ?></p>
            </div>
            <div class="px-5 py-3.5 info-row">
              <p class="text-xxs text-gray-400 mb-0.5">หลักสูตร/โครงการ</p>
              <p class="text-sm font-medium text-gray-700"><?= e($certificate['project_name']) ?></p>
            </div>
          </div>
          <div class="grid grid-cols-2 divide-x divide-gray-50">
            <div class="px-5 py-3.5 info-row">
              <p class="text-xxs text-gray-400 mb-0.5">คะแนนที่ได้</p>
              <p class="text-sm font-semibold text-primary-500"><?= (int)$certificate['percent'] ?>% <span class="text-green-600 text-xs">(ผ่าน)</span></p>
            </div>
            <div class="px-5 py-3.5 info-row">
              <p class="text-xxs text-gray-400 mb-0.5">วันที่ออกเกียรติบัตร</p>
              <p class="text-sm font-medium text-gray-700"><?= date('j', strtotime($certificate['issued_date'])) . ' ' . (['','ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'][date('n', strtotime($certificate['issued_date']))]) . ' ' . (date('Y', strtotime($certificate['issued_date']))+543) ?></p>
            </div>
          </div>
          <div class="px-5 py-3.5 info-row">
            <p class="text-xxs text-gray-400 mb-0.5">เลขที่เกียรติบัตร</p>
            <div class="flex items-center gap-2">
              <p class="text-sm font-mono font-semibold text-gray-800"><?= e($certificate['cert_number']) ?></p>
              <button onclick="copyText('<?= e($certificate['cert_number']) ?>', 'คัดลอกเลขที่เกียรติบัตรแล้ว')" class="text-gray-400 hover:text-primary-400 transition-colors">
                <i class="fas fa-copy text-xs"></i>
              </button>
            </div>
          </div>
          <div class="px-5 py-3.5">
            <p class="text-xxs text-gray-400 mb-0.5">Verify Token</p>
            <p class="text-xxs font-mono text-gray-500 break-all"><?= e($certificate['verify_token']) ?></p>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-2.5 anim-fade-up d5">
        <button onclick="triggerDownload()" id="btn-download" class="flex items-center justify-center gap-2 py-3 bg-primary-400 hover:bg-primary-500 text-white font-semibold rounded-xl transition-colors text-sm shadow-card">
          <i class="fas fa-download text-xs"></i> ดาวน์โหลด PDF
        </button>
        <button onclick="copyLink()" class="flex items-center justify-center gap-2 py-3 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-xl transition-colors text-sm border border-gray-200">
          <i class="fas fa-link text-primary-400 text-xs"></i> คัดลอกลิงก์
        </button>
      </div>

      <div class="flex items-center justify-center gap-2 pt-2 anim-fade-up d6">
        <i class="fas fa-shield-halved text-primary-300 text-sm"></i>
        <p class="text-xxs text-gray-400">ตรวจสอบโดยระบบ <?= e(APP_NAME) ?> · ข้อมูลเชื่อถือได้</p>
      </div>
    </div>

    <!-- ══ INVALID STATE ══ -->
    <?php elseif ($mode === 'invalid'): ?>
    <div id="state-invalid" class="space-y-4">
      <div class="text-center anim-scale-in">
        <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-red-400 to-red-600 rounded-full flex items-center justify-center shadow-card-lg">
          <i class="fas fa-xmark text-3xl text-white"></i>
        </div>
        <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-red-50 border border-red-200 rounded-full mb-3">
          <span class="w-2 h-2 rounded-full bg-red-500"></span>
          <span class="text-xs font-semibold text-red-700">ไม่พบเกียรติบัตร · Not Found</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-800 mb-1">ไม่พบเกียรติบัตรนี้</h1>
        <p class="text-sm text-gray-400">Token ที่ระบุไม่มีอยู่ในระบบ หรืออาจพิมพ์ผิด</p>
      </div>

      <div class="bg-white rounded-2xl shadow-card-lg overflow-hidden anim-fade-up d2">
        <div class="px-5 py-3.5 bg-red-50 border-b border-red-100 flex items-center gap-2">
          <i class="fas fa-triangle-exclamation text-red-400 text-sm"></i>
          <p class="text-sm font-semibold text-red-800">สาเหตุที่เป็นไปได้</p>
        </div>
        <div class="p-5 space-y-3">
          <div class="flex items-start gap-3">
            <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0 mt-0.5">
              <span class="text-xxs font-bold text-red-500">1</span>
            </div>
            <p class="text-sm text-gray-600">URL หรือ QR Code อาจชำรุด หรือถูกแก้ไข</p>
          </div>
          <div class="flex items-start gap-3">
            <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0 mt-0.5">
              <span class="text-xxs font-bold text-red-500">2</span>
            </div>
            <p class="text-sm text-gray-600">เกียรติบัตรนี้อาจเป็นเอกสารปลอมที่ไม่ได้ออกจากระบบจริง</p>
          </div>
          <div class="flex items-start gap-3">
            <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0 mt-0.5">
              <span class="text-xxs font-bold text-red-500">3</span>
            </div>
            <p class="text-sm text-gray-600">Token อาจพิมพ์ผิด กรุณาตรวจสอบและลองใหม่อีกครั้ง</p>
          </div>
        </div>
      </div>
    </div>

    <!-- ══ REVOKED STATE ══ -->
    <?php elseif ($mode === 'revoked'): ?>
    <div id="state-revoked" class="space-y-4">
      <div class="text-center anim-scale-in">
        <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-gray-400 to-gray-600 rounded-full flex items-center justify-center shadow-card-lg">
          <i class="fas fa-ban text-3xl text-white"></i>
        </div>
        <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-gray-100 border border-gray-300 rounded-full mb-3">
          <span class="w-2 h-2 rounded-full bg-gray-500"></span>
          <span class="text-xs font-semibold text-gray-600">ถูกเพิกถอน · Revoked</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-800 mb-1">เกียรติบัตรถูกเพิกถอน</h1>
        <p class="text-sm text-gray-400">เกียรติบัตรนี้เคยมีอยู่จริง แต่ถูกยกเลิกโดยผู้ออกเกียรติบัตร</p>
      </div>

      <div class="anim-fade-up d2">
        <p class="text-xxs text-gray-400 uppercase tracking-widest mb-2.5 text-center">เกียรติบัตร (ถูกเพิกถอน)</p>
        <div class="relative bg-white rounded-2xl shadow-card overflow-hidden" style="aspect-ratio:1.414/1; min-height:200px; filter:grayscale(0.6);">
          <div class="cert-frame" style="border-color:#9ca3af"></div>
          <div class="cert-corner tl" style="border-color:#9ca3af"></div>
          <div class="cert-corner tr" style="border-color:#9ca3af"></div>
          <div class="cert-corner bl" style="border-color:#9ca3af"></div>
          <div class="cert-corner br" style="border-color:#9ca3af"></div>
          <div class="cert-watermark"><i class="fas fa-award"></i></div>
          <div class="absolute inset-0 z-0" style="background:#f9f9f9;"></div>
          <div class="stamp-revoked">REVOKED</div>
          <div class="relative z-5 flex flex-col items-center justify-center h-full px-8 text-center py-4 opacity-60">
            <p class="text-xxs uppercase tracking-[.15em] text-gray-400 font-semibold mb-1">เกียรติบัตร</p>
            <p class="font-bold text-gray-700 leading-tight mb-1" style="font-family:'Noto Serif Thai',serif;font-size:clamp(12px,3vw,18px);"><?= e($certificate['title'] . $certificate['first_name'] . ' ' . $certificate['last_name']) ?></p>
            <p class="font-medium text-gray-500 mb-1" style="font-size:clamp(9px,2vw,12px);"><?= e($certificate['project_name']) ?></p>
            <p class="text-xxs font-mono text-gray-400"><?= e($certificate['cert_number']) ?></p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-card-lg overflow-hidden anim-fade-up d3">
        <div class="px-5 py-3.5 bg-gray-50 border-b border-gray-100 flex items-center gap-2">
          <i class="fas fa-circle-info text-gray-400 text-sm"></i>
          <p class="text-sm font-semibold text-gray-600">ข้อมูลการเพิกถอน</p>
        </div>
        <div class="divide-y divide-gray-50">
          <div class="grid grid-cols-2 divide-x divide-gray-50">
            <div class="px-5 py-3.5">
              <p class="text-xxs text-gray-400 mb-0.5">ชื่อ-นามสกุล</p>
              <p class="text-sm font-semibold text-gray-500"><?= e($certificate['first_name'] . ' ' . $certificate['last_name']) ?></p>
            </div>
            <div class="px-5 py-3.5">
              <p class="text-xxs text-gray-400 mb-0.5">หลักสูตร</p>
              <p class="text-sm font-medium text-gray-500"><?= e($certificate['project_name']) ?></p>
            </div>
          </div>
          <div class="px-5 py-3.5">
            <p class="text-xxs text-gray-400 mb-0.5">เหตุผล</p>
            <p class="text-sm text-gray-600">พบข้อผิดพลาดในการออกเกียรติบัตร หรือถูกยกเลิกโดยผู้ดูแลระบบ</p>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- ── Search bar ── -->
    <div id="search-bar" class="mt-6 anim-fade-up d6">
      <div class="bg-white/70 backdrop-blur rounded-2xl border border-white/80 shadow-card p-4">
        <p class="text-xxs text-gray-400 uppercase tracking-widest mb-2.5">ตรวจสอบเกียรติบัตรอื่น</p>
        <div class="flex gap-2">
          <input id="token-input" type="text" placeholder="วาง Verify Token หรือเลขที่เกียรติบัตร..."
            class="token-input flex-1 h-10 px-3 text-sm font-mono border border-gray-200 rounded-xl bg-white transition-colors">
          <button onclick="handleSearch()"
            class="px-4 h-10 bg-primary-400 hover:bg-primary-500 text-white text-sm font-medium rounded-xl transition-colors flex items-center gap-2">
            <i class="fas fa-search text-xs"></i>
            <span class="hidden sm:inline">ตรวจสอบ</span>
          </button>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- ── FOOTER ── -->
<footer class="border-t border-gray-100 bg-white/60 backdrop-blur py-5 px-6 text-center">
  <div class="flex items-center justify-center gap-2 mb-1">
    <div class="w-5 h-5 bg-primary-400 rounded flex items-center justify-center">
      <i class="fas fa-award text-white text-xxs"></i>
    </div>
    <span class="text-xs font-semibold text-gray-600"><?= e(APP_NAME) ?> Verification System</span>
  </div>
  <p class="text-xxs text-gray-400">© <?= date('Y')+543 ?> <?= e(APP_NAME) ?> · ระบบออกข้อสอบและเกียรติบัตรออนไลน์</p>
</footer>

<!-- Hidden Iframe for PDF rendering -->
<iframe id="download-iframe" name="cert_iframe" src="about:blank" style="width:1px; height:1px; border:none; opacity:0; position:absolute;"></iframe>

<div id="toast" class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-50
  bg-gray-900 text-white text-sm font-medium px-5 py-2.5 rounded-xl shadow-card-lg
  flex items-center gap-2.5 transition-all">
  <i id="toast-icon" class="fas fa-check text-green-400 text-xs"></i>
  <span id="toast-msg"></span>
</div>

<script>
function showToast(msg, icon='fa-check', color='text-green-400') {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent  = msg;
  document.getElementById('toast-icon').className = `fas ${icon} text-xs ${color}`;
  t.classList.remove('hidden');
  clearTimeout(t._timer);
  t._timer = setTimeout(() => t.classList.add('hidden'), 2500);
}

function copyText(text, successMsg) {
  navigator.clipboard?.writeText(text);
  showToast(successMsg);
}

function copyLink() {
  navigator.clipboard?.writeText(window.location.href);
  showToast('คัดลอกลิงก์แล้ว');
}

function handleSearch() {
  const val = document.getElementById('token-input').value.trim();
  if (!val) { 
    showToast('กรุณากรอก Token หรือเลขที่เกียรติบัตร','fa-triangle-exclamation','text-amber-400'); 
    return; 
  }
  window.location.href = `<?= e(BASE_URL) ?>/public/verify.php?token=${encodeURIComponent(val)}`;
}

function handleInitialSearch() {
  const val = document.getElementById('initial-token-input').value.trim();
  if (!val) { 
    showToast('กรุณากรอก Token หรือเลขที่เกียรติบัตร','fa-triangle-exclamation','text-amber-400'); 
    return; 
  }
  window.location.href = `<?= e(BASE_URL) ?>/public/verify.php?token=${encodeURIComponent(val)}`;
}

function triggerDownload() {
    <?php if ($certificate): ?>
    const btn = document.getElementById('btn-download');
    const iframe = document.getElementById('download-iframe');
    if (!btn || !iframe) return;

    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> กำลังเตรียมไฟล์...';
    btn.disabled = true;

    const renderUrl = "<?= e(BASE_URL . '/public/render-cert.php?token=' . $certificate['verify_token']) ?>&download=1";
    iframe.src = renderUrl;

    window.onmessage = function(e) {
        if (e.data === 'download_complete') {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        }
    };
    <?php endif; ?>
}
</script>
