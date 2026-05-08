<?php
declare(strict_types=1);

require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Project.php';

requireLogin();

$project = projectDefaults();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = 'คำขอไม่ถูกต้อง กรุณาลองใหม่';
    } else {
        $result = createProject($_POST, currentAdminId());
        if ($result['success']) {
            setFlash('success', 'สร้างโครงการสอบสำเร็จ');
            redirect('admin/projects/detail.php?id=' . (int) $result['id']);
        }
        $errors = $result['errors'];
        $project = array_merge($project, $_POST);
    }
}

$action = BASE_URL . '/admin/projects/create.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สร้างโครงการสอบ | <?= e(APP_NAME) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
    <main class="max-w-5xl mx-auto p-6">
        <h1 class="mb-6 text-2xl font-semibold">สร้างโครงการสอบ</h1>
        <?php require __DIR__ . '/_form.php'; ?>
    </main>
</body>
</html>

