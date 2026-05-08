<?php
declare(strict_types=1);

require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Project.php';
require_once __DIR__ . '/../../src/Question.php';

requireLogin();

$projectId = (int) ($_GET['project_id'] ?? 0);
$project = getProject($projectId);
if (!$project) {
    http_response_code(404);
    exit('Project not found.');
}

$questions = getQuestionsByProject($projectId);
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คลังข้อสอบ | <?= e(APP_NAME) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
    <main class="max-w-7xl mx-auto p-6">
        <div class="mb-6 flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold">คลังข้อสอบ</h1>
                <p class="text-sm text-gray-600"><?= e($project['name']) ?></p>
            </div>
            <div class="flex gap-2">
                <a href="<?= e(BASE_URL) ?>/admin/projects/detail.php?id=<?= (int) $projectId ?>" class="rounded border border-gray-200 px-4 py-2">กลับโครงการ</a>
                <a href="<?= e(BASE_URL) ?>/admin/questions/create.php?project_id=<?= (int) $projectId ?>" class="rounded bg-orange-600 px-4 py-2 text-white">เพิ่มข้อสอบ</a>
            </div>
        </div>
        <?php if ($flash): ?>
            <div class="mb-4 rounded border border-green-200 bg-green-50 px-4 py-3 text-green-700"><?= e($flash['message'] ?? '') ?></div>
        <?php endif; ?>
        <div class="rounded-lg border border-gray-200 bg-white">
            <?php foreach ($questions as $question): ?>
                <div class="border-b border-gray-200 p-4">
                    <div class="flex justify-between gap-4">
                        <div>
                            <p class="font-medium"><?= e($question['question_text']) ?></p>
                            <p class="mt-1 text-xs text-gray-500"><?= e($question['type']) ?> | <?= e($question['difficulty']) ?> | <?= e((string) $question['score_weight']) ?> คะแนน</p>
                        </div>
                        <a class="text-orange-700 hover:underline" href="<?= e(BASE_URL) ?>/admin/questions/edit.php?id=<?= (int) $question['id'] ?>">แก้ไข</a>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (!$questions): ?>
                <div class="p-8 text-center text-gray-500">ยังไม่มีข้อสอบ</div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

