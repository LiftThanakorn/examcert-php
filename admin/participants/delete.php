<?php
declare(strict_types=1);

require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Participant.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validateCsrfToken($_POST['csrf_token'] ?? null)) {
    http_response_code(400);
    exit('Bad request.');
}

$id = (int) ($_POST['id'] ?? 0);
$participant = $id > 0 ? getParticipant($id) : null;
$projectId = $participant ? (int) $participant['project_id'] : 0;

if ($participant && deleteParticipant($id)) {
    setFlash('success', 'ลบรายชื่อผู้มีสิทธิ์สอบสำเร็จ');
} else {
    setFlash('error', 'ไม่สามารถลบรายชื่อผู้มีสิทธิ์สอบได้');
}

redirect('admin/participants/?project_id=' . $projectId);

