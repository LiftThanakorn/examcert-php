<?php
declare(strict_types=1);

// Go up one level from scratch/
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

function exportTableData(string $table): string {
    // Laragon Local
    $db = new PDO('mysql:host=127.0.0.1;dbname=examcert;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    $stmt = $db->query("SELECT * FROM `$table` ");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($rows)) return "-- No data for $table\n\n";

    $sql = "-- Data for table $table\n";
    foreach ($rows as $row) {
        $cols = array_keys($row);
        $vals = array_values($row);
        
        $placeholders = array_map(function($v) use ($db) {
            if ($v === null) return 'NULL';
            return $db->quote((string)$v);
        }, $vals);

        $sql .= "INSERT IGNORE INTO `$table` (`" . implode("`, `", $cols) . "`) VALUES (" . implode(", ", $placeholders) . ");\n";
    }
    return $sql . "\n";
}

try {
    $tables = ['admins', 'cert_templates', 'projects', 'participants', 'questions', 'exam_sessions', 'answer_logs', 'certificates'];
    $allSql = "-- ============================================\n";
    $allSql .= "-- DATA EXPORT: " . date('Y-m-d H:i:s') . "\n";
    $allSql .= "-- ============================================\n\n";
    $allSql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

    foreach ($tables as $table) {
        $allSql .= exportTableData($table);
    }

    $allSql .= "SET FOREIGN_KEY_CHECKS = 1;\n";

    $outputPath = ROOT_PATH . '/database/data_export.sql';
    file_put_contents($outputPath, $allSql);
    echo "Exported to $outputPath successfully.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
