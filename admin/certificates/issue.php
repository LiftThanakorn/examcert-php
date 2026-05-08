<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Certificate.php';
requireLogin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validateCsrfToken($_POST['csrf_token'] ?? null)) {
    http_response_code(400);
    exit('Bad request.');
}
$sessionId = (int) ($_POST['session_id'] ?? 0);
$result = issueCertificateFromSession($sessionId, currentAdminId());
setFlash($result['success'] ? 'success' : 'error', $result['message']);
redirect('admin/certificates/');

