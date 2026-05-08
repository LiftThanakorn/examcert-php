<?php
declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/src/helpers.php';

$dashboardDesign = __DIR__ . '/views/dashboard/index.html';

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
    <title><?= e(APP_NAME) ?></title>
</head>
<body>
    <main>
        <h1><?= e(APP_NAME) ?></h1>
        <p>ExamCert foundation is ready. Add implementation pages under <code>views/</code>.</p>
    </main>
</body>
</html>
