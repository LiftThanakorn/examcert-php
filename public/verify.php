<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/Certificate.php';
$token = trim((string) ($_GET['token'] ?? ''));
$certificate = $token !== '' ? getCertificateByToken($token) : null;
?>
<!DOCTYPE html><html lang="th"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Verify Certificate | <?= e(APP_NAME) ?></title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-gray-50 text-gray-900"><main class="max-w-2xl mx-auto p-6">
<section class="rounded-lg border border-gray-200 bg-white p-8 text-center">
<?php if (!$certificate): ?><h1 class="text-2xl font-semibold text-red-700">ไม่พบใบเซอร์</h1><p class="mt-2 text-gray-600">token ไม่ถูกต้องหรือใบเซอร์ถูกลบ</p>
<?php elseif ((int) $certificate['is_revoked'] === 1): ?><h1 class="text-2xl font-semibold text-red-700">ใบเซอร์ถูกยกเลิก</h1><p class="mt-2"><?= e($certificate['revoke_reason'] ?? '') ?></p>
<?php else: $name = trim(($certificate['title'] ? $certificate['title'] . ' ' : '') . $certificate['first_name'] . ' ' . $certificate['last_name']); ?>
<p class="text-green-700">ตรวจสอบสำเร็จ</p><h1 class="mt-2 text-2xl font-semibold"><?= e($certificate['cert_number']) ?></h1><p class="mt-4 text-gray-600">มอบให้</p><h2 class="text-xl font-semibold"><?= e($name) ?></h2><p class="mt-4">โครงการ <?= e($certificate['project_name']) ?></p><p class="text-sm text-gray-600">คะแนน <?= e((string) $certificate['percent']) ?>% | วันที่ออก <?= e($certificate['issued_date']) ?></p>
<?php endif; ?>
</section></main></body></html>

