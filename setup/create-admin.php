<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This setup script is CLI-only.');
}

$username = $argv[1] ?? 'admin';
$password = $argv[2] ?? '';
$fullName = $argv[3] ?? 'System Administrator';
$email = $argv[4] ?? null;

if ($password === '') {
    echo "Usage: php setup/create-admin.php <username> <password> [full_name] [email]\n";
    exit(1);
}

try {
    $db = getDB();
    $stmt = $db->prepare('
        INSERT INTO admins (username, password_hash, full_name, email, role, is_active)
        VALUES (?, ?, ?, ?, ?, 1)
        ON DUPLICATE KEY UPDATE
            password_hash = VALUES(password_hash),
            full_name = VALUES(full_name),
            email = VALUES(email),
            role = VALUES(role),
            is_active = 1
    ');
    $stmt->execute([
        trim((string) $username),
        password_hash((string) $password, PASSWORD_BCRYPT),
        trim((string) $fullName),
        $email ? trim((string) $email) : null,
        'superadmin',
    ]);

    $message = 'Admin user created or updated.';
    echo $message . PHP_EOL;
    exit(0);
} catch (Throwable $e) {
    logError('Create admin failed', ['error' => $e->getMessage()]);
    echo "Create admin failed.\n";
    exit(1);
}
