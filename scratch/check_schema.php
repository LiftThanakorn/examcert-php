<?php
require_once __DIR__ . '/../config/database.php';

try {
    // Laragon Default: root / no password
    $db = new PDO('mysql:host=127.0.0.1;dbname=examcert;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $tables = ['admins', 'cert_templates', 'projects', 'participants', 'questions', 'exam_sessions', 'answer_logs', 'certificates'];
    
    echo "--- LOCAL DATABASE SCHEMA CHECK ---\n";
    foreach ($tables as $table) {
        echo "\nTable: $table\n";
        try {
            $stmt = $db->query("DESCRIBE `$table` ");
            $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($cols as $col) {
                echo "  - {$col['Field']} ({$col['Type']})\n";
            }
        } catch (Exception $e) {
            echo "  [ERROR] " . $e->getMessage() . "\n";
        }
    }
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}
