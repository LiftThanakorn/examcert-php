<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

$db = getDB();

try {
    // 1. ตรวจสอบว่าตาราง cert_templates มีคอลัมน์ elements หรือไม่
    $s = $db->query("SHOW COLUMNS FROM cert_templates LIKE 'elements'");
    $hasElements = $s->fetch();

    if (!$hasElements) {
        echo "Updating cert_templates table schema...\n";
        
        // ถ้าตารางว่างหรือไม่มีข้อมูลสำคัญ แนะนำให้สร้างใหม่ตาม Spec
        // แต่เพื่อความปลอดภัย เราจะลอง ALTER ก่อน
        $db->exec("ALTER TABLE cert_templates 
            ADD COLUMN orientation ENUM('L','P') DEFAULT 'L' AFTER name,
            ADD COLUMN width_mm DECIMAL(6,2) DEFAULT 297.00 AFTER orientation,
            ADD COLUMN height_mm DECIMAL(6,2) DEFAULT 210.00 AFTER width_mm,
            ADD COLUMN bg_type ENUM('color','image') DEFAULT 'color' AFTER height_mm,
            ADD COLUMN bg_color VARCHAR(7) DEFAULT '#FFFFFF' AFTER bg_type,
            ADD COLUMN bg_image VARCHAR(255) AFTER bg_color,
            ADD COLUMN elements JSON COMMENT 'array of {id,type,x,y,w,h,content,style}' AFTER bg_image,
            ADD COLUMN is_active TINYINT(1) DEFAULT 1 AFTER elements");
        
        echo "Table cert_templates updated successfully.\n";
    } else {
        echo "cert_templates table already has the new schema.\n";
    }

    // 2. ตรวจสอบตาราง certificates ว่ามี verify_token หรือไม่ (ขั้นตอนที่ 5)
    $s = $db->query("SHOW COLUMNS FROM certificates LIKE 'verify_token'");
    if (!$s->fetch()) {
        echo "Adding verify_token to certificates table...\n";
        $db->exec("ALTER TABLE certificates ADD COLUMN verify_token VARCHAR(64) AFTER session_id");
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    
    // ถ้าตารางยังไม่มีเลย ให้สร้างใหม่
    if (str_contains($e->getMessage(), "doesn't exist")) {
        echo "Creating cert_templates table from scratch...\n";
        $sql = "CREATE TABLE cert_templates (
            id           INT AUTO_INCREMENT PRIMARY KEY,
            name         VARCHAR(100) NOT NULL,
            orientation  ENUM('L','P') DEFAULT 'L',
            width_mm     DECIMAL(6,2) DEFAULT 297.00,
            height_mm    DECIMAL(6,2) DEFAULT 210.00,
            bg_type      ENUM('color','image') DEFAULT 'color',
            bg_color     VARCHAR(7) DEFAULT '#FFFFFF',
            bg_image     VARCHAR(255),
            elements     JSON,
            is_active    TINYINT(1) DEFAULT 1,
            created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $db->exec($sql);
        echo "Table cert_templates created.\n";
    }
}
