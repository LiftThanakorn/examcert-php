<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/Exam.php';

$sessionId = (int) ($_GET['session_id'] ?? 0);
$session = getExamSession($sessionId);
if (!$session) { http_response_code(404); exit('Session not found.'); }
$project = getProject((int) $session['project_id']);
?>
<!DOCTYPE html>
<html lang="th"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>ผลสอบ | <?= e(APP_NAME) ?></title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-gray-50 text-gray-900"><main class="max-w-xl mx-auto p-6">
<section class="rounded-lg border border-gray-200 bg-white p-6 text-center">
<p class="text-sm text-gray-600"><?= e($project['name'] ?? '') ?></p>
<h1 class="mt-2 text-3xl font-semibold"><?= $session['result'] === 'pass' ? 'ผ่าน' : 'ไม่ผ่าน' ?></h1>
<p class="mt-4 text-xl">คะแนน <?= e((string) $session['score']) ?> / <?= e((string) $session['total_score']) ?></p>
<p class="text-gray-600"><?= e((string) $session['percent']) ?>%</p>
<?php if ($session['result'] === 'pass'): ?><p class="mt-4 text-sm text-green-700">สามารถออกใบเซอร์ได้จากระบบผู้ดูแล</p><?php endif; ?>
</section></main></body></html>

