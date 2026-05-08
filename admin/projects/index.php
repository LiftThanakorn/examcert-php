<?php
declare(strict_types=1);

require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Project.php';

requireLogin();

$projects = getAllProjects();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โครงการสอบ | <?= e(APP_NAME) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
    <main class="max-w-7xl mx-auto p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold">โครงการสอบ</h1>
                <p class="text-sm text-gray-600">จัดการโครงการสอบและการออกใบเซอร์</p>
            </div>
            <a href="<?= e(BASE_URL) ?>/admin/projects/create.php" class="rounded bg-orange-600 px-4 py-2 text-white hover:bg-orange-700">สร้างโครงการ</a>
        </div>

        <?php if ($flash): ?>
            <div class="mb-4 rounded border border-green-200 bg-green-50 px-4 py-3 text-green-700"><?= e($flash['message'] ?? '') ?></div>
        <?php endif; ?>

        <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="px-4 py-3">ชื่อโครงการ</th>
                        <th class="px-4 py-3">สถานะ</th>
                        <th class="px-4 py-3">ช่วงสอบ</th>
                        <th class="px-4 py-3">ผู้เข้าสอบ</th>
                        <th class="px-4 py-3">ข้อสอบ</th>
                        <th class="px-4 py-3 text-right">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                <?php foreach ($projects as $project): ?>
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-medium"><?= e($project['name']) ?></div>
                            <div class="text-xs text-gray-500"><?= e($project['code'] ?: '-') ?></div>
                        </td>
                        <td class="px-4 py-3"><?= e($project['status']) ?></td>
                        <td class="px-4 py-3"><?= e($project['exam_start'] ?: '-') ?> ถึง <?= e($project['exam_end'] ?: '-') ?></td>
                        <td class="px-4 py-3"><?= (int) $project['participant_count'] ?></td>
                        <td class="px-4 py-3"><?= (int) $project['question_count_total'] ?></td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a class="text-blue-700 hover:underline" href="<?= e(BASE_URL) ?>/admin/projects/detail.php?id=<?= (int) $project['id'] ?>">ดู</a>
                            <a class="text-orange-700 hover:underline" href="<?= e(BASE_URL) ?>/admin/projects/edit.php?id=<?= (int) $project['id'] ?>">แก้ไข</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$projects): ?>
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">ยังไม่มีโครงการสอบ</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>

