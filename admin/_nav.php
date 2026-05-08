<?php
$adminName = function_exists('currentAdminName') ? currentAdminName() : '';
?>
<nav class="border-b border-gray-200 bg-white">
    <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-6 py-3">
        <a href="<?= e(BASE_URL) ?>/admin/dashboard.php" class="font-semibold text-gray-900">ExamCert Admin</a>
        <div class="flex flex-wrap items-center gap-3 text-sm">
            <a class="text-gray-700 hover:text-orange-700" href="<?= e(BASE_URL) ?>/admin/projects/">โครงการสอบ</a>
            <a class="text-gray-700 hover:text-orange-700" href="<?= e(BASE_URL) ?>/admin/exam-sessions/">ผลสอบ</a>
            <a class="text-gray-700 hover:text-orange-700" href="<?= e(BASE_URL) ?>/admin/certificates/">ใบเซอร์</a>
            <a class="text-gray-700 hover:text-orange-700" href="<?= e(BASE_URL) ?>/admin/reports/">Reports</a>
            <span class="text-gray-400">|</span>
            <span class="text-gray-500"><?= e($adminName) ?></span>
            <a class="text-red-700 hover:underline" href="<?= e(BASE_URL) ?>/admin/logout.php">ออกจากระบบ</a>
        </div>
    </div>
</nav>
