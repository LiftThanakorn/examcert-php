<?php
declare(strict_types=1);

require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Project.php';
require_once __DIR__ . '/../../src/Participant.php';

requireLogin();

$projectId = (int) ($_GET['project_id'] ?? 0);
$project = getProject($projectId);
if (!$project) {
    http_response_code(404);
    exit('Project not found.');
}

$participants = getParticipantsByProject($projectId);
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ผู้มีสิทธิ์สอบ | <?= e(APP_NAME) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
    <main class="max-w-7xl mx-auto p-6">
        <div class="mb-6 flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold">ผู้มีสิทธิ์สอบ</h1>
                <p class="text-sm text-gray-600"><?= e($project['name']) ?></p>
            </div>
            <div class="flex gap-2">
                <a href="<?= e(BASE_URL) ?>/admin/projects/detail.php?id=<?= (int) $projectId ?>" class="rounded border border-gray-200 px-4 py-2">กลับโครงการ</a>
                <a href="<?= e(BASE_URL) ?>/admin/participants/create.php?project_id=<?= (int) $projectId ?>" class="rounded bg-orange-600 px-4 py-2 text-white">เพิ่มรายชื่อ</a>
            </div>
        </div>

        <?php if ($flash): ?>
            <div class="mb-4 rounded border border-green-200 bg-green-50 px-4 py-3 text-green-700"><?= e($flash['message'] ?? '') ?></div>
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
                            <a class="text-orange-700 hover:underline" href="<?= e(BASE_URL) ?>/admin/participants/edit.php?id=<?= (int) $participant['id'] ?>">แก้ไข</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$participants): ?>
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">ยังไม่มีรายชื่อผู้มีสิทธิ์สอบ</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>

