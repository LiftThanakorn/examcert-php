<div class="mb-6 flex items-start justify-between gap-4">
    <div>
        <h2 class="text-2xl font-semibold">ใบเซอร์/เกียรติบัตร</h2>
        <p class="text-sm text-gray-600">รายการใบเซอร์ที่ออกจากผลสอบผ่านแล้ว</p>
    </div>
</div>

<?php if ($flash): ?>
    <div class="mb-4 rounded border border-success-600 bg-success-50 px-4 py-3 text-success-600"><?= e($flash['message'] ?? '') ?></div>
<?php endif; ?>

<div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-100 text-left">
            <tr>
                <th class="px-4 py-3">เลขที่</th>
                <th class="px-4 py-3">ผู้รับ</th>
                <th class="px-4 py-3">โครงการ</th>
                <th class="px-4 py-3">วันที่ออก</th>
                <th class="px-4 py-3">Verify</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php foreach ($certificates as $cert): ?>
                <tr>
                    <td class="px-4 py-3"><?= e($cert['cert_number']) ?></td>
                    <td class="px-4 py-3"><?= e($cert['participant_name']) ?></td>
                    <td class="px-4 py-3"><?= e($cert['project_name']) ?></td>
                    <td class="px-4 py-3"><?= e($cert['issued_date']) ?></td>
                    <td class="px-4 py-3"><a class="text-primary-600 hover:underline" href="<?= e($cert['verify_url']) ?>" target="_blank">ตรวจสอบ</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$certificates): ?>
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">ยังไม่มีใบเซอร์</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
