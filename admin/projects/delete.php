<?php
declare(strict_types=1);

require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Project.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validateCsrfToken($_POST['csrf_token'] ?? null)) {
    http_response_code(400);
    exit('Bad request.');
}

$id = (int) ($_POST['id'] ?? 0);
if ($id > 0 && deleteProject($id)) {
    setFlash('success', 'ลบโครงการสอบสำเร็จ');
} else {
    setFlash('error', 'ไม่สามารถลบโครงการสอบได้');
}

redirect('admin/projects/');

