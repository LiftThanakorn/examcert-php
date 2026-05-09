<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/session.php';
require_once ROOT_PATH . '/models/Participant.php';
require_once ROOT_PATH . '/models/ExamSession.php';

$action = (string) ($_GET['action'] ?? $_POST['action'] ?? '');

if ($action === 'lookup') {
    $projectId = (int) ($_GET['project_id'] ?? 0);
    $token = trim((string) ($_GET['token'] ?? ''));
    $participant = $projectId > 0 && $token !== '' ? getParticipantByToken($projectId, $token) : null;
    if (!$participant) {
        jsonResponse(false, 'Participant not found.', [], 404);
    }

    jsonResponse(true, 'OK', [
        'id' => (int) $participant['id'],
        'name' => trim(($participant['title'] ? $participant['title'] . ' ' : '') . $participant['first_name'] . ' ' . $participant['last_name']),
        'organization' => $participant['organization'],
        'email' => $participant['email'],
    ]);
}

jsonResponse(false, 'Unknown participant API action.', [], 400);
