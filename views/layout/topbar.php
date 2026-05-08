<header class="bg-white border-b border-gray-200 sticky top-0 z-10">
    <div class="px-6 py-4 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900"><?= e($pageTitle ?? APP_NAME) ?></h1>
            <p class="text-sm text-gray-600">ระบบทำข้อสอบและออกใบเซอร์</p>
        </div>
        <div class="flex items-center gap-3 text-sm">
            <span class="text-gray-600"><?= e(function_exists('currentAdminName') ? currentAdminName() : '') ?></span>
            <a href="<?= e(BASE_URL) ?>/admin/logout.php" class="rounded border border-danger-600 px-3 py-2 text-danger-600 hover:bg-danger-50">ออกจากระบบ</a>
        </div>
    </div>
</header>

