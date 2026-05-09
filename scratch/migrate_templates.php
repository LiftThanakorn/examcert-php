<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/config/database.php';

try {
    $db = getDB();
    
    $cols = ['show_name', 'show_course', 'show_certno'];
    foreach ($cols as $col) {
        try {
            $db->exec("ALTER TABLE cert_templates ADD COLUMN $col TINYINT(1) DEFAULT 1");
            echo "Added $col successfully\n";
        } catch (Exception $e) {
            echo "Column $col might already exist: " . $e->getMessage() . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
