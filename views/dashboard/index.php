<?php
$stats = $stats ?? [];
$recentProjects = $recentProjects ?? [];
$passRateChart = $passRateChart ?? [];
$recentResults = $recentResults ?? [];
$todaySchedule = $todaySchedule ?? [];
$avgPassRate = $avgPassRate ?? 0;
?>



<!-- Welcome bar -->
<div class="mb-6 fade-up">
  <div class="bg-gradient-to-r from-primary-400 to-primary-500 rounded-2xl p-5 flex items-center justify-between overflow-hidden relative shadow-md shadow-orange-100">
    <!-- decorative circles -->
    <div class="absolute -right-8 -top-8 w-40 h-40 bg-white/10 rounded-full"></div>
    <div class="absolute right-24 top-4 w-20 h-20 bg-white/8 rounded-full"></div>
    <div>
      <p class="text-white/70 text-xs mb-1">สวัสดี, <?= e($_SESSION['admin_name'] ?? 'ผู้ดูแลระบบ') ?> 👋</p>
      <h2 class="text-white font-semibold text-lg">ยินดีต้อนรับสู่ ExamCert</h2>
      <p class="text-white/65 text-xs mt-1">วันนี้มีผู้เข้าสอบ <strong class="text-white"><?= (int) $stats['participants_today'] ?> คน</strong> และออกเกียรติบัตรไปแล้ว <strong class="text-white"><?= (int) $stats['certificates_today'] ?> ใบ</strong></p>
    </div>
    <div class="text-right relative z-10 hidden md:block">
      <p class="text-white/60 text-xxs">วันที่</p>
      <p class="text-white font-semibold text-sm" id="today-date-display"></p>
    </div>
  </div>
</div>

<!-- STATS ROW -->
<div class="grid grid-cols-4 gap-4 mb-6">
  <!-- Projects -->
  <div class="bg-white rounded-xl border border-gray-100 shadow-card p-4 flex items-start gap-3 fade-up fade-up-1">
    <div class="w-10 h-10 rounded-xl bg-primary-50 flex items-center justify-center flex-shrink-0">
      <i class="fas fa-folder-open text-primary-400 text-base"></i>
    </div>
    <div>
      <p class="text-2xl font-semibold text-gray-900 leading-none"><?= (int) $stats['projects'] ?></p>
      <p class="text-xxs text-gray-400 mt-1">โครงการทั้งหมด</p>
      <span class="inline-flex items-center gap-1 text-xxs font-medium text-green-700 bg-green-50 px-2 py-0.5 rounded-full mt-1.5">
        <i class="fas fa-arrow-trend-up text-xxs"></i> <?= (int) $stats['active_projects'] ?> เปิดอยู่
      </span>
    </div>
  </div>

  <!-- Participants -->
  <div class="bg-white rounded-xl border border-gray-100 shadow-card p-4 flex items-start gap-3 fade-up fade-up-2">
    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
      <i class="fas fa-users text-blue-500 text-base"></i>
    </div>
    <div>
      <p class="text-2xl font-semibold text-gray-900 leading-none"><?= (int) $stats['participants'] ?></p>
      <p class="text-xxs text-gray-400 mt-1">ผู้เข้าอบรมทั้งหมด</p>
      <span class="inline-flex items-center gap-1 text-xxs font-medium text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full mt-1.5">
        <i class="fas fa-plus text-xxs"></i> +<?= (int) $stats['participants_month'] ?> เดือนนี้
      </span>
    </div>
  </div>

  <!-- Certificates -->
  <div class="bg-white rounded-xl border border-gray-100 shadow-card p-4 flex items-start gap-3 fade-up fade-up-3">
    <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
      <i class="fas fa-certificate text-green-600 text-base"></i>
    </div>
    <div>
      <p class="text-2xl font-semibold text-gray-900 leading-none"><?= (int) $stats['certificates'] ?></p>
      <p class="text-xxs text-gray-400 mt-1">เกียรติบัตรออกแล้ว</p>
      <span class="inline-flex items-center gap-1 text-xxs font-medium text-green-700 bg-green-50 px-2 py-0.5 rounded-full mt-1.5">
        <i class="fas fa-arrow-trend-up text-xxs"></i> +<?= (int) $stats['certificates_today'] ?> วันนี้
      </span>
    </div>
  </div>

  <!-- Pass Rate -->
  <div class="bg-white rounded-xl border border-gray-100 shadow-card p-4 flex items-start gap-3 fade-up fade-up-4">
    <div class="w-10 h-10 rounded-xl bg-primary-50 flex items-center justify-center flex-shrink-0">
      <i class="fas fa-chart-pie text-primary-400 text-base"></i>
    </div>
    <div>
      <p class="text-2xl font-semibold text-gray-900 leading-none"><?= e((string)$avgPassRate) ?><span class="text-base text-gray-400 font-normal">%</span></p>
      <p class="text-xxs text-gray-400 mt-1">อัตราผ่านเฉลี่ย</p>
      <span class="inline-flex items-center gap-1 text-xxs font-medium text-primary-700 bg-primary-100 px-2 py-0.5 rounded-full mt-1.5">
        <i class="fas fa-minus text-xxs"></i> ภาพรวมระบบ
      </span>
    </div>
  </div>
