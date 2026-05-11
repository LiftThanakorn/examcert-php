<?php
declare(strict_types=1);

/**
 * ExamCert Database Installation & Maintenance Tool
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$lockFile = ROOT_PATH . '/logs/database-installed.lock';
$schemaFile = ROOT_PATH . '/database/schema.sql';

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

// 2. Initial Form
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    setupPage(
        'ExamCert Database Setup',
        '<h1>ExamCert Database Setup & Maintenance</h1>
        <p>เครื่องมือนี้ใช้สำหรับตั้งค่าฐานข้อมูลบน Production</p>
        <hr>
        <div style="background:#fff7ed; border:1px solid #ffedd5; padding:20px; border-radius:12px; margin-bottom:24px;">
            <h3 class="warn" style="margin-top:0;">ตัวเลือกที่ 1: ติดตั้งใหม่ / ล้างข้อมูลเดิม (Clean Install)</h3>
            <p>ระบบจะ <strong>ลบตารางเดิมทิ้งทั้งหมด</strong> แล้วสร้างใหม่ตาม schema.sql และนำเข้าข้อมูลเริ่มต้น</p>
            <form method="post">
                <input type="hidden" name="action" value="clean_install">
                <button class="btn" style="background:#dc2626;" onclick="return confirm(\'คุณแน่ใจหรือไม่? ข้อมูลเดิมทั้งหมดจะหายไปและไม่สามารถกู้คืนได้!\')">ล้างข้อมูลและติดตั้งใหม่</button>
            </form>
        </div>

        <div style="background:#f0f9ff; border:1px solid #e0f2fe; padding:20px; border-radius:12px;">
            <h3 style="margin-top:0; color:#0369a1;">ตัวเลือกที่ 2: ตรวจสอบและอัปเดต (Update/Migration)</h3>
            <p>ระบบจะสร้างตารางที่ยังไม่มี และเพิ่มคอลัมน์ใหม่ โดย <strong>ไม่ลบข้อมูลเดิม</strong></p>
            <form method="post">
                <input type="hidden" name="action" value="update">
                <button class="btn" style="background:#0369a1;">อัปเดตโครงสร้างเดิม</button>
            </form>
        </div>'
    );
}

// 3. Execution Logic
try {
    $action = (string) ($_POST['action'] ?? '');
    
    // Connect to MySQL without dbname first
    $dsn = sprintf('mysql:host=%s;charset=%s', DB_HOST, DB_CHARSET);
    $db = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    
    $log = [];

    // --- STEP 0: Clean Install (Drop Tables) ---
    if ($action === 'clean_install') {
        $log[] = "[PROCESS] Starting Clean Install (Dropping existing tables)...";
        $db->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`");
        $db->exec("USE `" . DB_NAME . "`");
        
        $tablesToDrop = ['certificates', 'answer_logs', 'exam_sessions', 'questions', 'participants', 'projects', 'cert_templates', 'admins'];
        $db->exec("SET FOREIGN_KEY_CHECKS = 0");
        foreach ($tablesToDrop as $tbl) {
            $db->exec("DROP TABLE IF EXISTS `$tbl` ");
            $log[] = "[OK] Dropped table `$tbl` (if existed)";
        }
        $db->exec("SET FOREIGN_KEY_CHECKS = 1");
    }

    // --- STEP 1: Run Schema.sql (CREATE TABLE IF NOT EXISTS) ---
    if (!is_file($schemaFile)) {
        throw new Exception("ไม่พบไฟล์ schema.sql ที่ " . $schemaFile);
    }
    $sql = file_get_contents($schemaFile);
    $sql = preg_replace('/--.*$/m', '', $sql);
    $sql = preg_replace('/#.*$/m', '', $sql);
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $stmt) {
        if ($stmt === '') continue;
        try {
            $db->exec($stmt);
            $log[] = "[OK] SQL Statement executed.";
        } catch (PDOException $e) {
            $log[] = "[ERR] Statement failed: " . $e->getMessage() . " | SQL: " . substr($stmt, 0, 50) . "...";
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
        try {
            $stmt = $db->query("SHOW COLUMNS FROM `$table` ");
            $existingCols = array_map(fn($r) => $r['Field'], $stmt->fetchAll());
            
            foreach ($cols as $colName => $alterSql) {
                if (!in_array($colName, $existingCols)) {
                    $db->exec($alterSql);
                    $log[] = "[MIGRATION] Added column '$colName' to table '$table'";
                }
            }
        } catch (Exception $e) {}
    }

    // --- STEP 3: Import Data Export (if exists) ---
    $dataExportFile = ROOT_PATH . '/database/data_export.sql';
    if (is_file($dataExportFile)) {
        $log[] = "[PROCESS] Found data_export.sql, starting import...";
        $dataSql = file_get_contents($dataExportFile);
        if ($dataSql) {
            $dataSql = preg_replace('/--.*$/m', '', $dataSql);
            $dataStatements = array_filter(array_map('trim', explode(';', $dataSql)));
            
            $dataImported = 0;
            foreach ($dataStatements as $dStmt) {
                if ($dStmt === '') continue;
                try {
                    $db->exec($dStmt);
                    $dataImported++;
                } catch (PDOException $e) {
                    $log[] = "[WARN] Data import skipped: " . substr($dStmt, 0, 50) . "...";
                }
            }
            $log[] = "[OK] Imported $dataImported records from data_export.sql";
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
        <p>ฐานข้อมูลของคุณได้รับการติดตั้งหรือปรับปรุงเรียบร้อยแล้ว</p>
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
