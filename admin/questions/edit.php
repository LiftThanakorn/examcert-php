<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Project.php';
require_once __DIR__ . '/../../src/Question.php';
requireLogin();
$id = (int) ($_GET['id'] ?? 0);
$questionRow = getQuestion($id);
if (!$questionRow) { http_response_code(404); exit('Question not found.'); }
$project = getProject((int) $questionRow['project_id']);
if (!$project) { http_response_code(404); exit('Project not found.'); }
$question = decodeQuestionForForm($questionRow);
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = 'คำขอไม่ถูกต้อง กรุณาลองใหม่';
    } else {
        $result = updateQuestion($id, $_POST);
        if ($result['success']) {
            setFlash('success', 'แก้ไขข้อสอบสำเร็จ');
            redirect('admin/questions/?project_id=' . (int) $project['id']);
        }
        $errors = $result['errors'];
        $question = array_merge($question, $_POST);
    }
}
$action = BASE_URL . '/admin/questions/edit.php?id=' . $id;
?>
<!DOCTYPE html><html lang="th"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>แก้ไขข้อสอบ | <?= e(APP_NAME) ?></title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-gray-50 text-gray-900"><main class="max-w-5xl mx-auto p-6"><h1 class="mb-1 text-2xl font-semibold">แก้ไขข้อสอบ</h1><p class="mb-6 text-sm text-gray-600"><?= e($project['name']) ?></p><?php require __DIR__ . '/_form.php'; ?>
<form method="post" action="<?= e(BASE_URL) ?>/admin/questions/delete.php" class="mt-6" onsubmit="return confirm('ยืนยันลบข้อสอบนี้?');"><?= csrfField() ?><input type="hidden" name="id" value="<?= (int) $id ?>"><button class="rounded border border-red-200 px-4 py-2 text-red-700 hover:bg-red-50">ลบข้อสอบ</button></form></main></body></html>

