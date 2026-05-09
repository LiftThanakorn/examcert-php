<?php
$passCount = 0;
$failCount = 0;
foreach ($rows as $row) {
    $passCount += (int) ($row['pass_count'] ?? 0);
    $failCount += (int) ($row['session_count'] ?? 0) - (int) ($row['pass_count'] ?? 0);
}
?>

<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 fade-up">
    <div>
        <h2 class="text-xl font-semibold text-gray-800">รายงานสรุปภาพรวม</h2>
        <p class="text-sm text-gray-400 mt-0.5">สรุปสถิติรายโครงการสำหรับติดตามความคืบหน้า</p>
    </div>
    <div class="flex items-center gap-2">
        <a class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-xl transition-colors shadow-sm" href="<?= e(BASE_URL) ?>/admin/reports/export.php">
            <i class="fas fa-file-export mr-2 text-gray-400"></i> ส่งออกรายงาน (CSV)
        </a>
    </div>
</div>

<!-- Summary Charts -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8 fade-up fade-up-1">
    <div class="lg:col-span-1 bg-white rounded-2xl border border-gray-100 shadow-card p-6 flex flex-col items-center justify-center">
        <h3 class="text-xxs font-bold text-gray-400 uppercase tracking-widest mb-4">สัดส่วนผู้สอบผ่าน/ไม่ผ่าน ทั้งหมด</h3>
        <div class="relative w-full max-w-[180px] aspect-square flex items-center justify-center">
            <canvas id="reportDonutChart"></canvas>
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                <span class="text-2xl font-bold text-gray-800"><?= $passCount + $failCount ?></span>
                <span class="text-[10px] text-gray-400 uppercase">ครั้งที่สอบ</span>
            </div>
        </div>
        <div class="flex gap-4 mt-4">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                <span class="text-xxs font-bold text-gray-600">ผ่าน: <?= $passCount ?></span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-red-400"></span>
                <span class="text-xxs font-bold text-gray-600">ไม่ผ่าน: <?= $failCount ?></span>
            </div>
        </div>
    </div>
    
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-card p-6">
        <h3 class="text-xxs font-bold text-gray-400 uppercase tracking-widest mb-6 px-2">อัตราการผ่านรายโครงการ (%)</h3>
        <div class="h-[200px]">
            <canvas id="reportBarChart"></canvas>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden fade-up fade-up-2">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse table-row-hover">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100 text-xs font-semibold text-gray-500 tracking-wide uppercase">
                    <th class="px-6 py-4 font-medium">โครงการ</th>
                    <th class="px-6 py-4 font-medium text-center">ผู้มีสิทธิ์</th>
                    <th class="px-6 py-4 font-medium text-center">ข้อสอบ</th>
                    <th class="px-6 py-4 text-center font-medium">ส่งข้อสอบแล้ว</th>
                    <th class="px-6 py-4 text-center font-medium">สอบผ่าน</th>
                    <th class="px-6 py-4 text-center font-medium">คะแนนเฉลี่ย</th>
                    <th class="px-6 py-4 text-right font-medium">Pass Rate</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                <?php foreach ($rows as $row): ?>
                    <?php
                    $sessions = (int) $row['session_count'];
                    $passes = (int) $row['pass_count'];
                    $passRate = $sessions > 0 ? round(($passes / $sessions) * 100, 1) : 0;
                    ?>
                    <tr class="group transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-800"><?= e($row['name']) ?></div>
                            <div class="text-[10px] text-gray-400 font-medium uppercase tracking-wider mt-0.5"><?= e($row['code'] ?: '-') ?> | <?= e($row['status']) ?></div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-gray-600"><?= (int) $row['participant_count'] ?></span>
                        </td>
                        <td class="px-6 py-4 text-center text-gray-600">
                            <?= (int) $row['question_count'] ?>
                        </td>
                        <td class="px-6 py-4 text-center font-semibold text-gray-800">
                            <?= $sessions ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xxs font-bold bg-green-50 text-green-700 border border-green-100">
                                <?= $passes ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-bold text-gray-700"><?= $row['avg_percent'] !== null ? round((float) $row['avg_percent'], 1) . '%' : '-' ?></span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex flex-col items-end">
                                <span class="font-bold text-primary-500"><?= e((string) $passRate) ?>%</span>
                                <div class="w-16 h-1.5 bg-gray-100 rounded-full mt-1.5 overflow-hidden">
                                    <div class="h-full bg-primary-400 rounded-full" style="width: <?= $passRate ?>%"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$rows): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mb-4">
                                    <i class="fas fa-chart-pie text-3xl text-gray-200"></i>
                                </div>
                                <p class="text-sm font-medium">ยังไม่มีข้อมูลรายงาน</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Donut Chart
    const donutCtx = document.getElementById('reportDonutChart').getContext('2d');
    new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: ['ผ่าน', 'ไม่ผ่าน'],
            datasets: [{
                data: [<?= $passCount ?>, <?= $failCount ?>],
                backgroundColor: ['#22c55e', '#f87171'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            cutout: '75%',
            plugins: {
                legend: { display: false }
            },
            animation: { animateScale: true, animateRotate: true }
        }
    });

    // Bar Chart
    const barCtx = document.getElementById('reportBarChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_map(fn($r) => $r['name'], $rows)) ?>,
            datasets: [{
                label: 'Pass Rate (%)',
                data: <?= json_encode(array_map(fn($r) => $r['session_count'] > 0 ? round(($r['pass_count'] / $r['session_count']) * 100, 1) : 0, $rows)) ?>,
                backgroundColor: 'rgba(232, 119, 34, 0.85)',
                borderRadius: 6,
                maxBarThickness: 40
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: { callback: v => v + '%' }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10 } }
                }
            }
        }
    });
});
</script>
