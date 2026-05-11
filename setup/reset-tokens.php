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

    $generateToken = static function () use ($db): string {
        $check = $db->prepare("SELECT id FROM participants WHERE access_token = ? LIMIT 1");

        for ($attempt = 0; $attempt < 50; $attempt++) {
            $token = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $check->execute([$token]);

            if (!$check->fetch()) {
                return $token;
            }
        }

        throw new RuntimeException("Unable to generate a unique participant access token.");
    };

    foreach ($rows as $row) {
        if (!preg_match('/^[0-9]{6}$/', (string) $row['access_token'])) {
            $newToken = $generateToken();
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
