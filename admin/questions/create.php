<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Project.php';
require_once __DIR__ . '/../../src/Question.php';
requireLogin();
$projectId = (int) ($_GET['project_id'] ?? 0);
$project = getProject($projectId);
if (!$project) { http_response_code(404); exit('Project not found.'); }
$question = questionDefaults();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = 'คำขอไม่ถูกต้อง กรุณาลองใหม่';
    } else {
        $result = createQuestion($projectId, $_POST, currentAdminId());
        if ($result['success']) {
            setFlash('success', 'เพิ่มข้อสอบสำเร็จ');
            redirect('admin/questions/?project_id=' . $projectId);
        }
        $errors = $result['errors'];
        $question = array_merge($question, $_POST);
    }
}
$action = BASE_URL . '/admin/questions/create.php?project_id=' . $projectId;
?>
<!DOCTYPE html><html lang="th"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>เพิ่มข้อสอบ | <?= e(APP_NAME) ?></title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-gray-50 text-gray-900"><main class="max-w-5xl mx-auto p-6"><h1 class="mb-1 text-2xl font-semibold">เพิ่มข้อสอบ</h1><p class="mb-6 text-sm text-gray-600"><?= e($project['name']) ?></p><?php require __DIR__ . '/_form.php'; ?></main></body></html>

