<?php
declare(strict_types=1);

/**
 * ExamCert Database Installation & Maintenance Tool
 * Use this to setup or update your database schema on production.
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

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
    echo '<title>' . htmlspecialchars($title) . '</title>';
    echo '<style>body{font-family:system-ui,-apple-system,sans-serif;background:#f8fafc;color:#111827;margin:0;padding:40px}.card{max-width:800px;margin:auto;background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:32px;box-shadow:0 10px 30px rgba(15,23,42,.08)}.ok{color:#059669;font-weight:700}.err{color:#dc2626;font-weight:700}.warn{color:#d97706}.btn{background:#E87722;color:#fff;border:0;border-radius:10px;padding:12px 24px;font-weight:700;cursor:pointer;transition:all 0.2s}.btn:hover{background:#d66a1a;transform:translateY(-1px)}code,pre{background:#f1f5f9;border-radius:8px;padding:4px 8px;font-size:0.9em}pre{padding:16px;overflow:auto;white-space:pre-wrap;border:1px solid #e2e8f0}hr{border:0;border-top:1px solid #f1f5f9;margin:24px 0}</style>';
    echo '</head><body><main class="card">' . $body . '</main></body></html>';
    exit;
}

// 1. Security Check
if (!$isAllowedToken) {
    setupPage(
        'Setup Denied',
        '<h1 class="err">ไม่อนุญาตให้เข้าถึง</h1><p>กรุณาตั้งค่า <code>SETUP_WEB_TOKEN</code> ใน <code>config/config.php</code> ให้เป็นความลับก่อน แล้วเปิด URL พร้อม <code>?token=YOUR_TOKEN</code></p>',
        403
    );
}

// 2. Initial Form
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    $statusMsg = is_file($lockFile) ? '<p class="ok">● ระบบเคยติดตั้งไปแล้ว (พบ Lock File)</p><p>คุณสามารถรันเพื่อ <strong>ตรวจสอบและอัปเดตโครงสร้าง (Update/Migration)</strong> ให้เป็นเวอร์ชันล่าสุดได้</p>' 
                                  : '<p class="warn">● ยังไม่มีการติดตั้งระบบนี้</p>';
    
    setupPage(
        'ExamCert Database Setup',
        '<h1>ExamCert Database Setup & Maintenance</h1>' . $statusMsg . '
        <hr>
        <p>เครื่องมือนี้จะดำเนินการดังนี้:</p>
        <ul>
            <li>สร้างตารางที่ยังไม่มี (Create Tables)</li>
            <li>เพิ่มคอลัมน์ที่ขาดหายไป (Update Columns/Migration)</li>
            <li>อัปเดตข้อมูลเทมเพลตเริ่มต้น</li>
        </ul>
        <form method="post">
            <input type="hidden" name="token" value="' . htmlspecialchars($requestToken) . '">
            <button class="btn">ดำเนินการติดตั้ง / อัปเดตโครงสร้าง</button>
        </form>
        <p style="margin-top:20px; font-size:0.85em; color:#64748b;">* โปรดสำรองข้อมูลฐานข้อมูลเดิมก่อนดำเนินการทุกครั้ง</p>'
    );
}

// 3. Execution Logic
try {
    $db = getDB();
    $log = [];

    // --- STEP 1: Run Schema.sql (CREATE TABLE IF NOT EXISTS) ---
    if (!is_file($schemaFile)) {
        throw new Exception("ไม่พบไฟล์ schema.sql ที่ " . $schemaFile);
    }
    $sql = file_get_contents($schemaFile);
    // Remove comments
    $sql = preg_replace('/--.*$/m', '', $sql);
    $sql = preg_replace('/#.*$/m', '', $sql);
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $stmt) {
        if ($stmt === '') continue;
        try {
            $db->exec($stmt);
            $log[] = "[OK] SQL Statement executed.";
        } catch (PDOException $e) {
            $log[] = "[INFO] Statement skipped or already exists: " . substr($stmt, 0, 50) . "...";
        }
    }

    // --- STEP 2: Custom Migrations (Adding columns if missing) ---
    $migrations = [
        'certificates' => [
            'is_revoked' => "ALTER TABLE certificates ADD COLUMN is_revoked TINYINT(1) DEFAULT 0 AFTER verify_token",
            'revoke_reason' => "ALTER TABLE certificates ADD COLUMN revoke_reason TEXT NULL AFTER is_revoked",
        ],
        'cert_templates' => [
            'bg_color' => "ALTER TABLE cert_templates ADD COLUMN bg_color VARCHAR(7) DEFAULT '#FFFFFF' AFTER bg_type",
            'bg_image' => "ALTER TABLE cert_templates ADD COLUMN bg_image VARCHAR(255) AFTER bg_color",
            'orientation' => "ALTER TABLE cert_templates ADD COLUMN orientation ENUM('L','P') DEFAULT 'L' AFTER name",
        ]
    ];

    foreach ($migrations as $table => $cols) {
        $stmt = $db->query("SHOW COLUMNS FROM `$table` ");
        $existingCols = array_map(fn($r) => $r['Field'], $stmt->fetchAll());
        
        foreach ($cols as $colName => $alterSql) {
            if (!in_array($colName, $existingCols)) {
                $db->exec($alterSql);
                $log[] = "[MIGRATION] Added column '$colName' to table '$table'";
            }
        }
    }

    // Create lock file if not exists
    if (!is_file($lockFile)) {
        if (!is_dir(dirname($lockFile))) mkdir(dirname($lockFile), 0755, true);
        file_put_contents($lockFile, 'Installed/Updated at ' . date('Y-m-d H:i:s'));
    }

    setupPage(
        'Setup Successful',
        '<h1 class="ok">ดำเนินการเสร็จสมบูรณ์</h1>
        <p>ฐานข้อมูลของคุณได้รับการติดตั้งหรือปรับปรุงเป็นเวอร์ชันล่าสุดแล้ว</p>
        <pre>' . htmlspecialchars(implode("\n", $log)) . '</pre>
        <hr>
        <p class="err">คำเตือน: โปรดลบไฟล์ <code>install-database.php</code> ออกจาก Server ทันทีหลังจากนี้</p>
        <a href="' . BASE_URL . '/admin" class="btn" style="text-decoration:none; display:inline-block;">ไปหน้า Admin</a>'
    );

} catch (Throwable $e) {
    setupPage(
        'Setup Error',
        '<h1 class="err">เกิดข้อผิดพลาด</h1>
        <p>' . htmlspecialchars($e->getMessage()) . '</p>
        <hr>
        <a href="?" class="btn">ลองใหม่อีกครั้ง</a>',
        500
    );
}
