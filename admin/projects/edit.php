<?php
declare(strict_types=1);

require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Project.php';

requireLogin();

$id = (int) ($_GET['id'] ?? 0);
$project = getProject($id);
if (!$project) {
    http_response_code(404);
    exit('Project not found.');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = 'คำขอไม่ถูกต้อง กรุณาลองใหม่';
    } else {
        $result = updateProject($id, $_POST);
        if ($result['success']) {
            setFlash('success', 'แก้ไขโครงการสอบสำเร็จ');
            redirect('admin/projects/detail.php?id=' . $id);
        }
        $errors = $result['errors'];
        $project = array_merge($project, $_POST);
    }
}

$action = BASE_URL . '/admin/projects/edit.php?id=' . $id;
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขโครงการสอบ | <?= e(APP_NAME) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
    <main class="max-w-5xl mx-auto p-6">
        <h1 class="mb-6 text-2xl font-semibold">แก้ไขโครงการสอบ</h1>
        <?php require __DIR__ . '/_form.php'; ?>
    </main>
</body>
</html>