</div>

<!-- MAIN GRID -->
<div class="grid grid-cols-3 gap-4">

  <!-- LEFT COLUMN -->
  <div class="col-span-2 space-y-4">

    <!-- Chart: Pass rate per project -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-card overflow-hidden fade-up fade-up-2">
      <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-50">
        <div class="flex items-center gap-2">
          <i class="fas fa-chart-line text-primary-400 text-sm"></i>
          <h2 class="text-sm font-semibold text-gray-800">อัตราผ่านแต่ละโครงการ</h2>
        </div>
      </div>
      <div class="p-5">
        <canvas id="passRateChart" height="130"></canvas>
      </div>
    </div>

    <!-- Recent projects table -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-card overflow-hidden fade-up fade-up-3">
      <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-50">
        <div class="flex items-center gap-2">
          <i class="fas fa-folder-open text-primary-400 text-sm"></i>
          <h2 class="text-sm font-semibold text-gray-800">โครงการล่าสุด</h2>
        </div>
        <a href="<?= e(BASE_URL) ?>/admin/projects/" class="text-xs text-primary-400 hover:text-primary-500 font-medium transition-colors">ดูทั้งหมด →</a>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
              <th class="text-left text-xxs font-medium text-gray-400 uppercase tracking-wider px-5 py-3">โครงการ</th>
              <th class="text-left text-xxs font-medium text-gray-400 uppercase tracking-wider px-4 py-3 text-center">ผู้เข้า</th>
              <th class="text-left text-xxs font-medium text-gray-400 uppercase tracking-wider px-4 py-3">ผ่าน</th>
              <th class="text-left text-xxs font-medium text-gray-400 uppercase tracking-wider px-4 py-3">สถานะ</th>
              <th class="px-4 py-3"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <?php foreach ($recentProjects as $rp): ?>
            <tr class="hover:bg-primary-50/30 transition-colors group">
              <td class="px-5 py-3">
                <p class="font-medium text-gray-800 text-sm"><?= e($rp['name']) ?></p>
                <p class="text-xxs text-gray-400 mt-0.5"><?= e($rp['code'] ?: '-') ?></p>
              </td>
              <td class="px-4 py-3 text-center">
                <span class="text-sm font-medium text-gray-700"><?= (int) $rp['participant_count'] ?></span>
              </td>
              <td class="px-4 py-3">
                <?php if ($rp['pass_rate'] !== null): ?>
                <div class="flex items-center gap-2">
                  <div class="flex-1 h-1.5 bg-gray-100 rounded-full w-16">
                    <div class="h-full bg-green-500 rounded-full progress-fill" style="width:<?= (float)$rp['pass_rate'] ?>%"></div>
                  </div>
                  <span class="text-xs text-gray-600 font-medium"><?= e((string)$rp['pass_rate']) ?>%</span>
                </div>
                <?php else: ?>
                  <span class="text-xxs text-gray-400">ยังไม่เริ่ม</span>
                <?php endif; ?>
              </td>
              <td class="px-4 py-3">
                <?php
                $sCls = 'bg-gray-100 text-gray-500';
                $sDot = 'bg-gray-400';
                $sLbl = 'ร่าง';
                if ($rp['status'] === 'active') {
                  $sCls = 'bg-green-50 text-green-700';
                  $sDot = 'bg-green-500';
                  $sLbl = 'เปิดอยู่';
                } elseif ($rp['status'] === 'closed') {
                  $sCls = 'bg-red-50 text-red-700';
                  $sDot = 'bg-red-500';
                  $sLbl = 'ปิดแล้ว';
                }
                ?>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xxs font-medium <?= $sCls ?>">
                  <span class="w-1.5 h-1.5 rounded-full <?= $sDot ?>"></span><?= e($sLbl) ?>
                </span>
              </td>
              <td class="px-4 py-3">
                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                  <a href="<?= e(BASE_URL) ?>/admin/projects/detail.php?id=<?= (int)$rp['id'] ?>" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 transition-colors"><i class="fas fa-eye text-xxs"></i></a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Recent exam results -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-card overflow-hidden fade-up fade-up-4">
      <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-50">
        <div class="flex items-center gap-2">
          <i class="fas fa-clock-rotate-left text-primary-400 text-sm"></i>
          <h2 class="text-sm font-semibold text-gray-800">ผลสอบล่าสุด</h2>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
              <th class="text-left text-xxs font-medium text-gray-400 uppercase tracking-wider px-5 py-3">ผู้สอบ</th>
              <th class="text-left text-xxs font-medium text-gray-400 uppercase tracking-wider px-4 py-3">โครงการ</th>
              <th class="text-left text-xxs font-medium text-gray-400 uppercase tracking-wider px-4 py-3 text-center">คะแนน</th>
              <th class="text-left text-xxs font-medium text-gray-400 uppercase tracking-wider px-4 py-3">ผล</th>
              <th class="text-left text-xxs font-medium text-gray-400 uppercase tracking-wider px-4 py-3 text-right">เวลา</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <?php foreach ($recentResults as $rr): ?>
            <tr class="hover:bg-primary-50/30 transition-colors">
              <td class="px-5 py-3 font-medium text-gray-800 text-sm"><?= e($rr['first_name'].' '.$rr['last_name']) ?></td>
              <td class="px-4 py-3 text-xs text-gray-500"><?= e($rr['project_name']) ?></td>
              <td class="px-4 py-3 text-sm font-semibold text-gray-700 text-center"><?= e((string)$rr['percent']) ?>%</td>
              <td class="px-4 py-3">
                <?php if ($rr['result'] === 'pass'): ?>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xxs font-medium bg-green-50 text-green-700"><i class="fas fa-check text-xxs"></i>ผ่าน</span>
                <?php else: ?>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xxs font-medium bg-red-50 text-red-600"><i class="fas fa-xmark text-xxs"></i>ไม่ผ่าน</span>
                <?php endif; ?>
              </td>
              <td class="px-4 py-3 text-xxs text-gray-400 text-right"><?= e($rr['submitted_at'] ? date('H:i', strtotime($rr['submitted_at'])) : '-') ?> น.</td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div><!-- /LEFT COLUMN -->

  <!-- RIGHT COLUMN -->
  <div class="space-y-4">

    <!-- Exam schedule status -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-card overflow-hidden fade-up fade-up-2">
      <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-50">
        <div class="flex items-center gap-2">
          <i class="fas fa-calendar-check text-primary-400 text-sm"></i>
          <h2 class="text-sm font-semibold text-gray-800">สถานะการสอบวันนี้</h2>
        </div>
      </div>
      <div class="p-4 space-y-3">
        <?php foreach ($todaySchedule as $ts): ?>
        <?php 
        $isOpen = ($ts['status'] === 'active' && ($ts['manual_override'] || (strtotime($ts['exam_start'] ?? '') <= time() && strtotime($ts['exam_end'] ?? '') >= time())));
        $bgCls = $isOpen ? 'bg-green-50 border-green-100' : 'bg-blue-50 border-blue-100';
        $iconCls = $isOpen ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600';
        $icon = $isOpen ? 'fa-circle-play' : 'fa-clock';
        $lbl = $isOpen ? 'OPEN' : 'SOON';
        $lblCls = $isOpen ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700';
        ?>
        <div class="flex items-center justify-between p-3 rounded-xl border <?= $bgCls ?>">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg <?= $iconCls ?> flex items-center justify-center">
              <i class="fas <?= $icon ?> text-sm"></i>
            </div>
            <div>
              <p class="text-xs font-medium text-gray-800"><?= e($ts['name']) ?></p>
              <p class="text-xxs text-gray-400"><?= $isOpen ? 'ถึง '.date('H:i', strtotime($ts['exam_end'])) : 'เริ่ม '.date('d/m H:i', strtotime($ts['exam_start'])) ?></p>
            </div>
          </div>
          <span class="text-xxs font-semibold <?= $lblCls ?> px-2 py-1 rounded-lg"><?= $lbl ?></span>
        </div>
        <?php endforeach; ?>
        <?php if (!$todaySchedule): ?>
          <p class="text-xxs text-gray-400 text-center py-4">ไม่มีรายการสอบที่กำลังจะมาถึง</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Pass/Fail donut chart -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-card overflow-hidden fade-up fade-up-3">
      <div class="px-5 py-3.5 border-b border-gray-50 flex items-center gap-2">
        <i class="fas fa-chart-pie text-primary-400 text-sm"></i>
        <h2 class="text-sm font-semibold text-gray-800">ผ่าน / ไม่ผ่าน (รวม)</h2>
      </div>
      <div class="p-5">
        <div class="relative flex items-center justify-center" style="height:140px">
          <canvas id="donutChart" width="140" height="140" style="max-width:140px"></canvas>
          <div class="absolute text-center pointer-events-none">
            <p class="text-xl font-bold text-gray-800"><?= e((string)$avgPassRate) ?>%</p>
            <p class="text-xxs text-gray-400">ผ่าน</p>
          </div>
        </div>
        <div class="flex justify-center gap-5 mt-3">
          <div class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
            <span class="text-xxs text-gray-500">ผ่าน <?= (int) $stats['passed_sessions'] ?> คน</span>
          </div>
          <div class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-red-400"></span>
            <span class="text-xxs text-gray-500">ไม่ผ่าน <?= (int) $stats['failed_sessions'] ?> คน</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick actions -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-card p-4 fade-up fade-up-5">
      <h2 class="text-sm font-semibold text-gray-800 mb-3 uppercase tracking-wider">Quick Actions</h2>
      <div class="space-y-2">
        <a href="<?= e(BASE_URL) ?>/admin/projects/create.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-primary-50 transition-colors group">
          <div class="w-8 h-8 rounded-lg bg-primary-50 group-hover:bg-primary-100 flex items-center justify-center transition-colors flex-shrink-0">
            <i class="fas fa-folder-plus text-primary-400 text-sm"></i>
          </div>
          <span class="text-sm text-gray-700 group-hover:text-primary-600 transition-colors">สร้างโครงการใหม่</span>
          <i class="fas fa-chevron-right text-gray-300 text-xxs ml-auto"></i>
        </a>
        <a href="<?= e(BASE_URL) ?>/admin/participants/" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-primary-50 transition-colors group">
          <div class="w-8 h-8 rounded-lg bg-blue-50 group-hover:bg-blue-100 flex items-center justify-center transition-colors flex-shrink-0">
            <i class="fas fa-file-import text-blue-500 text-sm"></i>
          </div>
          <span class="text-sm text-gray-700 group-hover:text-primary-600 transition-colors">จัดการรายชื่อผู้เข้าสอบ</span>
          <i class="fas fa-chevron-right text-gray-300 text-xxs ml-auto"></i>
        </a>
      </div>
    </div>

  </div><!-- /RIGHT COLUMN -->

