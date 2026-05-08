<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | <?= e(APP_NAME) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
    <main class="mx-auto max-w-xl p-6">
        <h1 class="mb-2 text-2xl font-semibold">เข้าสู่ระบบทำข้อสอบ</h1>
        <?php if ($project): ?>
            <p class="mb-6 text-gray-600"><?= e($project['name']) ?></p>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-red-700"><?= e($error) ?></div>
        <?php endif; ?>
        <form method="post" class="space-y-5 rounded-lg border border-gray-200 bg-white p-6">
            <?= csrfField() ?>
            <div>
                <label class="mb-2 block text-sm font-medium">รหัสโครงการหรือ ID</label>
                <input name="project" required value="<?= e($code) ?>" class="w-full rounded border border-gray-200 px-3 py-2">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium">Access Token</label>
                <input name="access_token" required class="w-full rounded border border-gray-200 px-3 py-2">
            </div>
            <button class="rounded bg-orange-600 px-4 py-2 text-white">เริ่มทำข้อสอบ</button>
        </form>
    </main>
</body>
</html>
