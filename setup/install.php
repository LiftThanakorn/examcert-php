<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This setup script is CLI-only.');
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/helpers.php';

$schemaFile = __DIR__ . '/../database/schema.sql';
if (!is_file($schemaFile)) {
    echo "Schema file not found.\n";
    exit(1);
}

$dsn = sprintf('mysql:host=%s;charset=%s', DB_HOST, DB_CHARSET);

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $sql = file_get_contents($schemaFile);
    if ($sql === false) {
        echo "Cannot read schema file.\n";
        exit(1);
    }

    $lines = preg_split('/\R/', $sql) ?: [];
    $sqlWithoutComments = implode("\n", array_filter($lines, static function (string $line): bool {
        return !str_starts_with(trim($line), '--');
    }));

    $statements = array_filter(array_map('trim', explode(';', $sqlWithoutComments)));
    foreach ($statements as $statement) {
        if ($statement === '') {
            continue;
        }
        $pdo->exec($statement);
    }

    echo "Database schema installed.\n";
} catch (Throwable $e) {
    logError('Install schema failed', ['error' => $e->getMessage()]);
    echo "Install failed: " . $e->getMessage() . "\n";
    exit(1);
}
