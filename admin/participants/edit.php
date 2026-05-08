<?php
declare(strict_types=1);

require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Project.php';
require_once __DIR__ . '/../../src/Participant.php';

requireLogin();

$id = (int) ($_GET['id'] ?? 0);
$participant = getParticipant($id);
if (!$participant) {
    http_response_code(404);
    exit('Participant not found.');
}

$project = getProject((int) $participant['project_id']);
if (!$project) {
    http_response_code(404);
    exit('Project not found.');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = 'คำขอไม่ถูกต้อง กรุณาลองใหม่';
    } else {
        $result = updateParticipant($id, $_POST);
        if ($result['success']) {
            setFlash('success', 'แก้ไขผู้มีสิทธิ์สอบสำเร็จ');
            redirect('admin/participants/?project_id=' . (int) $project['id']);
        }
        $errors = $result['errors'];
        $participant = array_merge($participant, $_POST);
    }
}

$action = BASE_URL . '/admin/participants/edit.php?id=' . $id;
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขผู้มีสิทธิ์สอบ | <?= e(APP_NAME) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
    <main class="max-w-5xl mx-auto p-6">
        <h1 class="mb-1 text-2xl font-semibold">แก้ไขผู้มีสิทธิ์สอบ</h1>
        <p class="mb-6 text-sm text-gray-600"><?= e($project['name']) ?></p>
        <?php require __DIR__ . '/_form.php'; ?>

        <form method="post" action="<?= e(BASE_URL) ?>/admin/participants/delete.php" class="mt-6" onsubmit="return confirm('ยืนยันลบรายชื่อนี้?');">
            <?= csrfField() ?>
            <input type="hidden" name="id" value="<?= (int) $participant['id'] ?>">
            <button class="rounded border border-red-200 px-4 py-2 text-red-700 hover:bg-red-50">ลบรายชื่อ</button>
        </form>
    </main>
</body>
</html>

