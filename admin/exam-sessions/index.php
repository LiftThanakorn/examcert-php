<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Certificate.php';
requireLogin();
$stmt = getDB()->query('SELECT es.*, p.name AS project_name, CONCAT(pt.first_name, " ", pt.last_name) AS participant_name FROM exam_sessions es JOIN projects p ON p.id=es.project_id JOIN participants pt ON pt.id=es.participant_id ORDER BY es.started_at DESC');
$sessions = $stmt->fetchAll();
?>
<!DOCTYPE html><html lang="th"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Exam Sessions | <?= e(APP_NAME) ?></title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-gray-50 text-gray-900"><main class="max-w-7xl mx-auto p-6"><div class="mb-6 flex items-center justify-between gap-4"><h1 class="text-2xl font-semibold">Exam Sessions</h1><a class="rounded border border-gray-200 px-4 py-2" href="<?= e(BASE_URL) ?>/admin/exam-sessions/export.php">Export CSV</a></div>
<div class="overflow-x-auto rounded-lg border border-gray-200 bg-white"><table class="min-w-full divide-y divide-gray-200 text-sm"><thead class="bg-gray-100 text-left"><tr><th class="px-4 py-3">ผู้สอบ</th><th class="px-4 py-3">โครงการ</th><th class="px-4 py-3">คะแนน</th><th class="px-4 py-3">ผล</th><th class="px-4 py-3">จัดการ</th></tr></thead><tbody class="divide-y divide-gray-200">
<?php foreach ($sessions as $s): ?><tr><td class="px-4 py-3"><?= e($s['participant_name']) ?></td><td class="px-4 py-3"><?= e($s['project_name']) ?></td><td class="px-4 py-3"><?= e((string) $s['percent']) ?>%</td><td class="px-4 py-3"><?= e($s['result']) ?></td><td class="px-4 py-3"><?php if ($s['result'] === 'pass' && $s['status'] === 'submitted'): ?><form method="post" action="<?= e(BASE_URL) ?>/admin/certificates/issue.php"><?= csrfField() ?><input type="hidden" name="session_id" value="<?= (int) $s['id'] ?>"><button class="text-orange-700 hover:underline">ออกใบเซอร์</button></form><?php endif; ?></td></tr><?php endforeach; ?>
</tbody></table></div></main></body></html>
