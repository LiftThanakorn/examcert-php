<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/Dashboard.php';

requireLogin();

$flash = getFlash();
$stats = dashboardStats();
$passRate = $stats['submitted_sessions'] > 0 ? round(($stats['passed_sessions'] / $stats['submitted_sessions']) * 100, 2) : 0;
$projects = recentProjects();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | <?= e(APP_NAME) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
    <?php require __DIR__ . '/_nav.php'; ?>
    <main class="mx-auto max-w-7xl p-6">
        <?php if ($flash): ?>
            <div class="mb-4 rounded border border-green-200 bg-green-50 px-4 py-3 text-green-700"><?= e($flash['message'] ?? '') ?></div>
        <?php endif; ?>

        <div class="mb-6">
            <p class="text-sm font-medium text-orange-700">ExamCert Standalone v1</p>
            <h1 class="text-2xl font-semibold">Dashboard</h1>
        </div>

        <section class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <?php
            $cards = [
                'โครงการทั้งหมด' => $stats['projects'],
                'โครงการที่เปิด' => $stats['active_projects'],
                'ผู้มีสิทธิ์สอบ' => $stats['participants'],
                'ข้อสอบ' => $stats['questions'],
                'ส่งข้อสอบแล้ว' => $stats['submitted_sessions'],
                'สอบผ่าน' => $stats['passed_sessions'],
                'Pass rate' => $passRate . '%',
                'ใบเซอร์' => $stats['certificates'],
            ];
            ?>
            <?php foreach ($cards as $label => $value): ?>
                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-600"><?= e($label) ?></p>
                    <p class="mt-2 text-3xl font-semibold text-orange-700"><?= e((string) $value) ?></p>
                </div>
            <?php endforeach; ?>
        </section>

        <section class="mt-8 grid gap-6 lg:grid-cols-3">
            <div class="rounded-lg border border-gray-200 bg-white p-5">
                <h2 class="mb-4 font-semibold">ทางลัด</h2>
                <div class="grid gap-3">
                    <a class="rounded border border-gray-200 px-4 py-3 hover:bg-gray-50" href="<?= e(BASE_URL) ?>/admin/projects/">จัดการโครงการสอบ</a>
                    <a class="rounded border border-gray-200 px-4 py-3 hover:bg-gray-50" href="<?= e(BASE_URL) ?>/admin/exam-sessions/">ดูผลสอบ</a>
                    <a class="rounded border border-gray-200 px-4 py-3 hover:bg-gray-50" href="<?= e(BASE_URL) ?>/admin/certificates/">ใบเซอร์/เกียรติบัตร</a>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-5 lg:col-span-2">
                <h2 class="mb-4 font-semibold">โครงการล่าสุด</h2>
                <div class="divide-y divide-gray-200">
                    <?php foreach ($projects as $project): ?>
                        <div class="flex items-center justify-between gap-4 py-3">
                            <div>
                                <p class="font-medium"><?= e($project['name']) ?></p>
                                <p class="text-xs text-gray-500"><?= e($project['code'] ?: '-') ?> | <?= e($project['status']) ?></p>
                            </div>
                            <a class="text-sm text-orange-700 hover:underline" href="<?= e(BASE_URL) ?>/admin/projects/detail.php?id=<?= (int) $project['id'] ?>">เปิด</a>
                        </div>
                    <?php endforeach; ?>
                    <?php if (!$projects): ?>
                        <p class="py-6 text-center text-gray-500">ยังไม่มีโครงการสอบ</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
