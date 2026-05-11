<?php
declare(strict_types=1);

require_once __DIR__ . '/config/config.php';

$lockFile = ROOT_PATH . '/logs/database-installed.lock';
$schemaFile = ROOT_PATH . '/database/schema.sql';
$requestToken = (string) ($_GET['token'] ?? $_POST['token'] ?? '');
$isAllowedToken = SETUP_WEB_TOKEN !== 'CHANGE_THIS_SETUP_TOKEN'
    && $requestToken !== ''
    && hash_equals(SETUP_WEB_TOKEN, $requestToken);

function setupPage(string $title, string $body, int $statusCode = 200): never
{
    http_response_code($statusCode);
    echo '<!DOCTYPE html><html lang="th"><head><meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<title>' . e($title) . '</title>';
    echo '<style>body{font-family:Arial,sans-serif;background:#f8fafc;color:#111827;margin:0;padding:40px}.card{max-width:760px;margin:auto;background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:28px;box-shadow:0 10px 30px rgba(15,23,42,.08)}.ok{color:#047857}.err{color:#b91c1c}.btn{background:#E87722;color:#fff;border:0;border-radius:8px;padding:10px 16px;font-weight:700;cursor:pointer}code,pre{background:#f3f4f6;border-radius:6px;padding:2px 6px}pre{padding:12px;overflow:auto;white-space:pre-wrap}</style>';
    echo '</head><body><main class="card">' . $body . '</main></body></html>';
    exit;
}

if (!$isAllowedToken) {
    setupPage(
        'Setup denied',
        '<h1 class="err">ไม่อนุญาตให้รัน Setup</h1><p>กรุณาตั้งค่า <code>SETUP_WEB_TOKEN</code> ใน <code>config/config.php</code> ให้เป็นค่าลับก่อน แล้วเปิด URL พร้อม <code>?token=...</code></p>',
        403
    );
}

if (is_file($lockFile)) {
    setupPage(
        'Database already installed',
        '<h1 class="ok">ฐานข้อมูลถูกติดตั้งแล้ว</h1><p>พบ lock file: <code>logs/database-installed.lock</code></p><p>ถ้าต้องการรันใหม่ ให้ลบ lock file นี้เองหลังสำรองข้อมูลแล้วเท่านั้น</p>'
    );
}

if (!is_file($schemaFile)) {
    setupPage('Schema missing', '<h1 class="err">ไม่พบไฟล์ schema</h1><p>ไม่พบ <code>database/schema.sql</code></p>', 500);
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    setupPage(
        'Install Database',
        '<h1>ติดตั้งฐานข้อมูล ExamCert</h1><p>ไฟล์นี้จะรัน <code>database/schema.sql</code> ด้วยค่าใน <code>config/database.php</code></p><p class="err">หลังติดตั้งสำเร็จ ให้ลบไฟล์ <code>install-database.php</code> ออกจาก server ทันที</p><form method="post"><input type="hidden" name="token" value="' . e($requestToken) . '"><button class="btn">Run Database Setup</button></form>'
    );
}

try {
    $dsn = sprintf('mysql:host=%s;charset=%s', DB_HOST, DB_CHARSET);
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $sql = file_get_contents($schemaFile);
    if ($sql === false) {
        throw new RuntimeException('Cannot read schema.sql');
    }

    $lines = preg_split('/\R/', $sql) ?: [];
    $sqlWithoutComments = implode("\n", array_filter($lines, static function (string $line): bool {
        $trimmed = trim($line);
        return $trimmed !== '' && !str_starts_with($trimmed, '--') && !str_starts_with($trimmed, '#');
    }));

    $statements = array_filter(array_map('trim', explode(';', $sqlWithoutComments)));
    $executed = 0;
    $log = [];

    foreach ($statements as $statement) {
        $pdo->exec($statement);
        $executed++;
        $log[] = '[OK] ' . substr(preg_replace('/\s+/', ' ', $statement) ?? $statement, 0, 90);
    }

    if (!is_dir(LOGS_PATH)) {
        mkdir(LOGS_PATH, 0755, true);
    }
    file_put_contents($lockFile, 'Installed at ' . date('Y-m-d H:i:s') . PHP_EOL, LOCK_EX);

    setupPage(
        'Database installed',
        '<h1 class="ok">ติดตั้งฐานข้อมูลสำเร็จ</h1><p>Executed statements: <strong>' . $executed . '</strong></p><pre>' . e(implode("\n", $log)) . '</pre><p class="err">ขั้นตอนต่อไป: ลบ <code>install-database.php</code> ออกจาก server และเปลี่ยน <code>SETUP_WEB_TOKEN</code></p>'
    );
} catch (Throwable $e) {
    logError('Web database setup failed', ['error' => $e->getMessage()]);
    setupPage('Setup failed', '<h1 class="err">ติดตั้งไม่สำเร็จ</h1><p>' . e($e->getMessage()) . '</p>', 500);
}
