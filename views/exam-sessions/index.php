<div class="mb-6 flex items-center justify-between gap-4">
    <h2 class="text-2xl font-semibold">Exam Sessions</h2>
    <a class="rounded border border-gray-200 px-4 py-2" href="<?= e(BASE_URL) ?>/admin/exam-sessions/export.php">Export CSV</a>
</div>

<div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-100 text-left">
            <tr>
                <th class="px-4 py-3">ผู้สอบ</th>
                <th class="px-4 py-3">โครงการ</th>
                <th class="px-4 py-3">คะแนน</th>
                <th class="px-4 py-3">ผล</th>
                <th class="px-4 py-3">จัดการ</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php foreach ($sessions as $session): ?>
                <tr>
                    <td class="px-4 py-3"><?= e($session['participant_name']) ?></td>
                    <td class="px-4 py-3"><?= e($session['project_name']) ?></td>
                    <td class="px-4 py-3"><?= e((string) $session['percent']) ?>%</td>
                    <td class="px-4 py-3"><?= e($session['result']) ?></td>
                    <td class="px-4 py-3">
                        <?php if ($session['result'] === 'pass' && $session['status'] === 'submitted'): ?>
                            <form method="post" action="<?= e(BASE_URL) ?>/admin/certificates/issue.php">
                                <?= csrfField() ?>
                                <input type="hidden" name="session_id" value="<?= (int) $session['id'] ?>">
                                <button class="text-primary-600 hover:underline">ออกใบเซอร์</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$sessions): ?>
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">ยังไม่มี session การสอบ</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
