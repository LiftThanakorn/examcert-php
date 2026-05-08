<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/Exam.php';

$sessionId = (int) ($_GET['session_id'] ?? $_POST['session_id'] ?? 0);
$session = getExamSession($sessionId);
if (!$session) { http_response_code(404); exit('Session not found.'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
        exit('Bad request.');
    }
    $result = submitExamSession($sessionId, $_POST['answers'] ?? []);
    if ($result['success']) {
        redirect('public/result.php?session_id=' . $sessionId);
    }
    exit(e($result['message']));
}

if ($session['status'] !== 'in_progress') {
    redirect('public/result.php?session_id=' . $sessionId);
}

$project = getProject((int) $session['project_id']);
$questions = getSessionQuestions($session);
?>
<!DOCTYPE html>
<html lang="th"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>ทำข้อสอบ | <?= e(APP_NAME) ?></title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-gray-50 text-gray-900"><main class="max-w-4xl mx-auto p-6">
<h1 class="mb-2 text-2xl font-semibold"><?= e($project['name'] ?? 'Exam') ?></h1>
<p class="mb-6 text-sm text-gray-600">หมดเวลา: <?= e($session['expires_at']) ?></p>
<form method="post" class="space-y-5"><?= csrfField() ?><input type="hidden" name="session_id" value="<?= (int) $sessionId ?>">
<?php foreach ($questions as $index => $question): $choices = json_decode((string) $question['choices'], true) ?: []; ?>
<section class="rounded-lg border border-gray-200 bg-white p-5">
<h2 class="mb-4 font-semibold">ข้อ <?= $index + 1 ?>. <?= e($question['question_text']) ?></h2>
<?php if ($question['type'] === 'fill_blank'): ?>
<input name="answers[<?= (int) $question['id'] ?>]" class="w-full rounded border border-gray-200 px-3 py-2">
<?php else: foreach ($choices as $choice): ?>
<label class="mb-2 flex gap-2"><input type="radio" name="answers[<?= (int) $question['id'] ?>]" value="<?= e((string) $choice['key']) ?>"> <span><?= e((string) $choice['text']) ?></span></label>
<?php endforeach; endif; ?>
</section>
<?php endforeach; ?>
<button class="rounded bg-orange-600 px-5 py-2 text-white" onclick="return confirm('ยืนยันส่งข้อสอบ?');">ส่งข้อสอบ</button>
</form></main></body></html>

