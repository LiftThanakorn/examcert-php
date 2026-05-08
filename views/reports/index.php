<div class="mb-6 flex items-start justify-between gap-4">
    <div>
        <h2 class="text-2xl font-semibold">Reports</h2>
        <p class="text-sm text-gray-600">สรุปภาพรวมรายโครงการสำหรับตรวจ local test</p>
    </div>
    <a class="rounded border border-gray-200 px-4 py-2" href="<?= e(BASE_URL) ?>/admin/reports/export.php">Export CSV</a>
</div>

<div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-100 text-left">
            <tr>
                <th class="px-4 py-3">โครงการ</th>
                <th class="px-4 py-3">ผู้มีสิทธิ์</th>
                <th class="px-4 py-3">ข้อสอบ</th>
                <th class="px-4 py-3">Sessions</th>
                <th class="px-4 py-3">ผ่าน</th>
                <th class="px-4 py-3">คะแนนเฉลี่ย</th>
                <th class="px-4 py-3">Pass rate</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php foreach ($rows as $row): ?>
                <?php
                $sessions = (int) $row['session_count'];
                $passes = (int) $row['pass_count'];
                $passRate = $sessions > 0 ? round(($passes / $sessions) * 100, 2) : 0;
                ?>
                <tr>
                    <td class="px-4 py-3">
                        <div class="font-medium"><?= e($row['name']) ?></div>
                        <div class="text-xs text-gray-500"><?= e($row['code'] ?: '-') ?> | <?= e($row['status']) ?></div>
                    </td>
                    <td class="px-4 py-3"><?= (int) $row['participant_count'] ?></td>
                    <td class="px-4 py-3"><?= (int) $row['question_count'] ?></td>
                    <td class="px-4 py-3"><?= $sessions ?></td>
                    <td class="px-4 py-3"><?= $passes ?></td>
                    <td class="px-4 py-3"><?= $row['avg_percent'] !== null ? e((string) round((float) $row['avg_percent'], 2)) . '%' : '-' ?></td>
                    <td class="px-4 py-3"><?= e((string) $passRate) ?>%</td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$rows): ?>
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">ยังไม่มีข้อมูลรายงาน</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
