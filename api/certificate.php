<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/session.php';
require_once ROOT_PATH . '/models/Certificate.php';

$action = (string) ($_GET['action'] ?? $_POST['action'] ?? '');

if ($action === 'verify') {
    $token = trim((string) ($_GET['token'] ?? ''));
    $certificate = $token !== '' ? getCertificateByToken($token) : null;
    if (!$certificate) {
        jsonResponse(false, 'Certificate not found.', [], 404);
    }

    jsonResponse(true, 'OK', [
        'cert_number' => $certificate['cert_number'],
        'project_name' => $certificate['project_name'],
        'participant_name' => trim(($certificate['title'] ? $certificate['title'] . ' ' : '') . $certificate['first_name'] . ' ' . $certificate['last_name']),
        'issued_date' => $certificate['issued_date'],
        'is_revoked' => (int) $certificate['is_revoked'] === 1,
        'revoke_reason' => $certificate['revoke_reason'],
    ]);
}

jsonResponse(false, 'Unknown certificate API action.', [], 400);
