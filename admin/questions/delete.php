<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Question.php';
requireLogin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validateCsrfToken($_POST['csrf_token'] ?? null)) {
    http_response_code(400);
    exit('Bad request.');
}
$id = (int) ($_POST['id'] ?? 0);
$question = $id > 0 ? getQuestion($id) : null;
$projectId = $question ? (int) $question['project_id'] : 0;
if ($question && deleteQuestion($id)) {
    setFlash('success', 'ลบข้อสอบสำเร็จ');
} else {
    setFlash('error', 'ไม่สามารถลบข้อสอบได้');
}
redirect('admin/questions/?project_id=' . $projectId);

