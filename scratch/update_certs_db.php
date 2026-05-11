<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

function updateCertificatesTable(): void {
    $db = getDB();
    echo "Checking certificates table schema...\n";

    $stmt = $db->query("SHOW COLUMNS FROM certificates");
    $columns = array_map(fn($row) => $row['Field'], $stmt->fetchAll());

    if (!in_array('is_revoked', $columns)) {
        echo "Adding 'is_revoked' column...\n";
        $db->exec("ALTER TABLE certificates ADD COLUMN is_revoked TINYINT(1) DEFAULT 0 AFTER verify_token");
    }

    if (!in_array('revoke_reason', $columns)) {
        echo "Adding 'revoke_reason' column...\n";
        $db->exec("ALTER TABLE certificates ADD COLUMN revoke_reason TEXT NULL AFTER is_revoked");
    }

    echo "Table schema is up to date.\n";
}

try {
    updateCertificatesTable();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
