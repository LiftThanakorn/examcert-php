<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/session.php';
require_once ROOT_PATH . '/models/Project.php';
require_once ROOT_PATH . '/models/ExamSession.php';

$action = (string) ($_GET['action'] ?? $_POST['action'] ?? '');

if ($action === 'check_time') {
    $sessionId = (int) ($_GET['session_id'] ?? $_POST['session_id'] ?? 0);
    $session = $sessionId > 0 ? getExamSession($sessionId) : null;
    if (!$session) {
        jsonResponse(false, 'Session not found.', ['should_submit' => false], 404);
    }

    $project = getProject((int) $session['project_id']);
    if (!$project) {
        jsonResponse(false, 'Project not found.', ['should_submit' => false], 404);
    }

    $projectStatus = getProjectRuntimeStatus($project);
    $expiresLeft = null;
    if (!empty($session['expires_at'])) {
        $expiresLeft = max(0, (new DateTimeImmutable((string) $session['expires_at']))->getTimestamp() - time());
    }
    $secondsLeft = $expiresLeft;
    if ($projectStatus['seconds_left'] !== null) {
        $secondsLeft = $secondsLeft === null ? (int) $projectStatus['seconds_left'] : min($secondsLeft, (int) $projectStatus['seconds_left']);
    }
    $shouldSubmit = $session['status'] !== 'in_progress'
        || $secondsLeft === 0
        || ((int) ($project['auto_submit_on_close'] ?? 1) === 1 && !$projectStatus['allowed']);

    jsonResponse(true, 'OK', [
        'session_status' => $session['status'],
        'project_status' => $projectStatus['status'],
        'seconds_left' => $secondsLeft,
        'warning' => (bool) $projectStatus['warning'],
        'should_submit' => $shouldSubmit,
        'message' => $projectStatus['message'],
    ]);
}

jsonResponse(false, 'Unknown exam API action.', [], 400);
