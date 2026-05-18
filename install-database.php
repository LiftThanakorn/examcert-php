<?php
declare(strict_types=1);

/**
 * ExamCert Database Installation & Maintenance Tool
 *
 * Use this file only for local/staging setup, then remove it from the web root.
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$schemaFile = ROOT_PATH . '/database/schema.sql';
$dataExportFile = ROOT_PATH . '/database/data_export.sql';
$lockFile = ROOT_PATH . '/logs/database-installed.lock';

function installerEscape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function installerIdentifier(string $identifier): string
{
    if (!preg_match('/^[A-Za-z0-9_]+$/', $identifier)) {
        throw new RuntimeException('Invalid database identifier: ' . $identifier);
    }

    return '`' . str_replace('`', '``', $identifier) . '`';
}

function installerRender(string $title, string $body, int $statusCode = 200): never
{
    http_response_code($statusCode);
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<title>' . installerEscape($title) . '</title>';
    echo '<style>
        body{font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;background:#f9f8f6;color:#111827;margin:0;padding:32px}
        .card{max-width:880px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:32px;box-shadow:0 8px 32px rgba(0,0,0,.12)}
        h1{font-size:28px;margin:0 0 8px} h2{font-size:18px;margin:0 0 8px} p{color:#4b5563;line-height:1.6}
        .panel{border:1px solid #e5e7eb;border-radius:14px;padding:20px;margin-top:20px;background:#f9fafb}
        .danger{border-color:#fecaca;background:#fef2f2}.info{border-color:#fed7aa;background:#fff7ed}.ok{color:#059669}.err{color:#dc2626}.muted{color:#6b7280}
        .btn{display:inline-flex;align-items:center;gap:8px;border:0;border-radius:12px;padding:12px 20px;font-weight:700;cursor:pointer;text-decoration:none}
        .btn-primary{background:#E87722;color:#fff}.btn-danger{background:#dc2626;color:#fff}.btn-secondary{background:#0369a1;color:#fff}
        .btn:hover{filter:brightness(.96)} code,pre{background:#f1f5f9;border-radius:8px} code{padding:2px 6px}
        pre{padding:16px;overflow:auto;white-space:pre-wrap;border:1px solid #e2e8f0;color:#334155}
        .row{display:flex;gap:12px;flex-wrap:wrap}.warn{font-weight:700;color:#b45309}
    </style>';
    echo '</head><body><main class="card">' . $body . '</main></body></html>';
    exit;
}

function installerConnectServer(): PDO
{
    $dsn = sprintf('mysql:host=%s;charset=%s', DB_HOST, DB_CHARSET);
    return new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}

function installerPrepareDatabase(PDO $db, array &$log): void
{
    $dbName = installerIdentifier(DB_NAME);
    $charset = preg_match('/^[A-Za-z0-9_]+$/', DB_CHARSET) ? DB_CHARSET : 'utf8mb4';
    $db->exec("CREATE DATABASE IF NOT EXISTS {$dbName} DEFAULT CHARACTER SET {$charset} COLLATE {$charset}_unicode_ci");
    $db->exec("USE {$dbName}");
    $db->exec("SET NAMES {$charset}");
    $db->exec("SET time_zone = '+07:00'");
    $log[] = '[OK] Database ready: ' . DB_NAME;
}

function installerStripSchemaDatabaseLines(string $sql): string
{
    $lines = preg_split('/\R/', $sql) ?: [];
    $kept = array_filter($lines, static function (string $line): bool {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '--')) {
            return false;
        }
        if (preg_match('/^CREATE\s+DATABASE\b/i', $trimmed)) {
            return false;
        }
        if (preg_match('/^USE\s+`?[^`;]+`?\s*;?$/i', $trimmed)) {
            return false;
        }
        return true;
    });

    return implode("\n", $kept);
}

function installerSplitSql(string $sql): array
{
    $statements = [];
    $buffer = '';
    $quote = null;
    $length = strlen($sql);

    for ($i = 0; $i < $length; $i++) {
        $char = $sql[$i];
        $next = $i + 1 < $length ? $sql[$i + 1] : '';
        $buffer .= $char;

        if ($quote !== null) {
            if ($char === '\\' && $next !== '') {
                $i++;
                $buffer .= $next;
                continue;
            }
            if ($char === $quote) {
                $quote = null;
            }
            continue;
        }

        if ($char === "'" || $char === '"' || $char === '`') {
            $quote = $char;
            continue;
        }

        if ($char === ';') {
            $statement = trim(substr($buffer, 0, -1));
            if ($statement !== '') {
                $statements[] = $statement;
            }
            $buffer = '';
        }
    }

    $tail = trim($buffer);
    if ($tail !== '') {
        $statements[] = $tail;
    }

    return $statements;
}

function installerRunSqlFile(PDO $db, string $file, array &$log, bool $continueOnError = true): void
{
    if (!is_file($file)) {
        throw new RuntimeException('SQL file not found: ' . $file);
    }

    $sql = file_get_contents($file);
    if ($sql === false) {
        throw new RuntimeException('Cannot read SQL file: ' . $file);
    }

    $sql = installerStripSchemaDatabaseLines($sql);
    $statements = installerSplitSql($sql);
    $executed = 0;

    foreach ($statements as $statement) {
        try {
            $db->exec($statement);
            $executed++;
        } catch (PDOException $e) {
            $preview = preg_replace('/\s+/', ' ', substr($statement, 0, 120));
            $log[] = '[WARN] SQL skipped: ' . $e->getMessage() . ' | ' . $preview;
            if (!$continueOnError) {
                throw $e;
            }
        }
    }

    $log[] = '[OK] Executed ' . $executed . ' statements from ' . basename($file);
}

function installerTableExists(PDO $db, string $table): bool
{
    $stmt = $db->prepare('
        SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?
    ');
    $stmt->execute([$table]);
    return (int) $stmt->fetchColumn() > 0;
}

function installerColumnExists(PDO $db, string $table, string $column): bool
{
    $stmt = $db->prepare('
        SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?
    ');
    $stmt->execute([$table, $column]);
    return (int) $stmt->fetchColumn() > 0;
}

function installerAddColumn(PDO $db, string $table, string $column, string $alterSql, array &$log): void
{
    if (!installerTableExists($db, $table)) {
        $log[] = '[WARN] Table not found for migration: ' . $table;
        return;
    }

    if (installerColumnExists($db, $table, $column)) {
        $log[] = '[SKIP] Column exists: ' . $table . '.' . $column;
        return;
    }

    $db->exec($alterSql);
    $log[] = '[MIGRATION] Added column: ' . $table . '.' . $column;
}

function installerEnsureRatingScaleEnum(PDO $db, array &$log): void
{
    if (!installerTableExists($db, 'questions')) {
        $log[] = '[WARN] questions table missing, cannot update type enum';
        return;
    }

    $stmt = $db->prepare('
        SELECT COLUMN_TYPE
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?
        LIMIT 1
    ');
    $stmt->execute(['questions', 'type']);
    $columnType = (string) $stmt->fetchColumn();

    if (str_contains($columnType, 'rating_scale')) {
        $log[] = '[SKIP] questions.type already supports rating_scale';
        return;
    }

    $db->exec("
        ALTER TABLE questions
        MODIFY type ENUM('multiple_choice','true_false','fill_blank','subjective','rating_scale')
        COLLATE utf8mb4_unicode_ci DEFAULT 'multiple_choice'
    ");
    $log[] = '[MIGRATION] Updated questions.type enum with rating_scale';
}

function installerRunMigrations(PDO $db, array &$log): void
{
    installerEnsureRatingScaleEnum($db, $log);

    installerAddColumn($db, 'questions', 'question_image', "ALTER TABLE questions ADD COLUMN question_image VARCHAR(255) NULL AFTER question_text", $log);
    installerAddColumn($db, 'answer_logs', 'grading_status', "ALTER TABLE answer_logs ADD COLUMN grading_status VARCHAR(20) DEFAULT 'auto' AFTER score_earned", $log);

    installerAddColumn($db, 'participants', 'title', "ALTER TABLE participants ADD COLUMN title VARCHAR(30) NULL AFTER project_id", $log);
    installerAddColumn($db, 'projects', 'manual_override', "ALTER TABLE projects ADD COLUMN manual_override TINYINT(1) DEFAULT 0 AFTER exam_end", $log);
    installerAddColumn($db, 'projects', 'warning_before', "ALTER TABLE projects ADD COLUMN warning_before INT DEFAULT 30 AFTER manual_override", $log);
    installerAddColumn($db, 'projects', 'allow_early_login', "ALTER TABLE projects ADD COLUMN allow_early_login TINYINT(1) DEFAULT 0 AFTER warning_before", $log);
    installerAddColumn($db, 'projects', 'auto_submit_on_close', "ALTER TABLE projects ADD COLUMN auto_submit_on_close TINYINT(1) DEFAULT 1 AFTER allow_early_login", $log);
    installerAddColumn($db, 'projects', 'cert_number_prefix', "ALTER TABLE projects ADD COLUMN cert_number_prefix VARCHAR(20) DEFAULT 'CERT' AFTER cert_template_id", $log);
    installerAddColumn($db, 'projects', 'cert_sequence', "ALTER TABLE projects ADD COLUMN cert_sequence INT DEFAULT 1 AFTER cert_number_prefix", $log);

    installerAddColumn($db, 'certificates', 'verify_url', "ALTER TABLE certificates ADD COLUMN verify_url VARCHAR(500) NULL AFTER verify_token", $log);
    installerAddColumn($db, 'certificates', 'is_revoked', "ALTER TABLE certificates ADD COLUMN is_revoked TINYINT(1) DEFAULT 0 AFTER last_downloaded_at", $log);
    installerAddColumn($db, 'certificates', 'revoke_reason', "ALTER TABLE certificates ADD COLUMN revoke_reason TEXT NULL AFTER is_revoked", $log);

    installerAddColumn($db, 'cert_templates', 'orientation', "ALTER TABLE cert_templates ADD COLUMN orientation ENUM('L','P') DEFAULT 'L' AFTER elements", $log);
    installerAddColumn($db, 'cert_templates', 'width_mm', "ALTER TABLE cert_templates ADD COLUMN width_mm DECIMAL(6,2) DEFAULT '297.00' AFTER orientation", $log);
    installerAddColumn($db, 'cert_templates', 'height_mm', "ALTER TABLE cert_templates ADD COLUMN height_mm DECIMAL(6,2) DEFAULT '210.00' AFTER width_mm", $log);
    installerAddColumn($db, 'cert_templates', 'bg_type', "ALTER TABLE cert_templates ADD COLUMN bg_type ENUM('color','image') DEFAULT 'color' AFTER height_mm", $log);
    installerAddColumn($db, 'cert_templates', 'bg_color', "ALTER TABLE cert_templates ADD COLUMN bg_color VARCHAR(7) DEFAULT '#FFFFFF' AFTER bg_type", $log);
    installerAddColumn($db, 'cert_templates', 'layout_json', "ALTER TABLE cert_templates ADD COLUMN layout_json JSON NULL AFTER bg_color", $log);
}

function installerDropTables(PDO $db, array &$log): void
{
    $tablesToDrop = [
        'certificates',
        'answer_logs',
        'exam_sessions',
        'questions',
        'participants',
        'projects',
        'cert_templates',
        'admins',
    ];

    $db->exec('SET FOREIGN_KEY_CHECKS = 0');
    foreach ($tablesToDrop as $table) {
        $db->exec('DROP TABLE IF EXISTS ' . installerIdentifier($table));
        $log[] = '[OK] Dropped table if existed: ' . $table;
    }
    $db->exec('SET FOREIGN_KEY_CHECKS = 1');
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    $hasDataExport = is_file($dataExportFile)
        ? '<span class="ok">Found database/data_export.sql</span>'
        : '<span class="warn">database/data_export.sql not found</span>';

    installerRender(
        'ExamCert Database Installer',
        '<h1>ExamCert Database Installer</h1>
        <p class="muted">Version: 2026-05-18 rating-scale ready. Target database: <code>' . installerEscape(DB_NAME) . '</code></p>
        <div class="panel info">
            <h2>Safety Notes</h2>
            <p>This file can change or erase database tables. Use it only on a test/staging machine or during controlled setup. Remove <code>install-database.php</code> after use.</p>
            <p>Sample data status: ' . $hasDataExport . '</p>
        </div>
        <div class="panel danger">
            <h2>Clean Install</h2>
            <p>Drop all ExamCert tables, recreate them from <code>database/schema.sql</code>, run migrations, then import <code>database/data_export.sql</code> if available.</p>
            <form method="post" onsubmit="return confirm(\'This will DROP existing ExamCert tables. Continue?\')">
                <input type="hidden" name="action" value="clean_install">
                <button class="btn btn-danger" type="submit">Drop Tables and Install Fresh</button>
            </form>
        </div>
        <div class="panel">
            <h2>Update Existing Database</h2>
            <p>Create missing tables from schema where possible, run migrations, then import <code>database/data_export.sql</code> with <code>INSERT IGNORE</code> if available. Existing data is not deleted.</p>
            <form method="post">
                <input type="hidden" name="action" value="update">
                <button class="btn btn-secondary" type="submit">Update Existing Database</button>
            </form>
        </div>'
    );
}

try {
    $action = (string) ($_POST['action'] ?? '');
    if (!in_array($action, ['clean_install', 'update'], true)) {
        throw new RuntimeException('Unknown installer action.');
    }

    $log = [];
    $db = installerConnectServer();
    installerPrepareDatabase($db, $log);

    if ($action === 'clean_install') {
        installerDropTables($db, $log);
    }

    installerRunSqlFile($db, $schemaFile, $log, $action !== 'clean_install');
    installerRunMigrations($db, $log);

    if (is_file($dataExportFile)) {
        installerRunSqlFile($db, $dataExportFile, $log, true);
    } else {
        $log[] = '[SKIP] database/data_export.sql not found';
    }

    if (!is_dir(dirname($lockFile))) {
        mkdir(dirname($lockFile), 0755, true);
    }
    file_put_contents($lockFile, 'Installed/Updated at ' . date('Y-m-d H:i:s'));

    installerRender(
        'Setup Successful',
        '<h1 class="ok">Database setup completed</h1>
        <p>The ExamCert database was installed or updated. Remove <code>install-database.php</code> from the server after testing.</p>
        <pre>' . installerEscape(implode("\n", $log)) . '</pre>
        <div class="row">
            <a class="btn btn-primary" href="' . installerEscape(BASE_URL) . '/admin">Open Admin</a>
            <a class="btn btn-secondary" href="?">Back to Installer</a>
        </div>'
    );
} catch (Throwable $e) {
    installerRender(
        'Setup Error',
        '<h1 class="err">Database setup failed</h1>
        <p>' . installerEscape($e->getMessage()) . '</p>
        <div class="row"><a class="btn btn-secondary" href="?">Back to Installer</a></div>',
        500
    );
}
