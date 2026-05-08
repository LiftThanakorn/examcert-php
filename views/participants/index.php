<div class="mb-6 flex items-start justify-between gap-4">
    <div>
        <h2 class="text-2xl font-semibold">ผู้มีสิทธิ์สอบ</h2>
        <p class="text-sm text-gray-600"><?= e($project['name']) ?></p>
    </div>
    <div class="flex gap-2">
        <a href="<?= e(BASE_URL) ?>/admin/projects/detail.php?id=<?= (int) $projectId ?>" class="rounded border border-gray-200 px-4 py-2">กลับโครงการ</a>
        <a href="<?= e(BASE_URL) ?>/admin/participants/create.php?project_id=<?= (int) $projectId ?>" class="rounded bg-primary-600 px-4 py-2 text-white">เพิ่มรายชื่อ</a>
    </div>
</div>

<?php if ($flash): ?>
    <div class="mb-4 rounded border border-success-600 bg-success-50 px-4 py-3 text-success-600"><?= e($flash['message'] ?? '') ?></div>
<?php endif; ?>

<div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-100 text-left">
            <tr>
                <th class="px-4 py-3">ชื่อ-นามสกุล</th>
                <th class="px-4 py-3">หน่วยงาน</th>
                <th class="px-4 py-3">อีเมล</th>
                <th class="px-4 py-3">Token</th>
                <th class="px-4 py-3 text-right">จัดการ</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
        <?php foreach ($participants as $participant): ?>
            <tr>
                <td class="px-4 py-3 font-medium"><?= e(trim(($participant['title'] ? $participant['title'] . ' ' : '') . $participant['first_name'] . ' ' . $participant['last_name'])) ?></td>
                <td class="px-4 py-3"><?= e($participant['organization'] ?: '-') ?></td>
                <td class="px-4 py-3"><?= e($participant['email'] ?: '-') ?></td>
                <td class="px-4 py-3"><code class="text-xs"><?= e(substr($participant['access_token'], 0, 12)) ?>...</code></td>
                <td class="px-4 py-3 text-right">
                    <a class="text-primary-600 hover:underline" href="<?= e(BASE_URL) ?>/admin/participants/edit.php?id=<?= (int) $participant['id'] ?>">แก้ไข</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$participants): ?>
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">ยังไม่มีรายชื่อผู้มีสิทธิ์สอบ</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

