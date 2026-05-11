<?php
try {
    $db = new PDO('mysql:host=127.0.0.1;dbname=examcert;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $tables = ['admins', 'cert_templates', 'projects', 'participants', 'questions', 'exam_sessions', 'answer_logs', 'certificates'];
    
    echo "-- EXAMCERT FULL SCHEMA FROM LOCAL --\n\n";
    echo "CREATE DATABASE IF NOT EXISTS `examcert` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n";
    echo "USE `examcert`;\n\n";
    echo "SET FOREIGN_KEY_CHECKS = 0;\n\n";

    foreach ($tables as $table) {
        $stmt = $db->query("SHOW CREATE TABLE `$table` ");
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        echo $res['Create Table'] . ";\n\n";
    }
    
    echo "SET FOREIGN_KEY_CHECKS = 1;\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
