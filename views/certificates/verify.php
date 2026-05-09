<?php
/** @var string $token */
/** @var string $mode */
/** @var array|null $certificate */
/** @var array $results */
?>

<style>
/* ── Mesh backgrounds ── */
.bg-mesh-initial, .bg-mesh-list {
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
@keyframes ripple  { 0%{transform:scale(1);opacity:0.55} 100%{transform:scale(2.6);opacity:0} }

.anim-fade-up  { animation:fadeUp  0.45s ease both; }
.anim-scale-in { animation:scaleIn 0.45s cubic-bezier(.34,1.46,.64,1) both; }
.d1{animation-delay:.08s} .d2{animation-delay:.16s} .d3{animation-delay:.24s}
.d4{animation-delay:.32s} .d5{animation-delay:.42s} .d6{animation-delay:.54s}

.ripple-ring { position:absolute; inset:0; border-radius:50%; border:2px solid rgba(232,119,34,0.35); animation:ripple 2.5s ease-out infinite; }
.ripple-ring:nth-child(2) { animation-delay:0.8s; }
.ripple-ring:nth-child(3) { animation-delay:1.6s; }

.token-input:focus { outline:none; border-color:#E87722; box-shadow:0 0 0 3px rgba(232,119,34,0.18); }
body { transition: background-color 0.5s ease; min-height: 100vh; display: flex; flex-direction: column; }
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

    <?php if ($mode === 'initial'): ?>
      <!-- ══ INITIAL STATE ══ -->
      <div id="state-initial" class="space-y-8 py-10">
        <div class="text-center anim-fade-up">
          <div class="w-24 h-24 mx-auto mb-6 bg-white rounded-3xl shadow-card-lg flex items-center justify-center relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-primary-50 to-primary-100/50 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <i class="fas fa-search text-4xl text-primary-400 relative z-10 transition-transform group-hover:scale-110"></i>
          </div>
          <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-3">ตรวจสอบเกียรติบัตร</h1>
          <p class="text-gray-500 max-w-sm mx-auto leading-relaxed">
            กรุณากรอก Verify Token, เลขที่เกียรติบัตร <br>หรือ<b>ชื่อ-นามสกุล</b> เพื่อดาวน์โหลดเกียรติบัตร
          </p>
        </div>

        <div class="bg-white/80 backdrop-blur-xl rounded-[2.5rem] border border-white p-8 shadow-card-lg anim-fade-up d2">
          <div class="space-y-4">
            <div class="relative">
              <label class="block text-xxs font-bold text-primary-400 uppercase tracking-[0.2em] mb-2 ml-1">กรอกข้อมูลเพื่อค้นหา</label>
              <input id="initial-token-input" type="text" placeholder="ชื่อ-นามสกุล, เลขใบเซอร์ หรือ Token..."
                class="token-input w-full h-14 px-5 text-base font-sans border border-gray-100 rounded-2xl bg-white/50 backdrop-blur transition-all focus:bg-white">
            </div>
            <button onclick="handleInitialSearch()"
              class="w-full h-14 bg-primary-400 hover:bg-primary-500 text-white font-bold rounded-2xl transition-all shadow-lg shadow-primary-200 active:scale-[0.98] flex items-center justify-center gap-3">
              <i class="fas fa-magnifying-glass text-lg"></i>
              ค้นหาข้อมูลเกียรติบัตร
            </button>
          </div>
        </div>
      </div>

    <?php elseif ($mode === 'list'): ?>
      <!-- ══ LIST STATE (Multiple Results) ══ -->
      <div id="state-list" class="space-y-6 py-6">
        <div class="text-center anim-fade-up">
          <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-primary-50 border border-primary-200 rounded-full mb-3">
            <span class="w-2 h-2 rounded-full bg-primary-400 animate-pulse"></span>
            <span class="text-xs font-semibold text-primary-700">พบเกียรติบัตร <?= count($results) ?> รายการ</span>
          </div>
          <h1 class="text-2xl font-bold text-gray-800">เลือกเกียรติบัตรที่ต้องการ</h1>
          <p class="text-sm text-gray-500 mt-1">ผลการค้นหาสำหรับ "<?= e($token) ?>"</p>
        </div>

        <div class="grid gap-3 anim-fade-up d2">
          <?php foreach ($results as $index => $res): ?>
          <a href="<?= e(BASE_URL) ?>/public/verify.php?token=<?= e($res['verify_token']) ?>" 
             class="group bg-white/80 backdrop-blur hover:bg-white p-4 rounded-2xl border border-white shadow-sm hover:shadow-card-lg transition-all flex items-center gap-4 anim-fade-up d<?= ($index % 5) + 1 ?>">
            <div class="w-12 h-12 bg-primary-50 rounded-xl flex items-center justify-center text-primary-400 group-hover:bg-primary-400 group-hover:text-white transition-colors">
              <i class="fas fa-file-certificate text-xl"></i>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-bold text-gray-800 truncate"><?= e($res['title'] . $res['first_name'] . ' ' . $res['last_name']) ?></p>
              <p class="text-xs text-gray-500 truncate"><?= e($res['project_name']) ?></p>
              <div class="flex items-center gap-3 mt-1">
                <span class="text-xxs font-mono text-gray-400 bg-gray-50 px-1.5 py-0.5 rounded"><?= e($res['cert_number']) ?></span>
                <span class="text-xxs text-gray-400"><?= date('d/m/Y', strtotime($res['issued_date'])) ?></span>
              </div>
            </div>
            <i class="fas fa-chevron-right text-gray-300 group-hover:text-primary-400 transition-colors"></i>
          </a>
          <?php endforeach; ?>
        </div>

        <div class="text-center anim-fade-up d5">
          <p class="text-xs text-gray-400">ไม่พบรายการที่ต้องการ? <button onclick="document.getElementById('token-input')?.focus()" class="text-primary-400 font-semibold underline">ลองค้นหาใหม่อีกครั้ง</button></p>
        </div>
      </div>

    <?php elseif ($mode === 'valid'): ?>
      <!-- ══ VALID STATE ══ -->
      <div id="state-valid" class="space-y-4">
        <div class="text-center anim-scale-in">
          <div class="relative inline-flex items-center justify-center w-24 h-24 mb-4">
            <div class="ripple-ring"></div>
            <div class="ripple-ring"></div>
            <div class="ripple-ring"></div>
            <div class="relative w-20 h-20 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center shadow-cert z-10">
              <svg width="38" height="38" viewBox="0 0 38 38" fill="none">
                <path d="M8 19 L16 27 L30 11" stroke="white" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
          </div>
          <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-green-50 border border-green-200 rounded-full mb-3 anim-fade-up d1">
            <span class="w-2 h-2 rounded-full bg-green-500"></span>
            <span class="text-xs font-semibold text-green-700">เกียรติบัตรถูกต้อง · Verified</span>
          </div>
          <h1 class="text-2xl font-bold text-gray-800 mb-1 anim-fade-up d2">เกียรติบัตรนี้แท้จริง</h1>
          <p class="text-sm text-gray-400 anim-fade-up d3">ตรวจสอบ ณ <?= date('j') . ' ' . (['','ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'][date('n')]) . ' ' . (date('Y')+543) . ' · ' . date('H:i') ?> น.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-card-lg overflow-hidden anim-fade-up d4">
          <div class="divide-y divide-gray-50">
            <div class="grid grid-cols-2 divide-x divide-gray-50">
              <div class="px-5 py-3.5"><p class="text-xxs text-gray-400 mb-0.5">ชื่อ-นามสกุล</p><p class="text-sm font-semibold text-gray-800"><?= e($certificate['title'] . $certificate['first_name'] . ' ' . $certificate['last_name']) ?></p></div>
              <div class="px-5 py-3.5"><p class="text-xxs text-gray-400 mb-0.5">หลักสูตร/โครงการ</p><p class="text-sm font-medium text-gray-700 truncate"><?= e($certificate['project_name']) ?></p></div>
            </div>
            <div class="px-5 py-3.5"><p class="text-xxs text-gray-400 mb-0.5">เลขที่เกียรติบัตร</p>
              <div class="flex items-center gap-2"><p class="text-sm font-mono font-semibold text-gray-800"><?= e($certificate['cert_number']) ?></p>
              <button onclick="copyText('<?= e($certificate['cert_number']) ?>', 'คัดลอกเลขที่แล้ว')" class="text-gray-400 hover:text-primary-400"><i class="fas fa-copy text-xs"></i></button></div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-2.5 anim-fade-up d5">
          <button onclick="triggerDownload()" id="btn-download" class="flex items-center justify-center gap-2 py-3 bg-primary-400 hover:bg-primary-500 text-white font-semibold rounded-xl transition-colors text-sm shadow-card">
            <i class="fas fa-download text-xs"></i> ดาวน์โหลดเกียรติบัตร (PDF)
          </button>
          <button onclick="copyLink()" class="flex items-center justify-center gap-2 py-3 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-xl transition-colors text-sm border border-gray-200">
            <i class="fas fa-link text-primary-400 text-xs"></i> คัดลอกลิงก์
          </button>
        </div>
      </div>

    <?php elseif ($mode === 'invalid'): ?>
      <!-- ══ INVALID STATE ══ -->
      <div id="state-invalid" class="space-y-4 py-6">
        <div class="text-center anim-scale-in">
          <div class="w-20 h-20 mx-auto mb-4 bg-red-500 rounded-full flex items-center justify-center shadow-card-lg"><i class="fas fa-xmark text-3xl text-white"></i></div>
          <h1 class="text-2xl font-bold text-gray-800 mb-1">ไม่พบข้อมูลเกียรติบัตร</h1>
          <p class="text-sm text-gray-400">ชื่อ-นามสกุล หรือข้อมูลที่ระบุไม่ถูกต้อง</p>
        </div>
        <div class="bg-white rounded-2xl p-6 text-center shadow-sm anim-fade-up d2">
          <p class="text-sm text-gray-600 mb-4">ลองพิมพ์เฉพาะ<b>ชื่อ หรือนามสกุล</b> เพื่อค้นหาอีกครั้ง</p>
          <button onclick="document.getElementById('token-input')?.focus()" class="text-primary-400 font-bold text-sm">กลับไปค้นหาใหม่</button>
        </div>
      </div>

    <?php elseif ($mode === 'revoked'): ?>
      <!-- ══ REVOKED STATE ══ -->
      <div id="state-revoked" class="space-y-4 py-6">
        <div class="text-center anim-scale-in">
          <div class="w-20 h-20 mx-auto mb-4 bg-gray-500 rounded-full flex items-center justify-center shadow-card-lg"><i class="fas fa-ban text-3xl text-white"></i></div>
          <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-gray-100 border border-gray-300 rounded-full mb-3">
            <span class="w-2 h-2 rounded-full bg-gray-500"></span>
            <span class="text-xs font-semibold text-gray-600">ถูกเพิกถอน · Revoked</span>
          </div>
          <h1 class="text-2xl font-bold text-gray-800 mb-1">เกียรติบัตรถูกเพิกถอน</h1>
          <p class="text-sm text-gray-400">เกียรติบัตรเลขที่ <?= e($certificate['cert_number']) ?> ถูกยกเลิกโดยผู้ออกประกาศ</p>
        </div>
      </div>
    <?php endif; ?>

    <!-- ── Search bar (Always show except for initial results) ── -->
    <?php if ($mode !== 'initial'): ?>
    <div id="search-bar" class="mt-8 anim-fade-up d6">
      <div class="bg-white/70 backdrop-blur rounded-2xl border border-white/80 shadow-card p-4">
        <p class="text-xxs text-gray-400 uppercase tracking-widest mb-2.5">ค้นหาข้อมูลอื่น</p>
        <div class="flex gap-2">
          <input id="token-input" type="text" placeholder="ชื่อ-นามสกุล, เลขใบเซอร์ หรือ Token..."
            class="token-input flex-1 h-10 px-3 text-sm font-sans border border-gray-200 rounded-xl bg-white">
          <button onclick="handleSearch()" class="px-4 h-10 bg-primary-400 hover:bg-primary-500 text-white text-sm font-medium rounded-xl flex items-center gap-2 transition-colors">
            <i class="fas fa-search text-xs"></i><span>ค้นหา</span>
          </button>
        </div>
      </div>
    </div>
    <?php endif; ?>

  </div>
</main>

<!-- ── FOOTER ── -->
<footer class="py-20 px-6 border-t border-gray-100 bg-white/50 mt-auto">
    <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 items-center">
        <div>
            <div class="flex items-center gap-3 mb-6">
                <div class="w-8 h-8 bg-gray-900 rounded-lg flex items-center justify-center text-white">
                    <i class="fas fa-certificate text-sm"></i>
                </div>
                <span class="text-lg font-extrabold tracking-tighter text-gray-900">ExamCert <span class="text-gray-300 font-medium">v1.0</span></span>
            </div>
            <p class="text-sm text-gray-400 leading-relaxed max-w-md">
                ระบบสอบออนไลน์และออกใบประกาศนียบัตรอย่างเป็นทางการ<br>
                เพื่อสนับสนุนความเป็นเลิศทางวิชาการและการพัฒนาบุคลากรอย่างต่อเนื่อง
            </p>
        </div>
        <div class="space-y-4 md:text-right">
            <p class="text-sm font-bold text-gray-900">&copy; <?= date('Y') + 543 ?> มหาวิทยาลัยราชภัฏร้อยเอ็ด</p>
            <div class="text-xs text-gray-400 space-y-1">
                <p>ระบบออกข้อสอบพร้อมรับเกียรติบัตร พัฒนาโดยนายธนากร อินทพันธ์</p>
                <p>บุคลากร งานบริหารทรัพยากรบุคคลและนิติการ มหาวิทยาลัยราชภัฏร้อยเอ็ด</p>
            </div>
        </div>
    </div>
</footer>

<iframe id="download-iframe" name="cert_iframe" src="about:blank" style="display:none"></iframe>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Reusable Swal Toast function
    const swalToast = Swal.mixin({
      toast: true,
      position: 'bottom',
      showConfirmButton: false,
      timer: 2500,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
      }
    });

    window.copyText = function(text, msg) { 
      navigator.clipboard?.writeText(text); 
      swalToast.fire({ icon: 'success', title: msg });
    };

    window.copyLink = function() { 
      navigator.clipboard?.writeText(window.location.href); 
      swalToast.fire({ icon: 'success', title: 'คัดลอกลิงก์แล้ว' });
    };

    window.handleSearch = function() {
      const val = document.getElementById('token-input').value.trim();
      if (!val) {
        swalToast.fire({ icon: 'warning', title: 'กรุณากรอกข้อมูลค้นหา' });
        return;
      }
      window.location.href = `<?= e(BASE_URL) ?>/public/verify.php?token=${encodeURIComponent(val)}`;
    };

    window.handleInitialSearch = function() {
      const val = document.getElementById('initial-token-input').value.trim();
      if (!val) {
        swalToast.fire({ icon: 'warning', title: 'กรุณากรอกข้อมูลค้นหา' });
        return;
      }
      window.location.href = `<?= e(BASE_URL) ?>/public/verify.php?token=${encodeURIComponent(val)}`;
    };

    window.triggerDownload = function() {
        <?php if ($certificate): ?>
        const btn = document.getElementById('btn-download');
        const iframe = document.getElementById('download-iframe');
        const originalHTML = btn.innerHTML;
        
        swalToast.fire({ 
          icon: 'info', 
          title: 'กำลังเตรียมไฟล์เกียรติบัตร...',
          timer: 4000
        });

        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> กำลังเตรียมไฟล์...';
        btn.disabled = true;
        
        iframe.src = "<?= e(BASE_URL . '/public/render-cert.php?token=' . $certificate['verify_token']) ?>&download=1";
        
        window.onmessage = (e) => { 
          if (e.data === 'download_complete') { 
            btn.innerHTML = originalHTML; 
            btn.disabled = false;
            swalToast.fire({ icon: 'success', title: 'ดาวน์โหลดสำเร็จ' });
          } 
        };
        <?php endif; ?>
    };
});
</script>