</div><!-- /MAIN GRID -->

<script>
// Today date (Thai)
const months = ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
const now = new Date();
const dateStr = `${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear() + 543}`;
const dateEl = document.getElementById('today-date-display');
if (dateEl) dateEl.textContent = dateStr;

// Bar chart — Pass rate per project
const barCtx = document.getElementById('passRateChart').getContext('2d');
new Chart(barCtx, {
  type: 'bar',
  data: {
    labels: <?= json_encode(array_column($passRateChart, 'name')) ?>,
    datasets: [{
      label: 'ผ่าน (%)',
      data: <?= json_encode(array_column($passRateChart, 'rate')) ?>,
      backgroundColor: [
        'rgba(232,119,34,0.85)', 'rgba(232,119,34,0.65)',
        'rgba(232,119,34,0.55)', 'rgba(232,119,34,0.45)',
        'rgba(232,119,34,0.35)',
      ],
      borderRadius: 6,
      borderSkipped: false,
    },{
      label: 'เกณฑ์ผ่าน',
      data: <?= json_encode(array_fill(0, count($passRateChart), 70)) ?>,
      type: 'line',
      borderColor: 'rgba(163,45,45,0.5)',
      borderDash: [4,3],
      borderWidth: 1.5,
      pointRadius: 0,
      fill: false,
    }],
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false },
      tooltip: {
        callbacks: {
          label: ctx => ctx.dataset.label === 'เกณฑ์ผ่าน'
            ? `เกณฑ์มาตรฐาน: ${ctx.raw}%`
            : `อัตราผ่าน: ${ctx.raw}%`
        }
      }
    },
    scales: {
      x: { grid: { display: false }, ticks: { font: { family: 'Sarabun', size: 11 } } },
      y: {
        max: 100, min: 0,
        grid: { color: 'rgba(0,0,0,0.05)' },
        ticks: { font: { family: 'Sarabun', size: 11 }, callback: v => v + '%' }
      },
    },
  },
});

// Donut chart — pass/fail
const donutCtx = document.getElementById('donutChart').getContext('2d');
new Chart(donutCtx, {
  type: 'doughnut',
  data: {
    labels: ['ผ่าน', 'ไม่ผ่าน'],
    datasets: [{
      data: [<?= (int)$stats['passed_sessions'] ?>, <?= (int)$stats['failed_sessions'] ?>],
      backgroundColor: ['#22c55e', '#f87171'],
      borderWidth: 0,
      hoverOffset: 4,
    }],
  },
  options: {
    cutout: '72%',
    plugins: { legend: { display: false }, tooltip: { enabled: true } },
    animation: { animateRotate: true, duration: 900 },
  },
});
</script>
