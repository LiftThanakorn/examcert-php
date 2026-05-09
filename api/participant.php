<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/session.php';
require_once ROOT_PATH . '/models/Admin.php';
require_once ROOT_PATH . '/models/Participant.php';
require_once ROOT_PATH . '/models/ExamSession.php';

$action = (string) ($_GET['action'] ?? $_POST['action'] ?? '');

function requireParticipantApiLogin(): void
{
    if (!isLoggedIn()) {
        jsonResponse(false, 'Authentication required.', [], 401);
    }
}

if ($action === 'import') {
    requireParticipantApiLogin();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(false, 'Method not allowed.', [], 405);
    }
    
    $projectId = (int) ($_POST['project_id'] ?? 0);
    $data = $_POST['data'] ?? [];
    
    if ($projectId <= 0 || empty($data)) {
        jsonResponse(false, 'Invalid input data.', [], 400);
    }
    
    $result = importParticipants($projectId, $data, currentAdminId());
    jsonResponse(true, 'Import completed.', $result);
}

if ($action === 'list') {
    requireParticipantApiLogin();

    $projectId = (int) ($_GET['project_id'] ?? 0);
    if ($projectId <= 0) {
        jsonResponse(false, 'Invalid project ID.', [], 400);
    }
    
    $participants = getParticipantsByProject($projectId);
    $data = array_map(function($p) {
        return [
            'full_name' => trim(($p['title'] ? $p['title'] . ' ' : '') . $p['first_name'] . ' ' . $p['last_name']),
            'first_name' => $p['first_name'],
            'last_name' => $p['last_name']
        ];
    }, $participants);
    
    jsonResponse(true, 'OK', $data);
}

if ($action === 'search') {
    $projectId = (int) ($_GET['project_id'] ?? 0);
    $term = trim((string) ($_GET['term'] ?? ''));
    if ($projectId <= 0) {
        jsonResponse(false, 'Invalid project ID.', [], 400);
    }

    $participants = searchParticipantsByName($projectId, $term, 10);
    $data = array_map(function($p) {
        return [
            'full_name' => trim(($p['title'] ? $p['title'] . ' ' : '') . $p['first_name'] . ' ' . $p['last_name']),
            'first_name' => $p['first_name'],
            'last_name' => $p['last_name']
        ];
    }, $participants);

    jsonResponse(true, 'OK', $data);
}

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
