<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Certificate.php';
requireLogin();
$certificates = getCertificates();
$flash = getFlash();
?>
<!DOCTYPE html><html lang="th"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>ใบเซอร์ | <?= e(APP_NAME) ?></title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-gray-50 text-gray-900"><main class="max-w-7xl mx-auto p-6"><h1 class="mb-6 text-2xl font-semibold">ใบเซอร์/เกียรติบัตร</h1>
<?php if ($flash): ?><div class="mb-4 rounded border border-green-200 bg-green-50 px-4 py-3 text-green-700"><?= e($flash['message'] ?? '') ?></div><?php endif; ?>
<div class="overflow-x-auto rounded-lg border border-gray-200 bg-white"><table class="min-w-full divide-y divide-gray-200 text-sm"><thead class="bg-gray-100 text-left"><tr><th class="px-4 py-3">เลขที่</th><th class="px-4 py-3">ผู้รับ</th><th class="px-4 py-3">โครงการ</th><th class="px-4 py-3">วันที่ออก</th><th class="px-4 py-3">Verify</th></tr></thead><tbody class="divide-y divide-gray-200">
<?php foreach ($certificates as $cert): ?><tr><td class="px-4 py-3"><?= e($cert['cert_number']) ?></td><td class="px-4 py-3"><?= e($cert['participant_name']) ?></td><td class="px-4 py-3"><?= e($cert['project_name']) ?></td><td class="px-4 py-3"><?= e($cert['issued_date']) ?></td><td class="px-4 py-3"><a class="text-orange-700 hover:underline" href="<?= e($cert['verify_url']) ?>" target="_blank">ตรวจสอบ</a></td></tr><?php endforeach; ?>
<?php if (!$certificates): ?><tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">ยังไม่มีใบเซอร์</td></tr><?php endif; ?>
</tbody></table></div></main></body></html>

