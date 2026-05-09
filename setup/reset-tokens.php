<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/models/BaseModel.php';

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.\n");
}

echo "Updating all participant tokens to new short format...\n";

try {
    $db = getDB();
    $participants = $db->query("SELECT id FROM participants")->fetchAll();
    
    $count = 0;
    $stmt = $db->prepare("UPDATE participants SET access_token = ? WHERE id = ?");
    
    foreach ($participants as $p) {
        $newToken = generateToken(6);
        $stmt->execute([$newToken, $p['id']]);
        $count++;
    }
    
    echo "Done! Updated $count participants with new short tokens.\n";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
