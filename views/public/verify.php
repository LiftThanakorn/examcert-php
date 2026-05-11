<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../models/Certificate.php';

$token = trim((string) ($_GET['t'] ?? $_GET['token'] ?? ''));
$cert = $token !== '' ? getCertificateData($token) : null;
$valid = $cert && (int) ($cert['is_revoked'] ?? 0) !== 1;
$mode = $token === '' ? 'initial' : ($valid ? 'valid' : ($cert ? 'revoked' : 'invalid'));
$certificate = $cert;
$results = [];

require __DIR__ . '/../certificates/verify.php';
