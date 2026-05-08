<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/Auth.php';

requireLogin();

$flash = getFlash();
$dashboardDesign = __DIR__ . '/../views/dashboard/index.html';

if (is_file($dashboardDesign)) {
    readfile($dashboardDesign);
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | <?= e(APP_NAME) ?></title>
</head>
<body>
    <?php if ($flash): ?>
        <p><?= e($flash['message'] ?? '') ?></p>
    <?php endif; ?>
    <h1>Dashboard</h1>
    <p>ยินดีต้อนรับ <?= e(currentAdminName()) ?></p>
    <p><a href="<?= e(BASE_URL) ?>/admin/logout.php">ออกจากระบบ</a></p>
</body>
</html>

