<?php
declare(strict_types=1);

ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', (!isLocalEnvironment() || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')) ? '1' : '0');
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', '1');
ini_set('session.gc_maxlifetime', '1800');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Basic Session Fingerprinting
$currentUserAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
if (!isset($_SESSION['user_agent'])) {
    $_SESSION['user_agent'] = $currentUserAgent;
} elseif ($_SESSION['user_agent'] !== $currentUserAgent) {
    $wasAdmin = !empty($_SESSION['admin_id']);
    session_destroy();
    if ($wasAdmin) {
        header('Location: ' . BASE_URL . '/admin/login.php');
    } else {
        header('Location: ' . BASE_URL . '/');
    }
    exit;
}

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://code.jquery.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.tailwindcss.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; img-src 'self' data: blob:; connect-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com;");

