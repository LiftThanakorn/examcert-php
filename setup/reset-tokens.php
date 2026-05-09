<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.\n");
}

try {
    $db = getDB();
    $stmt = $db->query("SELECT id, access_token FROM participants");
    $rows = $stmt->fetchAll();
    $updated = 0;

    foreach ($rows as $row) {
        if (strlen($row['access_token']) < 32) {
            $newToken = bin2hex(random_bytes(32));
            $db->prepare("UPDATE participants SET access_token = ? WHERE id = ?")
               ->execute([$newToken, $row['id']]);
            $updated++;
            echo "Updated participant ID {$row['id']}\n";
        }
    }

    echo "Done. Updated: $updated tokens\n";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
